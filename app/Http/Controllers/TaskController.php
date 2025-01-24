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
use App\Events\ActivityLogged;

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
    $users = User::latest()->select('id', 'name')->get();
    $projects = Project::latest()->get();
    $statuses = TaskStatus::latest()->select('id', 'name', 'color')->get();
    $priorities = TaskPriority::latest()->select('id', 'name', 'color')->get();

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
        $query->with(
          'status',
          'priority',
          'assignedTo',
          'project:id,slug,name'
        );
        $this->applyFilters($query, $request);
      });
    } else {
      $query = Task::with(
        'status',
        'priority',
        'assignedTo',
        'project:id,slug,name'
      );
      $this->applyFilters($query, $request);
    }

    $perPage = $request->input('perPage', 10);
    $tasks = $query->latest()->paginate($perPage)->appends($request->except('page'));

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
    $users = User::latest()->select('id', 'name')->get();
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
          ->with('status', 'priority', 'assignedTo', 'project:id,slug,name');
        $this->applyFilters($query, $request);
      });
    } else {
      $query = $project
        ->tasks()
        ->with('status', 'priority', 'assignedTo', 'project:id,slug,name');
      $this->applyFilters($query, $request);
    }

    $perPage = $request->input('perPage', 10);
    $tasks = $query->latest()->paginate($perPage)->appends($request->except('page'));

    $tasks->getCollection()->transform(function ($task) {
      $metadata = $task->scoutMetadata();
      $task->name = $metadata['_formatted']['name'] ?? $task->name;
      $task->description =
        $metadata['_formatted']['description'] ?? $task->description;
      return $task;
    });

    $project->load([
      'phases:id,name,project_id',
      'phases.milestones:id,name,phase_id',
    ]);
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
      'phase_id' => 'nullable|exists:phases,id',
      'milestone_id' => 'nullable|exists:milestones,id',
    ]);

    $task = $project
      ->tasks()
      ->create($request->except(['assigned_to', 'project_slug']));
    $task->assignedTo()->sync($request->assigned_to);
    event(new ActivityLogged(auth()->user(), 'created_task', 'Created a task', $task));

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

    $task->load([
      'assignedTo:id,name',
      'status:id,name,color',
      'priority:id,name,color',
      'project:id,name,slug',
      'phase:id,name',
      'milestone:id,name',
    ]);
    return Inertia::render('Tasks/Show', compact('task'));
  }

  public function edit(Project $project, Task $task)
  {
    if ($task->project_id !== $project->id) {
      abort(403, 'Task not found in this project');
    }

    $users = User::latest()->select('id', 'name')->get();
    $task->load('assignedTo');
    $statuses = TaskStatus::latest()->get();
    $priorities = TaskPriority::latest()->get();
    return Inertia::render(
      'Tasks/Edit',
      compact('task', 'project', 'users', 'statuses', 'priorities')
    );
  }

  public function update(Request $request, Project $project, Task $task)
  {
    if ($task->project_id !== $project->id) {
      abort(403, 'Task not found in this project');
    }

    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'assigned_to' => 'nullable|array',
      'assigned_to.*' => 'exists:users,id',
      'status_id' => 'required|exists:task_statuses,id',
      'priority_id' => 'required|exists:task_priorities,id',
      'phase_id' => 'nullable|exists:phases,id',
      'milestone_id' => 'nullable|exists:milestones,id',
    ]);

    $task->update($request->except('assigned_to'));
    $task->assignedTo()->sync($request->assigned_to);
    event(new ActivityLogged(auth()->user(), 'updated_task', 'Updated a task', $task));

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
    if ($task->project_id !== $project->id) {
      abort(403, 'Task not found in this project');
    }

    $task->delete();
    event(new ActivityLogged(auth()->user(), 'deleted_task', 'Deleted a task', $task));
    return redirect()->route('tasks.index', $project);
  }
}
