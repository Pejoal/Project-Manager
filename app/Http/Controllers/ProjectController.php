<?php namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProjectController extends Controller
{
  public function index()
  {
    $projects = Project::with(['clients'])
      ->withCount('tasks')
      ->orderBy('id', 'desc')
      ->get();
    return Inertia::render('Projects/Index', compact('projects'));
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
    ]);

    $data = $request->except(['clients']);
    $data['slug'] = Str::slug($request->name);

    $project = Project::create($data);
    $project->clients()->sync($request->clients);

    return redirect()->route('projects.index');
  }

  public function show(Project $project)
  {
    $project->load(['clients']);
    return Inertia::render('Projects/Show', compact('project'));
  }

  public function edit(Project $project)
  {
    $project->load(['clients']);
    $clients = Client::orderBy('id', 'desc')->get();
    return Inertia::render('Projects/Edit', compact(['project', 'clients']));
  }

  public function update(Request $request, Project $project)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:projects,name,' . $project->id,
      'description' => 'nullable|string',
      'clients' => 'nullable|array',
      'clients.*' => 'exists:clients,id',
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
