<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\ActivityLogged;
use Carbon\Carbon;

class TimeEntryController extends Controller
{
  public function index(Request $request)
  {
    $query = TimeEntry::with(['user', 'task', 'project', 'approvedBy'])->when(
      !auth()->user()->hasRole('admin'),
      function ($q) {
        return $q->where('user_id', auth()->id());
      }
    );

    // Apply filters
    if ($request->filled('user_id') && auth()->user()->hasRole('admin')) {
      $query->where('user_id', $request->user_id);
    }

    if ($request->filled('project_id')) {
      $query->where('project_id', $request->project_id);
    }

    if ($request->filled('status')) {
      if ($request->status === 'pending') {
        $query->where('is_approved', false);
      } elseif ($request->status === 'approved') {
        $query->where('is_approved', true);
      }
    }

    if ($request->filled('date_from')) {
      $query->whereDate('start_datetime', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
      $query->whereDate('end_datetime', '<=', $request->date_to);
    }

    $timeEntries = $query->latest('start_datetime')->paginate(15);

    // Get filter options for admin
    $users = auth()->user()->hasRole('admin')
      ? User::whereHas('employeeProfile')->select('id', 'name')->get()
      : collect();

    $projects = Project::select('id', 'name')->get();

    return Inertia::render('TimeEntries/Index', [
      'timeEntries' => $timeEntries,
      'filters' => $request->only(['user_id', 'project_id', 'status', 'date_from', 'date_to']),
      'users' => $users,
      'projects' => $projects,
      'canManageAll' => auth()->user()->hasRole('admin'),
    ]);
  }

  public function create(Request $request)
  {
    $task = null;
    if ($request->filled('task_id')) {
      $task = Task::with(['project', 'assignedTo'])->findOrFail($request->task_id);

      // Check if user is assigned to this task or is admin
      if (!auth()->user()->hasRole('admin') && !$task->assignedTo->contains(auth()->user())) {
        abort(403, __('payroll.time_entries.not_assigned_to_task'));
      }
    }

    $projects = Project::select('id', 'name')->get();
    $tasks = $task ? collect([$task]) : collect();

    return Inertia::render('TimeEntries/Create', [
      'task' => $task,
      'projects' => $projects,
      'tasks' => $tasks,
    ]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'task_id' => 'required|exists:tasks,id',
      'start_datetime' => 'required|date',
      'end_datetime' => 'required|date|after:start_datetime',
      'description' => 'nullable|string|max:1000',
    ]);

    $task = Task::with(['project', 'assignedTo'])->findOrFail($request->task_id);

    // Check if user is assigned to this task or is admin
    if (!auth()->user()->hasRole('admin') && !$task->assignedTo->contains(auth()->user())) {
      abort(403, __('payroll.time_entries.not_assigned_to_task'));
    }

    // Get user's hourly rate
    $employeeProfile = auth()->user()->employeeProfile;
    if (!$employeeProfile) {
      return redirect()
        ->back()
        ->withErrors([
          'employee_profile' => __('payroll.time_entries.no_employee_profile'),
        ]);
    }

    // Check for overlapping time entries
    $overlapping = TimeEntry::where('user_id', auth()->id())
      ->where('id', '!=', $request->get('id', 0))
      ->where(function ($query) use ($request) {
        $query
          ->whereBetween('start_datetime', [$request->start_datetime, $request->end_datetime])
          ->orWhereBetween('end_datetime', [$request->start_datetime, $request->end_datetime])
          ->orWhere(function ($q) use ($request) {
            $q->where('start_datetime', '<=', $request->start_datetime)->where(
              'end_datetime',
              '>=',
              $request->end_datetime
            );
          });
      })
      ->exists();

    if ($overlapping) {
      return redirect()
        ->back()
        ->withErrors([
          'time_overlap' => __('payroll.time_entries.overlapping_time'),
        ]);
    }

    $timeEntry = TimeEntry::create([
      'user_id' => auth()->id(),
      'task_id' => $task->id,
      'project_id' => $task->project_id,
      'start_datetime' => $request->start_datetime,
      'end_datetime' => $request->end_datetime,
      'hourly_rate' => $employeeProfile->base_hourly_rate,
      'description' => $request->description,
    ]);

    event(
      new ActivityLogged(
        auth()->user(),
        'created_time_entry',
        __('payroll.activity.time_entry_created', ['hours' => $timeEntry->hours_worked]),
        $timeEntry
      )
    );

    return redirect()
      ->route('time-entries.index')
      ->with('flash.banner', __('payroll.time_entries.created_successfully'));
  }

  public function show(TimeEntry $timeEntry)
  {
    // Check permissions
    if (!auth()->user()->hasRole('admin') && $timeEntry->user_id !== auth()->id()) {
      abort(403, __('payroll.time_entries.unauthorized_access'));
    }

    $timeEntry->load(['user', 'task', 'project', 'approvedBy']);

    return Inertia::render('TimeEntries/Show', [
      'timeEntry' => $timeEntry,
    ]);
  }

  public function edit(TimeEntry $timeEntry)
  {
    // Check permissions - only owner can edit unless admin
    if (!auth()->user()->hasRole('admin') && $timeEntry->user_id !== auth()->id()) {
      abort(403, __('payroll.time_entries.unauthorized_access'));
    }

    // Cannot edit approved entries unless admin
    if ($timeEntry->is_approved && !auth()->user()->hasRole('admin')) {
      abort(403, __('payroll.time_entries.cannot_edit_approved'));
    }

    $timeEntry->load(['task.project']);
    $projects = Project::select('id', 'name')->get();

    return Inertia::render('TimeEntries/Edit', [
      'timeEntry' => $timeEntry,
      'projects' => $projects,
    ]);
  }

