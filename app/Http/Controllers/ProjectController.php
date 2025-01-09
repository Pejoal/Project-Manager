<?php namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProjectController extends Controller
{
  public function index()
  {
    $projects = Project::withCount('tasks')->orderBy('id', 'desc')->get();
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
    ]);

    $data = $request->all();
    $data['slug'] = Str::slug($request->name);

    Project::create($data);

    return redirect()->route('projects.index');
  }

  public function show(Project $project)
  {
    return Inertia::render('Projects/Show', compact('project'));
  }

  public function edit(Project $project)
  {
    return Inertia::render('Projects/Edit', compact('project'));
  }

  public function update(Request $request, Project $project)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:projects,name,' . $project->id,
      'description' => 'nullable|string',
    ]);

    $data = $request->all();
    $data['slug'] = Str::slug($request->name);

    $project->update($data);

    return redirect()->route('projects.index');
  }

  public function destroy(Project $project)
  {
    $project->delete();

    return redirect()->route('projects.index');
  }
}
