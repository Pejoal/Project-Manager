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
use App\Events\ActivityLogged;

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

    return Inertia::render('Payroll/Dashboard', [
      'stats' => $stats,
      'recentTimeEntries' => $recentTimeEntries,
      'recentPayslips' => $recentPayslips,
      'payrollSettings' => PayrollSettings::current(),
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

    event(
      new ActivityLogged(auth()->user(), 'updated_payroll_settings', __('payroll.activity.settings_updated'), $settings)
    );

    return redirect()->back()->with('flash.banner', __('payroll.settings.updated_successfully'));
  }

  public function reports(Request $request)
  {
    $period = $request->input('period', 'monthly');
    $reportType = $request->input('report_type', 'summary');

    $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth();
    $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->endOfMonth();

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

    // Base query for payslips, applying all filters
    $query = Payslip::query()
      ->with(['user', 'project'])
      ->whereBetween('pay_date', [$startDate, $endDate])
      ->when($request->filled('user_ids'), function ($q) use ($request) {
        $q->whereIn('user_id', $request->user_ids);
      })
      ->when($request->filled('project_ids'), function ($q) use ($request) {
        $q->whereHas('user.timeEntries', function ($tq) use ($request) {
          $tq->whereIn('project_id', $request->project_ids);
        });
      });

    // Calculate summary statistics
    $summary = [
      'total_hours' => $query->sum('regular_hours') + $query->sum('overtime_hours'),
      'regular_hours' => $query->sum('regular_hours'),
      'overtime_hours' => $query->sum('overtime_hours'),
      'total_payroll' => $query->sum('gross_total_pay'),
      'total_tax_deductions' => $query->sum('total_tax_deductions'),
      'total_net_pay' => $query->sum('net_pay'),
      'total_employees' => $query->distinct('user_id')->count(),
      'average_hourly_rate' => $query->avg('regular_rate'),
    ];

    // Get detailed data based on report type
    $reportData = [];
    switch ($reportType) {
      case 'by_employee':
        $reportData = $query
          ->get()
          ->groupBy('user_id')
          ->map(function ($payslips, $userId) {
            $user = $payslips->first()->user;
            return [
              'user_id' => $userId,
              'user_name' => $user->name,
              'employee_id' => $user->employeeProfile->employee_id ?? null,
              'total_hours' => $payslips->sum('regular_hours') + $payslips->sum('overtime_hours'),
              'regular_hours' => $payslips->sum('regular_hours'),
              'overtime_hours' => $payslips->sum('overtime_hours'),
              'gross_pay' => $payslips->sum('gross_total_pay'),
              'deductions' => $payslips->sum('total_tax_deductions'),
              'net_pay' => $payslips->sum('net_pay'),
              'payslips_count' => $payslips->count(),
            ];
          })
          ->values();
        break;

      case 'by_project':
        $timeEntries = TimeEntry::whereBetween('start_datetime', [$startDate, $endDate])
          ->when($request->filled('user_ids'), function ($q) use ($request) {
            $q->whereIn('user_id', $request->user_ids);
          })
          ->when($request->filled('project_ids'), function ($q) use ($request) {
            $q->whereIn('project_id', $request->project_ids);
          })
          ->with('project')
          ->get();

        $reportData = $timeEntries
          ->groupBy('project_id')
          ->map(function ($entries, $projectId) {
            $project = $entries->first()->project;
            return [
              'project_id' => $projectId,
              'project_name' => $project->name ?? 'N/A',
              'total_hours' => $entries->sum('hours_worked'),
              'total_cost' => $entries->sum('gross_amount'),
              'employees_count' => $entries->unique('user_id')->count(),
              'entries_count' => $entries->count(),
            ];
          })
          ->values();
        break;

      case 'detailed':
        $reportData = $query->get();
        break;

      case 'summary':
      default:
        $reportData = $summary;
        break;
    }

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
      'canExport' => true,
    ]);
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

    event(
      new ActivityLogged(
        auth()->user(),
        'generated_time_entries',
        __('payroll.activity.time_entries_generated', ['count' => $generatedCount]),
        null
      )
    );

    return redirect()
      ->back()
      ->with('flash.banner', __('payroll.time_entries.generated_successfully', ['count' => $generatedCount]));
  }
}