  public function update(Request $request, TimeEntry $timeEntry)
  {
    // Check permissions
    if (!auth()->user()->hasRole('admin') && $timeEntry->user_id !== auth()->id()) {
      abort(403, __('payroll.time_entries.unauthorized_access'));
    }

    // Cannot edit approved entries unless admin
    if ($timeEntry->is_approved && !auth()->user()->hasRole('admin')) {
      abort(403, __('payroll.time_entries.cannot_edit_approved'));
    }

    $request->validate([
      'start_datetime' => 'required|date',
      'end_datetime' => 'required|date|after:start_datetime',
      'description' => 'nullable|string|max:1000',
    ]);

    // Check for overlapping time entries
    $overlapping = TimeEntry::where('user_id', $timeEntry->user_id)
      ->where('id', '!=', $timeEntry->id)
      ->where(function ($query) use ($request) {
        $query
          ->whereBetween('start_datetime', [$request->start_datetime, $request->end_datetime])
          ->orWhereBetween('end_datetime', [$request->start_datetime, $request->end_datetime])
          ->orWhere(function ($q) use ($request) {
            $q->where('start_datetime', '<=', $request->start_datetime)->where(
              'end_datetime',
              '>=',
              $request->end_datetime
            );
          });
      })
      ->exists();

    if ($overlapping) {
      return redirect()
        ->back()
        ->withErrors([
          'time_overlap' => __('payroll.time_entries.overlapping_time'),
        ]);
    }

    $timeEntry->update([
      'start_datetime' => $request->start_datetime,
      'end_datetime' => $request->end_datetime,
      'description' => $request->description,
    ]);

    event(
      new ActivityLogged(
        auth()->user(),
        'updated_time_entry',
        __('payroll.activity.time_entry_updated', ['hours' => $timeEntry->hours_worked]),
        $timeEntry
      )
    );

    return redirect()
      ->route('time-entries.index')
      ->with('flash.banner', __('payroll.time_entries.updated_successfully'));
  }

  public function destroy(TimeEntry $timeEntry)
  {
    // Check permissions
    if (!auth()->user()->hasRole('admin') && $timeEntry->user_id !== auth()->id()) {
      abort(403, __('payroll.time_entries.unauthorized_access'));
    }

    // Cannot delete approved entries unless admin
    if ($timeEntry->is_approved && !auth()->user()->hasRole('admin')) {
      abort(403, __('payroll.time_entries.cannot_delete_approved'));
    }

    $timeEntry->delete();

    event(
      new ActivityLogged(auth()->user(), 'deleted_time_entry', __('payroll.activity.time_entry_deleted'), $timeEntry)
    );

    return redirect()
      ->route('time-entries.index')
      ->with('flash.banner', __('payroll.time_entries.deleted_successfully'));
  }

  public function approve(TimeEntry $timeEntry)
  {
    // Only admins can approve time entries
    if (!auth()->user()->hasRole('admin')) {
      abort(403, __('payroll.time_entries.unauthorized_approval'));
    }

    if ($timeEntry->is_approved) {
      return redirect()
        ->back()
        ->withErrors([
          'already_approved' => __('payroll.time_entries.already_approved'),
        ]);
    }

    $timeEntry->update([
      'is_approved' => true,
      'approved_by' => auth()->id(),
      'approved_at' => now(),
    ]);

    event(
      new ActivityLogged(
        auth()->user(),
        'approved_time_entry',
        __('payroll.activity.time_entry_approved', ['user' => $timeEntry->user->name]),
        $timeEntry
      )
    );

    return redirect()->back()->with('flash.banner', __('payroll.time_entries.approved_successfully'));
  }

  public function bulkApprove(Request $request)
  {
    // Only admins can bulk approve
    if (!auth()->user()->hasRole('admin')) {
      abort(403, __('payroll.time_entries.unauthorized_approval'));
    }

    $request->validate([
      'time_entry_ids' => 'required|array',
      'time_entry_ids.*' => 'exists:time_entries,id',
    ]);

    $timeEntries = TimeEntry::whereIn('id', $request->time_entry_ids)->where('is_approved', false)->get();

    $approvedCount = 0;
    foreach ($timeEntries as $timeEntry) {
      $timeEntry->update([
        'is_approved' => true,
        'approved_by' => auth()->id(),
        'approved_at' => now(),
      ]);
      $approvedCount++;
      
      event(new ActivityLogged(auth()->user(), 'update_time_entry', '', $timeEntry));
    }


    return redirect()
      ->back()
      ->with('flash.banner', __('payroll.time_entries.bulk_approved_successfully', ['count' => $approvedCount]));
  }

  /**
   * Get tasks for a specific project, filtered by user assignment.
   *
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Http\JsonResponse
   */
  public function getTasksForProject(Project $project)
  {
    $tasks = $project
      ->tasks()
      // Select only the columns needed by the frontend
      ->select('id', 'name', 'start_datetime', 'end_datetime')
      // If the user is NOT an admin, apply an additional filter
      ->when(!auth()->user()->hasRole('admin'), function ($query) {
        // This ensures users only see tasks they are assigned to
        return $query->whereHas('assignedTo', function ($q) {
          $q->where('user_id', auth()->id());
        });
      })
      ->get();

    return response()->json($tasks);
  }
}
