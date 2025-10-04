<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\TimeEntry;
use App\Models\EmployeeProfile;
use App\Models\PayrollSettings;
use App\Models\Payslip;
use App\Models\TaxConfiguration;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class PayrollController extends Controller
{
  public function index()
  {
    $employees = User::with([
      'employeeProfile',
      'timeEntries' => function ($query) {
        $query->where('created_at', '>=', now()->startOfMonth());
      },
    ])
      ->whereHas('employeeProfile')
      ->get()
      ->map(function ($user) {
        $currentMonthHours = $user->timeEntries->sum('hours_worked');
        $currentMonthEarnings = $user->timeEntries->sum('gross_amount');

        return [
          'id' => $user->id,
          'name' => $user->name,
          'email' => $user->email,
          'employee_id' => $user->employeeProfile->employee_id ?? null,
          'hourly_rate' => $user->employeeProfile->base_hourly_rate ?? 0,
          'current_month_hours' => $currentMonthHours,
          'current_month_earnings' => $currentMonthEarnings,
          'is_active' => $user->employeeProfile->is_active ?? false,
        ];
      });

    $stats = [
      'total_employees' => EmployeeProfile::active()->count(),
      'pending_approvals' => TimeEntry::pending()->count(),
      'current_month_payroll' => Payslip::whereMonth('pay_period_start', now()->month)
        ->whereYear('pay_period_start', now()->year)
        ->sum('gross_total_pay'),
      'pending_payslips' => Payslip::draft()->count(),
    ];

    return Inertia::render('Payroll/Index', [
      'employees' => $employees,
      'stats' => $stats,
      'payrollSettings' => PayrollSettings::current(),
    ]);
  }

  public function dashboard()
  {
    $currentMonth = now();
    $previousMonth = now()->subMonth();

    // Time entries statistics
    $currentMonthEntries = TimeEntry::whereMonth('start_datetime', $currentMonth->month)->whereYear(
      'start_datetime',
      $currentMonth->year
    );

    $previousMonthEntries = TimeEntry::whereMonth('start_datetime', $previousMonth->month)->whereYear(
      'start_datetime',
      $previousMonth->year
    );

    // Payroll statistics
    $currentMonthPayroll = Payslip::whereMonth('pay_period_start', $currentMonth->month)->whereYear(
      'pay_period_start',
      $currentMonth->year
    );

    $previousMonthPayroll = Payslip::whereMonth('pay_period_start', $previousMonth->month)->whereYear(
      'pay_period_start',
      $previousMonth->year
    );

    $stats = [
      'current_month' => [
        'total_hours' => $currentMonthEntries->sum('hours_worked'),
        'total_entries' => $currentMonthEntries->count(),
        'total_payroll' => $currentMonthPayroll->sum('gross_total_pay'),
        'total_employees' => $currentMonthEntries->distinct('user_id')->count(),
      ],
      'previous_month' => [
        'total_hours' => $previousMonthEntries->sum('hours_worked'),
        'total_entries' => $previousMonthEntries->count(),
        'total_payroll' => $previousMonthPayroll->sum('gross_total_pay'),
        'total_employees' => $previousMonthEntries->distinct('user_id')->count(),
      ],
      'pending' => [
        'time_entries' => TimeEntry::pending()->count(),
        'payslips' => Payslip::draft()->count(),
      ],
    ];

    // Recent activity
    $recentTimeEntries = TimeEntry::with(['user', 'task', 'project'])
      ->latest()
      ->limit(10)
      ->get();

    $recentPayslips = Payslip::with(['user', 'generatedBy'])
      ->latest()
      ->limit(10)
      ->get();

    $projects = Project::select('id', 'name')->orderBy('name')->get();

    return Inertia::render('Payroll/Dashboard', [
      'stats' => $stats,
      'recentTimeEntries' => $recentTimeEntries,
      'recentPayslips' => $recentPayslips,
      'payrollSettings' => PayrollSettings::current(),
      'projects' => $projects,
    ]);
  }

  public function settings()
  {
    $settings = PayrollSettings::current();
    $taxConfigurations = TaxConfiguration::orderBy('priority')->get();

    return Inertia::render('Payroll/Settings', [
      'settings' => $settings,
      'taxConfigurations' => $taxConfigurations,
    ]);
  }

  public function updateSettings(Request $request)
  {
    $request->validate([
      'company_name' => 'required|string|max:255',
      'company_address' => 'nullable|string',
      'company_tax_id' => 'nullable|string',
      'pay_period' => 'required|in:weekly,bi_weekly,monthly',
      'pay_day' => 'required|integer|min:0|max:31',
      'default_hourly_rate' => 'required|numeric|min:0',
      'working_days' => 'required|array',
      'working_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
      'standard_start_time' => 'required|date_format:H:i',
      'standard_end_time' => 'required|date_format:H:i|after:standard_start_time',
      'break_duration_minutes' => 'required|integer|min:0|max:480',
      'auto_calculate_overtime' => 'boolean',
      'require_approval_for_overtime' => 'boolean',
      'auto_generate_time_entries' => 'boolean',
      'currency' => 'required|string|size:3',
      'timezone' => 'required|string',
    ]);

    $settings = PayrollSettings::current();
    $settings->update($request->all());

    return redirect()->back()->with('flash.banner', __('payroll.settings.updated_successfully'));
  }

  public function reports(Request $request)
  {
    $request->validate([
      'period' => 'in:weekly,monthly,quarterly,yearly,custom',
      'start_date' => 'nullable|date',
      'end_date' => 'nullable|date|after_or_equal:start_date',
      'user_ids' => 'nullable|array',
      'user_ids.*' => 'exists:users,id',
      'project_ids' => 'nullable|array',
      'project_ids.*' => 'exists:projects,id',
      'report_type' => 'in:summary,detailed,by_employee,by_project',
      'export' => 'nullable|in:pdf,excel',
    ]);

    $period = $request->input('period', 'monthly');
    $reportType = $request->input('report_type', 'summary');

    $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth();
    $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->endOfMonth();

    // Set date ranges based on period
    if ($period !== 'custom') {
      switch ($period) {
        case 'weekly':
          $startDate = now()->startOfWeek();
          $endDate = now()->endOfWeek();
          break;
        case 'quarterly':
          $startDate = now()->startOfQuarter();
          $endDate = now()->endOfQuarter();
          break;
        case 'yearly':
          $startDate = now()->startOfYear();
          $endDate = now()->endOfYear();
          break;
        case 'monthly':
        default:
          $startDate = now()->startOfMonth();
          $endDate = now()->endOfMonth();
          break;
      }
    }

    // Base query for time entries with filters
    $timeEntriesQuery = TimeEntry::query()
      ->with(['user.employeeProfile', 'task', 'project'])
      ->whereBetween('start_datetime', [$startDate, $endDate])
      ->when($request->filled('user_ids'), function ($q) use ($request) {
        $q->whereIn('user_id', $request->user_ids);
      })
      ->when($request->filled('project_ids'), function ($q) use ($request) {
        $q->whereIn('project_id', $request->project_ids);
      });

    // Calculate summary statistics
    $timeEntries = $timeEntriesQuery->get();

    $summary = [
      'total_hours' => $timeEntries->sum('hours_worked'),
      'regular_hours' => $timeEntries->where('hours_worked', '<=', 8)->sum('hours_worked'),
      'overtime_hours' => $timeEntries->where('hours_worked', '>', 8)->sum(function ($entry) {
        return max(0, $entry->hours_worked - 8);
      }),
      'total_payroll' => $timeEntries->sum('gross_amount'),
      'total_entries' => $timeEntries->count(),
      'total_employees' => $timeEntries->unique('user_id')->count(),
      'average_hourly_rate' => $timeEntries->avg('hourly_rate'),
    ];

    // Get detailed data based on report type
    $reportData = [];

    switch ($reportType) {
      case 'by_employee':
        $reportData = $timeEntries
          ->groupBy('user_id')
          ->map(function ($entries, $userId) {
            $user = $entries->first()->user;
            $totalHours = $entries->sum('hours_worked');
            $grossPay = $entries->sum('gross_amount');

            // Calculate tax deductions (simplified for demo)
            $taxDeductions = $grossPay * 0.15; // 15% tax rate

            return [
              'employee_id' => $userId,
              'employee_name' => $user->name,
              'employee_code' => $user->employeeProfile->employee_id ?? 'N/A',
              'total_hours' => round($totalHours, 2),
              'regular_hours' => round($entries->where('hours_worked', '<=', 8)->sum('hours_worked'), 2),
              'overtime_hours' => round(
                $entries->sum(function ($entry) {
                  return max(0, $entry->hours_worked - 8);
                }),
                2
              ),
              'gross_pay' => round($grossPay, 2),
              'total_deductions' => round($taxDeductions, 2),
              'net_pay' => round($grossPay - $taxDeductions, 2),
              'entries_count' => $entries->count(),
              'average_hourly_rate' => round($entries->avg('hourly_rate'), 2),
            ];
          })
          ->values();
        break;

      case 'by_project':
        $reportData = $timeEntries
          ->groupBy('project_id')
          ->map(function ($entries, $projectId) {
            $project = $entries->first()->project;
            $totalHours = $entries->sum('hours_worked');
            $totalCost = $entries->sum('gross_amount');

            return [
              'project_id' => $projectId,
              'project_name' => $project->name ?? 'No Project',
              'total_hours' => round($totalHours, 2),
              'gross_pay' => round($totalCost, 2),
              'total_deductions' => round($totalCost * 0.15, 2), // 15% tax
              'net_pay' => round($totalCost * 0.85, 2),
              'employees_count' => $entries->unique('user_id')->count(),
              'entries_count' => $entries->count(),
              'average_hourly_rate' => round($entries->avg('hourly_rate'), 2),
            ];
          })
          ->values();
        break;

      case 'detailed':
        $reportData = $timeEntries
          ->map(function ($entry) {
            $taxDeduction = $entry->gross_amount * 0.15;
            return [
              'id' => $entry->id,
              'employee_name' => $entry->user->name,
              'project_name' => $entry->project->name ?? 'No Project',
              'task_name' => $entry->task->name ?? 'No Task',
              'date' => $entry->start_datetime->format('Y-m-d'),
              'hours_worked' => round($entry->hours_worked, 2),
              'hourly_rate' => round($entry->hourly_rate, 2),
              'gross_amount' => round($entry->gross_amount, 2),
              'deductions' => round($taxDeduction, 2),
              'net_pay' => round($entry->gross_amount - $taxDeduction, 2),
              'description' => $entry->description,
              'is_approved' => $entry->is_approved,
            ];
          })
          ->values();
        break;

      case 'summary':
      default:
        // For summary, we just use the summary data
        $reportData = collect([
          [
            'metric' => 'Total Hours',
            'value' => $summary['total_hours'],
            'formatted_value' => number_format($summary['total_hours'], 2) . ' hours',
          ],
          [
            'metric' => 'Total Payroll',
            'value' => $summary['total_payroll'],
            'formatted_value' => '€' . number_format($summary['total_payroll'], 2),
          ],
          [
            'metric' => 'Total Employees',
            'value' => $summary['total_employees'],
            'formatted_value' => $summary['total_employees'] . ' employees',
          ],
          [
            'metric' => 'Average Hourly Rate',
            'value' => $summary['average_hourly_rate'],
            'formatted_value' => '€' . number_format($summary['average_hourly_rate'], 2) . '/hour',
          ],
        ]);
        break;
    }

    // Handle export requests
    if ($request->filled('export')) {
      return $this->exportReport($request->export, $reportData, $summary, $startDate, $endDate, $reportType);
    }

    // Get users and projects for filters
    $users = User::whereHas('employeeProfile')->select('id', 'name')->get();
    $projects = Project::select('id', 'name')->get();

    return Inertia::render('Payroll/Reports', [
      'users' => $users,
      'projects' => $projects,
      'reportData' => [
        'summary' => $summary,
        'data' => $reportData,
      ],
      'filters' => [
        'period' => $period,
        'start_date' => $startDate->toDateString(),
        'end_date' => $endDate->toDateString(),
        'user_ids' => $request->input('user_ids', []),
        'project_ids' => $request->input('project_ids', []),
        'report_type' => $reportType,
      ],
      'canExport' => auth()->user()->can('export_payroll_reports'),
    ]);
  }

  /**
   * Export report data in specified format
   */
  private function exportReport($format, $data, $summary, $startDate, $endDate, $reportType)
  {
    $filename = "payroll_report_{$reportType}_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}";

    if ($format === 'pdf') {
      // PDF export logic would go here
      // For now, return a simple response
      return response()->json([
        'message' => 'PDF export functionality would be implemented here',
        'data' => $data,
        'summary' => $summary,
      ]);
    } elseif ($format === 'excel') {
      // Excel export logic would go here
      // For now, return a simple response
      return response()->json([
        'message' => 'Excel export functionality would be implemented here',
        'data' => $data,
        'summary' => $summary,
      ]);
    }

    return response()->json(['error' => 'Invalid export format'], 400);
  }

  public function generateTimeEntries()
  {
    $settings = PayrollSettings::current();

    if (!$settings->auto_generate_time_entries) {
      return redirect()
        ->back()
        ->with('flash.banner', __('payroll.time_entries.auto_generation_disabled'))
        ->with('flash.bannerStyle', 'error');
    }

    // Get tasks with start_datetime and end_datetime but no corresponding time entries
    $tasksWithoutEntries = \App\Models\Task::whereNotNull('start_datetime')
      ->whereNotNull('end_datetime')
      ->whereDoesntHave('timeEntries')
      ->with(['assignedTo', 'project'])
      ->get();

    $generatedCount = 0;

    foreach ($tasksWithoutEntries as $task) {
      foreach ($task->assignedTo as $user) {
        if (!$user->employeeProfile) {
          continue;
        }

        TimeEntry::create([
          'user_id' => $user->id,
          'task_id' => $task->id,
          'project_id' => $task->project_id,
          'start_datetime' => $task->start_datetime,
          'end_datetime' => $task->end_datetime,
          'hourly_rate' => $user->employeeProfile->base_hourly_rate,
          'description' => __('payroll.time_entries.auto_generated_from_task', ['task' => $task->name]),
        ]);

        $generatedCount++;
      }
    }

    return redirect()
      ->back()
      ->with('flash.banner', __('payroll.time_entries.generated_successfully', ['count' => $generatedCount]));
  }
}
