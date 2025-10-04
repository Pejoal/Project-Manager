<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmployeeProfile;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeProfileController extends Controller
{
  /**
   * Apply filters to the query
   */
  private function applyFilters($query, Request $request)
  {
    $request->validate([
      'search' => 'nullable|string|max:255',
      'status' => 'nullable|string|in:active,inactive',
      'payment_method' => 'nullable|string|in:bank_transfer,cash,check',
      'per_page' => 'nullable|integer|min:1|max:250',
      'sort_by' => 'nullable|string|in:created_at,hire_date,base_hourly_rate',
      'sort_direction' => 'nullable|string|in:asc,desc',
    ]);

    // Apply sorting with default fallback
    $sortBy = $request->input('sort_by', 'created_at');
    $sortDirection = $request->input('sort_direction', 'desc');
    $query->orderBy($sortBy, $sortDirection);

    // Apply search
    if ($request->filled('search')) {
      $search = $request->search;
      $query
        ->whereHas('user', function ($q) use ($search) {
          $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
        })
        ->orWhere('employee_id', 'like', "%{$search}%");
    }

    // Apply status filter
    if ($request->filled('status')) {
      $isActive = $request->status === 'active';
      $query->where('is_active', $isActive);
    }

    // Apply payment method filter
    if ($request->filled('payment_method')) {
      $query->where('payment_method', $request->payment_method);
    }
  }

  public function index(Request $request)
  {
    $query = EmployeeProfile::with(['user']);

    // Apply filters
    $this->applyFilters($query, $request);

    // Get pagination settings
    $perPage = $request->input('per_page', 15);
    $employeeProfiles = $query->paginate($perPage)->appends($request->except('page'));

    // Get filter options
    $users = User::doesntHave('employeeProfile')->select('id', 'name')->orderBy('name')->get();

    return Inertia::render('EmployeeProfiles/Index', [
      'employeeProfiles' => $employeeProfiles,
      'filters' => $request->only(['search', 'status', 'payment_method', 'per_page', 'sort_by', 'sort_direction']),
      'users' => $users,
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
          ->with(['task', 'project'])
          ->latest()
          ->limit(10);
      },
    ]);

    // Calculate statistics
    $stats = [
      'total_hours' => $employeeProfile->user->timeEntries()->sum('hours_worked'),
      'current_month_hours' => $employeeProfile->user
        ->timeEntries()
        ->whereMonth('start_datetime', now()->month)
        ->whereYear('start_datetime', now()->year)
        ->sum('hours_worked'),
      'current_month_earnings' => $employeeProfile->user
        ->timeEntries()
        ->whereMonth('start_datetime', now()->month)
        ->whereYear('start_datetime', now()->year)
        ->sum('gross_amount'),
      'total_earnings' => $employeeProfile->user->timeEntries()->sum('gross_amount'),
      'pending_entries' => $employeeProfile->user->timeEntries()->where('is_approved', false)->count(),
    ];

    $projects = Project::select('id', 'name')->orderBy('name')->get();

    return Inertia::render('EmployeeProfiles/Show', [
      'profile' => $employeeProfile,
      'stats' => $stats,
      'projects' => $projects,
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

    return redirect()
      ->route('employee-profiles.index')
      ->with('flash.banner', __('payroll.employee_profiles.deleted_successfully'));
  }

  public function activate(EmployeeProfile $employeeProfile)
  {
    $employeeProfile->update(['is_active' => true]);

    return redirect()->back()->with('flash.banner', __('payroll.employee_profiles.activated_successfully'));
  }

  public function deactivate(EmployeeProfile $employeeProfile)
  {
    $employeeProfile->update(['is_active' => false]);

    return redirect()->back()->with('flash.banner', __('payroll.employee_profiles.deactivated_successfully'));
  }

  /**
   * Handle bulk operations on employee profiles
   */
  public function bulkUpdate(Request $request)
  {
    $request->validate([
      'profile_ids' => 'required|array',
      'profile_ids.*' => 'exists:employee_profiles,id',
      'action' => 'required|in:activate,deactivate,update_rate',
      'hourly_rate' => 'required_if:action,update_rate|numeric|min:0',
    ]);

    $profiles = EmployeeProfile::whereIn('id', $request->profile_ids)->get();
    $message = '';

    switch ($request->action) {
      case 'activate':
        $profiles->each(function ($profile) {
          $profile->update(['is_active' => true]);
        });
        $message = trans('payroll.employee_profiles.bulk_activated', ['count' => $profiles->count()]);
        break;

      case 'deactivate':
        $profiles->each(function ($profile) {
          $profile->update(['is_active' => false]);
        });
        $message = trans('payroll.employee_profiles.bulk_deactivated', ['count' => $profiles->count()]);
        break;

      case 'update_rate':
        $profiles->each(function ($profile) use ($request) {
          $profile->update(['base_hourly_rate' => $request->hourly_rate]);
        });
        $message = trans('payroll.employee_profiles.bulk_rate_updated', [
          'count' => $profiles->count(),
          'rate' => $request->hourly_rate,
        ]);
        break;

      default:
        return redirect()
          ->back()
          ->withErrors(['action' => 'Invalid action']);
    }

    return redirect()->back()->with('flash.banner', $message);
  }
}
