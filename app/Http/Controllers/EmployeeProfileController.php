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
        $query->latest()->limit(10);
      },
      'payslips' => function ($query) {
        $query->latest()->limit(5);
      },
    ]);

    // Calculate statistics
    $currentMonth = now();
    $stats = [
      'current_month_hours' => $employeeProfile
        ->timeEntries()
        ->whereMonth('start_datetime', $currentMonth->month)
        ->whereYear('start_datetime', $currentMonth->year)
        ->sum('hours_worked'),
      'current_month_earnings' => $employeeProfile
        ->timeEntries()
        ->whereMonth('start_datetime', $currentMonth->month)
        ->whereYear('start_datetime', $currentMonth->year)
        ->sum('gross_amount'),
      'total_hours' => $employeeProfile->timeEntries()->sum('hours_worked'),
      'total_earnings' => $employeeProfile->timeEntries()->sum('gross_amount'),
      'pending_entries' => $employeeProfile->timeEntries()->pending()->count(),
    ];

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

    event(new ActivityLogged(auth()->user(), 'bulk_updated_employee_profiles', $message, null));

    return redirect()->back()->with('flash.banner', $message);
  }
}
