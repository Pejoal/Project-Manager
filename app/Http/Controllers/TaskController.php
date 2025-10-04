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
      'assigned_to_me' => 'nullable|string|in:true,false',
      'per_page' => 'nullable|integer|min:1|max:100',
      'sort_by' =>
        'nullable|string|in:name,description,created_at,start_datetime,end_datetime,status,priority,project,assigned_to',
      'sort_direction' => 'nullable|string|in:asc,desc',
    ]);

    // Apply sorting
    $sortBy = $request->input('sort_by', 'created_at');
    $sortDirection = $request->input('sort_direction', 'desc');

    // Handle special sorting for relationships
    if ($sortBy === 'assigned_to') {
      // Use subquery to avoid GROUP BY issues
      $query
        ->leftJoinSub(
          \DB::table('task_user')
            ->join('users', 'task_user.user_id', '=', 'users.id')
            ->select('task_user.task_id', \DB::raw('MIN(users.name) as user_name'))
            ->groupBy('task_user.task_id'),
          'assigned_users',
          'tasks.id',
          '=',
          'assigned_users.task_id'
        )
        ->orderBy('assigned_users.user_name', $sortDirection);
    } elseif ($sortBy === 'status') {
      $query
        ->join('task_statuses', 'tasks.status_id', '=', 'task_statuses.id')
        ->orderBy('task_statuses.name', $sortDirection)
        ->select('tasks.*');
    } elseif ($sortBy === 'priority') {
      $query
        ->join('task_priorities', 'tasks.priority_id', '=', 'task_priorities.id')
        ->orderBy('task_priorities.name', $sortDirection)
        ->select('tasks.*');
    } elseif ($sortBy === 'project') {
      $query
        ->join('projects', 'tasks.project_id', '=', 'projects.id')
        ->orderBy('projects.name', $sortDirection)
        ->select('tasks.*');
    } else {
      $query->orderBy($sortBy, $sortDirection);
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

    if ($request->has('projects') && !empty($request->projects)) {
      $query->whereIn('project_id', $request->projects);
    }

    if ($request->has('assigned_to_me') && $request->assigned_to_me === 'true') {
      $query->whereHas('assignedTo', function ($q) use ($request) {
        $q->where('user_id', auth()->user()->id);
      });
    }
  }

  public function all(Request $request)
  {
    $users = User::latest()->select('id', 'name')->get();
    $projects = Project::latest()->select('id', 'name', 'slug')->get();
    $statuses = TaskStatus::latest()->select('id', 'name', 'color')->get();
    $priorities = TaskPriority::latest()->select('id', 'name', 'color')->get();

    if ($request->has('search') && !empty($request->search)) {
      $search = $request->search;
      $query = Task::search($search, function ($meilisearch, string $query, array $options) {
        $options['attributesToHighlight'] = ['name', 'description'];
        $options['highlightPreTag'] = '<mark><strong>';
        $options['highlightPostTag'] = '</strong></mark>';

        return $meilisearch->search($query, $options);
      })->query(function (Builder $query) use ($request) {
        $query->with(['status', 'priority', 'assignedTo', 'project:id,slug,name']);
        $this->applyFilters($query, $request);
      });
    } else {
      $query = Task::with(['status', 'priority', 'assignedTo', 'project:id,slug,name']);
      $this->applyFilters($query, $request);
    }

    $perPage = $request->input('per_page', 10);
    $tasks = $query->paginate($perPage)->appends($request->except('page'));

    if ($request->has('search') && !empty($request->search)) {
      $tasks->getCollection()->transform(function ($task) {
        $metadata = $task->scoutMetadata();
        $task->name = $metadata['_formatted']['name'] ?? $task->name;
        $task->description = $metadata['_formatted']['description'] ?? $task->description;
        return $task;
      });
    }

    return Inertia::render('Tasks/Index', [
      'tasks' => $tasks,
      'filters' => $request->only([
        'search',
        'status',
        'priority',
        'assigned_to',
        'projects',
        'assigned_to_me',
        'per_page',
        'sort_by',
        'sort_direction',
      ]),
      'projects' => $projects,
      'users' => $users,
      'statuses' => $statuses,
      'priorities' => $priorities,
    ]);
  }

  public function index(Project $project, Request $request)
  {
    $users = User::latest()->select('id', 'name')->get();
    $statuses = TaskStatus::orderBy('id', 'desc')->select('id', 'name', 'color')->get();
    $priorities = TaskPriority::orderBy('id', 'desc')->select('id', 'name', 'color')->get();

    if ($request->has('search') && !empty($request->search)) {
      $search = $request->search;
      $query = Task::search($search, function ($meilisearch, string $query, array $options) {
        $options['attributesToHighlight'] = ['name', 'description'];
        $options['highlightPreTag'] = '<mark><strong>';
        $options['highlightPostTag'] = '</strong></mark>';

        return $meilisearch->search($query, $options);
      })->query(function (Builder $query) use ($project, $request) {
        $query->where('project_id', $project->id)->with(['status', 'priority', 'assignedTo', 'project:id,slug,name']);
        $this->applyFilters($query, $request);
      });
    } else {
      $query = $project->tasks()->with(['status', 'priority', 'assignedTo', 'project:id,slug,name']);
      $this->applyFilters($query, $request);
    }

    $perPage = $request->input('per_page', 15);
    $tasks = $query->paginate($perPage)->appends($request->except('page'));

    if ($request->has('search') && !empty($request->search)) {
      $tasks->getCollection()->transform(function ($task) {
        $metadata = $task->scoutMetadata();
        $task->name = $metadata['_formatted']['name'] ?? $task->name;
        $task->description = $metadata['_formatted']['description'] ?? $task->description;
        return $task;
      });
    }

    $project->load(['phases:id,name,project_id', 'phases.milestones:id,name,phase_id']);

    return Inertia::render('Tasks/Index', [
      'tasks' => $tasks,
      'filters' => $request->only([
        'search',
        'status',
        'priority',
        'assigned_to',
        'assigned_to_me',
        'per_page',
        'sort_by',
        'sort_direction',
      ]),
      'project' => $project,
      'users' => $users,
      'statuses' => $statuses,
      'priorities' => $priorities,
    ]);
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
      'start_datetime' => 'required|date',
      'end_datetime' => 'required|date|after:start_datetime',
    ]);

    $task = $project->tasks()->create($request->except(['assigned_to', 'project_slug']));
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
    $project->load('phases:id,name,project_id', 'phases.milestones:id,name,phase_id');
    $statuses = TaskStatus::latest()->get();
    $priorities = TaskPriority::latest()->get();
    return Inertia::render('Tasks/Edit', compact('task', 'project', 'users', 'statuses', 'priorities'));
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
      'start_datetime' => 'required|date',
      'end_datetime' => 'required|date|after:start_datetime',
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
    if ($task->project_id !== $project->id) {
      abort(403, 'Task not found in this project');
    }

    $task->delete();
    return redirect()->route('tasks.index', $project);
  }

  /**
   * Handle bulk operations on tasks
   */
  public function bulkUpdate(Request $request)
  {
    $request->validate([
      'task_ids' => 'required|array',
      'task_ids.*' => 'exists:tasks,id',
      'action' => 'required|in:update_status,update_priority,assign_users,delete',
      'status_id' => 'required_if:action,update_status|exists:task_statuses,id',
      'priority_id' => 'required_if:action,update_priority|exists:task_priorities,id',
      'user_ids' => 'required_if:action,assign_users|array',
      'user_ids.*' => 'exists:users,id',
    ]);

    $tasks = Task::whereIn('id', $request->task_ids)->get();
    $message = '';

    switch ($request->action) {
      case 'update_status':
        $status = TaskStatus::find($request->status_id);
        $tasks->each(function ($task) use ($request) {
          $task->update(['status_id' => $request->status_id]);
        });
        $message = trans('tasks.bulk_status_updated', ['count' => $tasks->count(), 'status' => $status->name]);
        break;

      case 'update_priority':
        $priority = TaskPriority::find($request->priority_id);
        $tasks->each(function ($task) use ($request) {
          $task->update(['priority_id' => $request->priority_id]);
        });
        $message = trans('tasks.bulk_priority_updated', ['count' => $tasks->count(), 'priority' => $priority->name]);
        break;

      case 'assign_users':
        $tasks->each(function ($task) use ($request) {
          $task->assignedTo()->sync($request->user_ids);
        });
        $message = trans('tasks.bulk_assigned', ['count' => $tasks->count()]);
        break;

      case 'delete':
        $tasks->each(function ($task) {
          $task->delete();
        });
        $message = trans('tasks.bulk_deleted', ['count' => $tasks->count()]);
        break;

      default:
        return redirect()
          ->back()
          ->withErrors(['action' => 'Invalid action']);
    }

    return redirect()->back()->with('flash.banner', $message);
  }
}
