<?php namespace App\Http\Controllers;

use App\Events\ActivityLogged;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectPriority;
use App\Models\ProjectStatus;
use App\Models\TaskPriority;
use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Models\User;

class ProjectController extends Controller
{
  public function index()
  {
    $projects = Project::with(['clients', 'status', 'priority'])
      ->withCount('tasks')
      ->orderBy('id', 'desc')
      ->get();

    $statuses = ProjectStatus::orderBy('id', 'desc')->get();
    $priorities = ProjectPriority::orderBy('id', 'desc')->get();
    return Inertia::render(
      'Projects/Index',
      compact('projects', 'statuses', 'priorities')
    );
  }

  public function single(Project $project)
  {
    return $project->load([
      'phases:id,name,project_id',
      'phases.milestones:id,name,phase_id',
    ]);
  }

  public function create()
  {
    return Inertia::render('Projects/Create');
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:projects,name',
      'description' => 'nullable|string',
      'clients' => 'nullable|array',
      'clients.*' => 'exists:clients,id',
      'status_id' => 'required|exists:project_statuses,id',
      'priority_id' => 'required|exists:project_priorities,id',
    ]);

    $data = $request->except(['clients']);
    $data['slug'] = Str::slug($request->name);

    $project = Project::create($data);
    $project->clients()->sync($request->clients);

    // Log the activity
    event(
      new ActivityLogged(
        auth()->user(),
        'created_project',
        'Created a new project',
        $project
      )
    );

    return redirect()->route('projects.index');
  }

  public function show(Project $project)
  {
    $project->load([
      'clients',
      'status',
      'priority',
      'phases:id,name,project_id',
      'phases.milestones:id,name,phase_id',
      'phases.tasks.status',
      'phases.tasks.priority',
      'phases.tasks.assignedTo',
    ]);

    $totalTasks = $project->tasks()->count();
    $completedStatus = TaskStatus::where('completed_field', true)->first();
    if ($completedStatus) {
      $completedStatusColor = $completedStatus->color;
      $completedTasks = $project
        ->tasks()
        ->whereHas('status', function ($query) use ($completedStatus) {
          $query->where('id', $completedStatus->id);
        })
        ->count();
    } else {
      $completedStatusColor = null;
      $completedTasks = 0;
    }

    $users = User::latest()->select('id', 'name')->get();
    $taskStatuses = TaskStatus::latest()->get();
    $taskPriorities = TaskPriority::latest()->get();

    return Inertia::render(
      'Projects/Show',
      compact(
        'project',
        'totalTasks',
        'completedTasks',
        'completedStatusColor',
        'users',
        'taskStatuses',
        'taskPriorities'
      )
    );
  }

  public function edit(Project $project)
  {
    $project->load(['clients']);
    $clients = Client::orderBy('id', 'desc')->get();
    $statuses = ProjectStatus::all();
    $priorities = ProjectPriority::all();

    return Inertia::render(
      'Projects/Edit',
      compact(['project', 'clients', 'statuses', 'priorities'])
    );
  }

  public function update(Request $request, Project $project)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:projects,name,' . $project->id,
      'description' => 'nullable|string',
      'clients' => 'nullable|array',
      'clients.*' => 'exists:clients,id',
      'status_id' => 'required|exists:project_statuses,id',
      'priority_id' => 'required|exists:project_priorities,id',
    ]);

    $data = $request->except(['clients']);
    $data['slug'] = Str::slug($request->name);

    $project->update($data);
    $project->clients()->sync($request->clients);
    event(new ActivityLogged(auth()->user(), 'updated_project', 'Updated a project', $project));

    return redirect()->route('projects.index');
  }

  public function destroy(Project $project)
  {
    $project->delete();
    event(new ActivityLogged(auth()->user(), 'deleted_project', 'Deleted a project', $project));

    return redirect()->route('projects.index');
  }
}
