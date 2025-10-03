<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmployeeProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\ActivityLogged;

class EmployeeProfileController extends Controller
{
  public function index(Request $request)
  {
    $query = EmployeeProfile::with('user');

    if ($request->filled('search')) {
      $search = $request->search;
      $query
        ->whereHas('user', function ($q) use ($search) {
          $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
        })
        ->orWhere('employee_id', 'like', "%{$search}%");
    }

    if ($request->filled('status')) {
      if ($request->status === 'active') {
        $query->active();
      } elseif ($request->status === 'inactive') {
        $query->inactive();
      }
    }

    $employeeProfiles = $query->latest()->paginate(15);

    return Inertia::render('EmployeeProfiles/Index', [
      'employeeProfiles' => $employeeProfiles,
      'filters' => $request->only(['search', 'status']),
    ]);
  }

  public function create()
  {
    $usersWithoutProfiles = User::whereDoesntHave('employeeProfile')->select('id', 'name', 'email')->get();

    return Inertia::render('EmployeeProfiles/Create', [
      'users' => $usersWithoutProfiles,
    ]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'user_id' => 'required|exists:users,id|unique:employee_profiles,user_id',
      'employee_id' => 'nullable|string|max:50|unique:employee_profiles,employee_id',
      'base_hourly_rate' => 'required|numeric|min:0|max:999.99',
      'overtime_rate_multiplier' => 'required|numeric|min:1|max:5',
      'standard_hours_per_day' => 'required|integer|min:1|max:24',
      'standard_hours_per_week' => 'required|integer|min:1|max:168',
      'payment_method' => 'required|in:bank_transfer,cash,check',
      'bank_account_number' => 'nullable|string|max:50',
      'bank_name' => 'nullable|string|max:100',
      'tax_id' => 'nullable|string|max:50',
      'tax_exemptions' => 'nullable|array',
      'hire_date' => 'required|date|before_or_equal:today',
      'termination_date' => 'nullable|date|after:hire_date',
    ]);

    $profile = EmployeeProfile::create($request->all());

    event(
      new ActivityLogged(
        auth()->user(),
        'created_employee_profile',
        __('payroll.activity.employee_profile_created', ['user' => $profile->user->name]),
        $profile
      )
    );

