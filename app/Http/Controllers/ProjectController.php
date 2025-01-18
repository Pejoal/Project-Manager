<?php namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectPriority;
use App\Models\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

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

    return redirect()->route('projects.index');
  }

  public function show(Project $project)
  {
    $project->load(['clients', 'status', 'priority', 'phases.tasks']);
    return Inertia::render('Projects/Show', compact('project'));
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

    return redirect()->route('projects.index');
  }

  public function destroy(Project $project)
  {
    $project->delete();

    return redirect()->route('projects.index');
  }
}
