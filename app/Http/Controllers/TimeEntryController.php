<?php
namespace App\Http\Controllers;

use App\Events\ActivityLogged;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TimeEntryController extends Controller
{
  public function index(Request $request)
  {
    $query = TimeEntry::with(['task', 'project', 'user'])
      ->forUser(auth()->id())
      ->latest();

    // Apply filters
    if ($request->has('project_id') && $request->project_id) {
      $query->forProject($request->project_id);
    }

    if ($request->has('task_id') && $request->task_id) {
      $query->forTask($request->task_id);
    }

    if ($request->has('date_from') && $request->date_from) {
      $query->where('start_time', '>=', $request->date_from);
    }

    if ($request->has('date_to') && $request->date_to) {
      $query->where('start_time', '<=', $request->date_to);
    }

    if ($request->has('status')) {
      if ($request->status === 'running') {
        $query->running();
      } elseif ($request->status === 'completed') {
        $query->completed();
      }
    }

    $timeEntries = $query->paginate(15);

    // Get projects and tasks for filters
    $projects = Project::orderBy('name')->get(['id', 'name']);
    $tasks = Task::with('project:id,name')->orderBy('name')->get();

    // Get running timer for current user
    $runningTimer = TimeEntry::forUser(auth()->id())
      ->running()
      ->with(['task', 'project'])
      ->first();

    return Inertia::render('TimeTracking/Index', [
      'timeEntries' => $timeEntries,
      'projects' => $projects,
      'tasks' => $tasks,
      'runningTimer' => $runningTimer,
      'filters' => $request->only(['project_id', 'task_id', 'date_from', 'date_to', 'status']),
    ]);
  }

  public function start(Request $request)
  {
    $request->validate([
      'task_id' => 'required|exists:tasks,id',
      'description' => 'nullable|string|max:500',
    ]);

    $task = Task::findOrFail($request->task_id);

    // Stop any running timers for this user
    TimeEntry::forUser(auth()->id())
      ->running()
      ->get()
      ->each(function ($entry) {
        $entry->stop();
      });

    // Create new time entry
    $timeEntry = TimeEntry::create([
      'user_id' => auth()->id(),
      'task_id' => $task->id,
      'project_id' => $task->project_id,
      'start_time' => now(),
      'description' => $request->description,
      'is_running' => true,
    ]);

    event(new ActivityLogged(auth()->user(), 'started_timer', 'Started timer for task: ' . $task->name, $timeEntry));

    return response()->json([
      'message' => 'Timer started successfully',
      'timeEntry' => $timeEntry->load(['task', 'project']),
    ]);
  }

  public function stop(Request $request, TimeEntry $timeEntry)
  {
    if ($timeEntry->user_id !== auth()->id()) {
      return response()->json(['error' => 'Unauthorized'], 403);
    }

    if (!$timeEntry->is_running) {
      return response()->json(['error' => 'Timer is not running'], 400);
    }

    $timeEntry->stop();

    event(
      new ActivityLogged(
        auth()->user(),
        'stopped_timer',
        'Stopped timer for task: ' . $timeEntry->task->name,
        $timeEntry
      )
    );

    return response()->json([
      'message' => 'Timer stopped successfully',
      'timeEntry' => $timeEntry->load(['task', 'project']),
    ]);
  }

  public function update(Request $request, TimeEntry $timeEntry)
  {
    if ($timeEntry->user_id !== auth()->id()) {
      return response()->json(['error' => 'Unauthorized'], 403);
    }

    $request->validate([
      'start_time' => 'required|date',
      'end_time' => 'required|date|after:start_time',
      'description' => 'nullable|string|max:500',
    ]);

    $timeEntry->update([
      'start_time' => $request->start_time,
      'end_time' => $request->end_time,
      'description' => $request->description,
      'is_running' => false,
    ]);

    event(
      new ActivityLogged(
        auth()->user(),
        'updated_time_entry',
        'Updated time entry for task: ' . $timeEntry->task->name,
        $timeEntry
      )
    );

    return response()->json([
      'message' => 'Time entry updated successfully',
      'timeEntry' => $timeEntry->load(['task', 'project']),
    ]);
  }

  public function destroy(TimeEntry $timeEntry)
  {
    if ($timeEntry->user_id !== auth()->id()) {
      return response()->json(['error' => 'Unauthorized'], 403);
    }

    $taskName = $timeEntry->task->name;
    $timeEntry->delete();

    event(
      new ActivityLogged(auth()->user(), 'deleted_time_entry', 'Deleted time entry for task: ' . $taskName, $timeEntry)
    );

    return response()->json(['message' => 'Time entry deleted successfully']);
  }

  public function getRunningTimer()
  {
    $runningTimer = TimeEntry::forUser(auth()->id())
      ->running()
      ->with(['task', 'project'])
      ->first();

    return response()->json(['runningTimer' => $runningTimer]);
  }

  public function quickStart(Request $request)
  {
    $request->validate([
      'task_id' => 'required|exists:tasks,id',
    ]);

    // Stop any running timers
    TimeEntry::forUser(auth()->id())
      ->running()
      ->get()
      ->each(function ($entry) {
        $entry->stop();
      });

    $task = Task::findOrFail($request->task_id);

    $timeEntry = TimeEntry::create([
      'user_id' => auth()->id(),
      'task_id' => $task->id,
      'project_id' => $task->project_id,
      'start_time' => now(),
      'is_running' => true,
    ]);

    return response()->json([
      'message' => 'Timer started',
      'timeEntry' => $timeEntry->load(['task', 'project']),
    ]);
  }
}