    return redirect()
      ->route('employee-profiles.index')
      ->with('flash.banner', __('payroll.employee_profiles.created_successfully'));
  }

  public function show(EmployeeProfile $employeeProfile)
  {
    $employeeProfile->load([
      'user',
      'timeEntries' => function ($query) {
        $query
          ->with(['task', 'project', 'approvedBy'])
          ->latest('start_datetime')
          ->limit(10);
      },
      'payslips' => function ($query) {
        $query
          ->with(['generatedBy', 'approvedBy'])
          ->latest()
          ->limit(5);
      },
    ]);

    // Calculate comprehensive statistics
    $currentMonth = now();
    $currentYear = now();

    // Current month statistics
    $currentMonthEntries = $employeeProfile
      ->timeEntries()
      ->whereMonth('start_datetime', $currentMonth->month)
      ->whereYear('start_datetime', $currentMonth->year);

    // Current year statistics
    $currentYearEntries = $employeeProfile->timeEntries()->whereYear('start_datetime', $currentYear->year);

    // Last 30 days statistics
    $last30DaysEntries = $employeeProfile->timeEntries()->where('start_datetime', '>=', now()->subDays(30));

    $stats = [
      // Current month
      'current_month_hours' => $currentMonthEntries->sum('hours_worked'),
      'current_month_earnings' => $currentMonthEntries->sum('gross_amount'),
      'current_month_entries' => $currentMonthEntries->count(),
      'current_month_approved' => $currentMonthEntries->where('is_approved', true)->count(),

      // Year to date
      'year_to_date_hours' => $currentYearEntries->sum('hours_worked'),
      'year_to_date_earnings' => $currentYearEntries->sum('gross_amount'),

      // Last 30 days
      'last_30_days_hours' => $last30DaysEntries->sum('hours_worked'),
      'last_30_days_earnings' => $last30DaysEntries->sum('gross_amount'),

      // All time totals
      'total_hours' => $employeeProfile->timeEntries()->sum('hours_worked'),
      'total_earnings' => $employeeProfile->timeEntries()->sum('gross_amount'),
      'total_entries' => $employeeProfile->timeEntries()->count(),

      // Pending items
      'pending_entries' => $employeeProfile->timeEntries()->pending()->count(),
      'pending_hours' => $employeeProfile->timeEntries()->pending()->sum('hours_worked'),
      'pending_earnings' => $employeeProfile->timeEntries()->pending()->sum('gross_amount'),

      // Averages
      'average_hours_per_entry' =>
        $employeeProfile->timeEntries()->count() > 0
          ? round($employeeProfile->timeEntries()->sum('hours_worked') / $employeeProfile->timeEntries()->count(), 2)
          : 0,
      'average_daily_rate' => round($employeeProfile->base_hourly_rate * $employeeProfile->standard_hours_per_day, 2),
      'monthly_potential_earnings' => round(
        $employeeProfile->base_hourly_rate * $employeeProfile->standard_hours_per_week * 4.33,
        2
      ),

      // Performance metrics
      'approval_rate' =>
        $employeeProfile->timeEntries()->count() > 0
          ? round(
            ($employeeProfile->timeEntries()->approved()->count() / $employeeProfile->timeEntries()->count()) * 100,
            1
          )
          : 0,

      // Recent activity indicators
      'last_entry_date' => $employeeProfile->timeEntries()->latest('start_datetime')->value('start_datetime'),
      'most_active_month' => $employeeProfile
        ->timeEntries()
        ->selectRaw('YEAR(start_datetime) as year, MONTH(start_datetime) as month, SUM(hours_worked) as total_hours')
        ->groupBy('year', 'month')
        ->orderBy('total_hours', 'desc')
        ->first(),
    ];

    // Add overtime analysis
    $overtimeEntries = $employeeProfile
      ->timeEntries()
      ->where('hours_worked', '>', $employeeProfile->standard_hours_per_day)
      ->get();

    $stats['overtime_entries'] = $overtimeEntries->count();
    $stats['total_overtime_hours'] = $overtimeEntries->sum(function ($entry) use ($employeeProfile) {
      return max(0, $entry->hours_worked - $employeeProfile->standard_hours_per_day);
    });
    $stats['overtime_earnings'] = $overtimeEntries->sum(function ($entry) use ($employeeProfile) {
      $overtimeHours = max(0, $entry->hours_worked - $employeeProfile->standard_hours_per_day);
      $regularHours = min($entry->hours_worked, $employeeProfile->standard_hours_per_day);
      $regularPay = $regularHours * $employeeProfile->base_hourly_rate;
      $overtimePay = $overtimeHours * ($employeeProfile->base_hourly_rate * $employeeProfile->overtime_rate_multiplier);
      return $overtimePay;
    });

    // Project distribution analysis
    $projectStats = $employeeProfile
      ->timeEntries()
      ->with('project')
      ->selectRaw(
        'project_id, SUM(hours_worked) as total_hours, SUM(gross_amount) as total_earnings, COUNT(*) as entry_count'
      )
      ->groupBy('project_id')
      ->orderBy('total_hours', 'desc')
      ->limit(5)
      ->get()
      ->map(function ($stat) {
        return [
          'project_name' => $stat->project->name ?? 'Unknown Project',
          'project_slug' => $stat->project->slug ?? '',
          'hours' => $stat->total_hours,
          'earnings' => $stat->total_earnings,
          'entries' => $stat->entry_count,
        ];
      });

    $stats['top_projects'] = $projectStats;

    return Inertia::render('EmployeeProfiles/Show', [
      'profile' => $employeeProfile,
      'stats' => $stats,
    ]);
  }

  public function edit(EmployeeProfile $employeeProfile)
  {
    $employeeProfile->load('user');

    return Inertia::render('EmployeeProfiles/Edit', [
      'profile' => $employeeProfile,
    ]);
  }

  public function update(Request $request, EmployeeProfile $employeeProfile)
  {
    $request->validate([
      'employee_id' => 'nullable|string|max:50|unique:employee_profiles,employee_id,' . $employeeProfile->id,
      'base_hourly_rate' => 'required|numeric|min:0|max:999.99',
      'overtime_rate_multiplier' => 'required|numeric|min:1|max:5',
      'standard_hours_per_day' => 'required|integer|min:1|max:24',
      'standard_hours_per_week' => 'required|integer|min:1|max:168',
      'payment_method' => 'required|in:bank_transfer,cash,check',
      'bank_account_number' => 'nullable|string|max:50',
      'bank_name' => 'nullable|string|max:100',
      'tax_id' => 'nullable|string|max:50',
      'tax_exemptions' => 'nullable|array',
      'is_active' => 'boolean',
      'hire_date' => 'required|date|before_or_equal:today',
      'termination_date' => 'nullable|date|after:hire_date',
    ]);

    $employeeProfile->update($request->all());

    event(
      new ActivityLogged(
        auth()->user(),
        'updated_employee_profile',
        __('payroll.activity.employee_profile_updated', ['user' => $employeeProfile->user->name]),
        $employeeProfile
      )
    );

    return redirect()
      ->route('employee-profiles.index')
      ->with('flash.banner', __('payroll.employee_profiles.updated_successfully'));
  }

  public function destroy(EmployeeProfile $employeeProfile)
  {
    // Check if profile has associated data
    if ($employeeProfile->timeEntries()->exists() || $employeeProfile->payslips()->exists()) {
      return redirect()
        ->back()
        ->withErrors([
          'has_data' => __('payroll.employee_profiles.cannot_delete_with_data'),
        ]);
    }

    $userName = $employeeProfile->user->name;
    $employeeProfile->delete();

    event(
      new ActivityLogged(
        auth()->user(),
        'deleted_employee_profile',
        __('payroll.activity.employee_profile_deleted', ['user' => $userName]),
        $employeeProfile
      )
    );

    return redirect()
      ->route('employee-profiles.index')
      ->with('flash.banner', __('payroll.employee_profiles.deleted_successfully'));
  }

  public function activate(EmployeeProfile $employeeProfile)
  {
    $employeeProfile->update(['is_active' => true]);

    event(
      new ActivityLogged(
        auth()->user(),
        'activated_employee_profile',
        __('payroll.activity.employee_profile_activated', ['user' => $employeeProfile->user->name]),
        $employeeProfile
      )
    );

    return redirect()->back()->with('flash.banner', __('payroll.employee_profiles.activated_successfully'));
  }

  public function deactivate(EmployeeProfile $employeeProfile)
  {
    $employeeProfile->update(['is_active' => false]);

    event(
      new ActivityLogged(
        auth()->user(),
        'deactivated_employee_profile',
        __('payroll.activity.employee_profile_deactivated', ['user' => $employeeProfile->user->name]),
        $employeeProfile
      )
    );

    return redirect()->back()->with('flash.banner', __('payroll.employee_profiles.deactivated_successfully'));
  }

  public function bulkUpdate(Request $request)
  {
    $request->validate([
      'profile_ids' => 'required|array',
      'profile_ids.*' => 'exists:employee_profiles,id',
      'action' => 'required|in:activate,deactivate,update_rate',
      'hourly_rate' => 'required_if:action,update_rate|numeric|min:0|max:999.99',
    ]);

    $profiles = EmployeeProfile::whereIn('id', $request->profile_ids)->get();

    switch ($request->action) {
      case 'activate':
        $profiles->each(function ($profile) {
          $profile->update(['is_active' => true]);
        });
        $message = __('payroll.employee_profiles.bulk_activated', ['count' => $profiles->count()]);
        break;

      case 'deactivate':
        $profiles->each(function ($profile) {
          $profile->update(['is_active' => false]);
        });
        $message = __('payroll.employee_profiles.bulk_deactivated', ['count' => $profiles->count()]);
        break;

      case 'update_rate':
        $profiles->each(function ($profile) use ($request) {
          $profile->update(['base_hourly_rate' => $request->hourly_rate]);
        });
        $message = __('payroll.employee_profiles.bulk_rate_updated', ['count' => $profiles->count()]);
        break;

      default:
        return redirect()
          ->back()
          ->withErrors(['action' => 'Invalid action']);
    }

    // event(new ActivityLogged(auth()->user(), 'bulk_updated_employee_profiles', $message, null));

    return redirect()->back()->with('flash.banner', $message);
  }
}
