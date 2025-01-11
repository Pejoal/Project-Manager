<?php namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\TaskStatus;
use App\Models\TaskPriority;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use App\Mail\TaskAssigned;

class TaskController extends Controller
{
  private function applyFilters($query, Request $request)
  {
    if ($request->has('search') && !empty($request->search)) {
      $search = $request->search;
      $query->where('name', 'like', "%{$search}%");
    }

    if ($request->has('status') && !empty($request->status)) {
      $query->whereIn('status_id', $request->status);
    }

    if ($request->has('priority') && !empty($request->priority)) {
      $query->whereIn('priority_id', $request->priority);
    }

    if ($request->has('assigned_to') && !empty($request->assigned_to)) {
      $query->whereHas('assignedTo', function ($q) use ($request) {
        $q->whereIn('user_id', $request->assigned_to);
      });
    }

    if (
      $request->has('assigned_to_me') &&
      $request->assigned_to_me === 'true'
    ) {
      $query->whereHas('assignedTo', function ($q) use ($request) {
        $q->where('user_id', auth()->user()->id);
      });
    }
  }

  public function all(Request $request)
  {
    $users = User::orderBy('id', 'desc')->get();
    $projects = Project::orderBy('id', 'desc')->get();
    $statuses = TaskStatus::orderBy('id', 'desc')->get();
    $priorities = TaskPriority::orderBy('id', 'desc')->get();

    $query = Task::with('project', 'status', 'priority', 'assignedTo');
    $this->applyFilters($query, $request);

    $perPage = $request->input('perPage', 5);
    $tasks = $query->latest('id')->paginate($perPage);
    return Inertia::render(
      'Tasks/Index',
      compact('tasks', 'projects', 'users', 'statuses', 'priorities')
    );
  }

  public function index(Project $project, Request $request)
  {
    $users = User::orderBy('id', 'desc')->get();
    $statuses = TaskStatus::orderBy('id', 'desc')->get();
    $priorities = TaskPriority::orderBy('id', 'desc')->get();
    $query = $project->tasks()->with('status', 'priority', 'assignedTo');
    $this->applyFilters($query, $request);

    $perPage = $request->input('perPage', 5);
    $tasks = $query->latest('id')->paginate($perPage);

    return Inertia::render(
      'Tasks/Index',
      compact('tasks', 'project', 'users', 'statuses', 'priorities')
    );
  }

  public function store(Request $request, Project $project)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'assigned_to' => 'nullable|array',
      'assigned_to.*' => 'exists:users,id',
      'status_id' => 'required|exists:task_statuses,id',
      'priority_id' => 'required|exists:task_priorities,id',
    ]);

    $task = $project
      ->tasks()
      ->create($request->except(['assigned_to', 'project_slug']));
    $task->assignedTo()->sync($request->assigned_to);

    // Send email notifications
    if ($request->has('assigned_to')) {
      $assignedUsers = User::whereIn('id', $request->assigned_to)->get();
      foreach ($assignedUsers as $user) {
        Mail::to($user->email)->send(new TaskAssigned($task, $user, true));
      }
    }

    return redirect()->route('tasks.index', $project);
  }

  public function show(Project $project, Task $task)
  {
    $task->load(['assignedTo', 'status', 'priority']);
    return Inertia::render('Tasks/Show', compact('task', 'project'));
  }

  public function edit(Project $project, Task $task)
  {
    $users = User::orderBy('id', 'desc')->get();
    $task->load('assignedTo');
    $statuses = TaskStatus::all();
    $priorities = TaskPriority::all();
    return Inertia::render(
      'Tasks/Edit',
      compact('task', 'project', 'users', 'statuses', 'priorities')
    );
  }

  public function update(Request $request, Project $project, Task $task)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'assigned_to' => 'nullable|array',
      'assigned_to.*' => 'exists:users,id',
      'status_id' => 'required|exists:task_statuses,id',
      'priority_id' => 'required|exists:task_priorities,id',
    ]);

    $task->update($request->except('assigned_to'));
    $task->assignedTo()->sync($request->assigned_to);

    // Send email notifications
    if ($request->has('assigned_to')) {
      $assignedUsers = User::whereIn('id', $request->assigned_to)->get();
      foreach ($assignedUsers as $user) {
        Mail::to($user->email)->send(new TaskAssigned($task, $user, false));
      }
    }

    return redirect()->route('tasks.index', $project);
  }

  public function destroy(Project $project, Task $task)
  {
    $task->delete();

    return redirect()->route('tasks.index', $project);
  }

  public function updateStatus(Request $request, Task $task)
  {
    $request->validate(['status_id' => 'required|exists:task_statuses,id']);
    $task->update(['status_id' => $request->status_id]);
    return redirect()->back();
  }

  public function updatePriority(Request $request, Task $task)
  {
    $request->validate(['priority_id' => 'required|exists:task_priorities,id']);
    $task->update(['priority_id' => $request->priority_id]);
    return redirect()->back();
  }
}
