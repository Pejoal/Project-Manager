<?php

namespace App\Http\Controllers;

use App\Models\Phase;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\TaskStatus;
use App\Models\TaskPriority;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use App\Mail\TaskAssigned;
use Illuminate\Database\Eloquent\Builder;

class TaskController extends Controller
{
  private function applyFilters($query, Request $request)
  {
    $request->validate([
      'search' => 'nullable|string|max:255',
      'status' => 'nullable|array',
      'status.*' => 'integer|exists:task_statuses,id',
      'priority' => 'nullable|array',
      'priority.*' => 'integer|exists:task_priorities,id',
      'assigned_to' => 'nullable|array',
      'assigned_to.*' => 'integer|exists:users,id',
      'projects' => 'nullable|array',
      'projects.*' => 'integer|exists:projects,id',
      'assigned_to_me' => 'nullable|string',
      'perPage' => 'nullable|integer|min:1|max:100',
    ]);

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

    if ($request->has('projects') && !empty($request->projects)) {
      $query->whereIn('project_id', $request->projects);
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

    if ($request->has('search') && !empty($request->search)) {
      $search = $request->search;
      $query = Task::search($search, function (
        $meilisearch,
        string $query,
        array $options
      ) {
        $options['attributesToHighlight'] = ['name', 'description'];
        $options['highlightPreTag'] = '<mark><strong>';
        $options['highlightPostTag'] = '</strong></mark>';

        return $meilisearch->search($query, $options);
      })->query(function (Builder $query) use ($request) {
        $query->with('project', 'status', 'priority', 'assignedTo');
        $this->applyFilters($query, $request);
      });
    } else {
      $query = Task::with('project', 'status', 'priority', 'assignedTo');
      $this->applyFilters($query, $request);
    }

    $perPage = $request->input('perPage', 10);
    $tasks = $query->latest('created_at')->paginate($perPage);

    $tasks->getCollection()->transform(function ($task) {
      $metadata = $task->scoutMetadata();
      $task->name = $metadata['_formatted']['name'] ?? $task->name;
      $task->description =
        $metadata['_formatted']['description'] ?? $task->description;
      return $task;
    });

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

    if ($request->has('search') && !empty($request->search)) {
      $search = $request->search;
      $query = Task::search($search, function (
        $meilisearch,
        string $query,
        array $options
      ) {
        $options['attributesToHighlight'] = ['name', 'description'];
        $options['highlightPreTag'] = '<mark><strong>';
        $options['highlightPostTag'] = '</strong></mark>';

        return $meilisearch->search($query, $options);
      })->query(function (Builder $query) use ($project, $request) {
        $query
          ->where('project_id', $project->id)
          ->with('project', 'status', 'priority', 'assignedTo');
        $this->applyFilters($query, $request);
      });
    } else {
      $query = $project->tasks()->with('status', 'priority', 'assignedTo');
      $this->applyFilters($query, $request);
    }

    $perPage = $request->input('perPage', 10);
    $tasks = $query->latest('created_at')->paginate($perPage);

    $tasks->getCollection()->transform(function ($task) {
      $metadata = $task->scoutMetadata();
      $task->name = $metadata['_formatted']['name'] ?? $task->name;
      $task->description =
        $metadata['_formatted']['description'] ?? $task->description;
      return $task;
    });

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
    // if ($request->has('assigned_to')) {
    //   $assignedUsers = User::whereIn('id', $request->assigned_to)->get();
    //   foreach ($assignedUsers as $user) {
    //     Mail::to($user->email)->send(new TaskAssigned($task, $user, true));
    //   }
    // }

    return redirect()->route('tasks.index', $project);
  }

  public function show(Project $project, Task $task)
  {
    if ($task->project_id !== $project->id) {
      abort(403, 'Task not found in this project');
    }

    $task->load(['assignedTo', 'status', 'priority', 'project']);
    return Inertia::render('Tasks/Show', compact('task'));
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
    // if ($request->has('assigned_to')) {
    //   $assignedUsers = User::whereIn('id', $request->assigned_to)->get();
    //   foreach ($assignedUsers as $user) {
    //     Mail::to($user->email)->send(new TaskAssigned($task, $user, false));
    //   }
    // }

    return redirect()->route('tasks.index', $project);
  }

  public function destroy(Project $project, Task $task)
  {
    $task->delete();
    return redirect()->route('tasks.index', $project);
  }
}
