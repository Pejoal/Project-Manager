<?php namespace App\Http\Controllers;

use App\Models\ProjectPriority;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectPriorityController extends Controller
{
  public function index()
  {
    $priorities = ProjectPriority::all();
    return Inertia::render('ProjectPriorities/Index', compact('priorities'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);
    ProjectPriority::create($request->all());
    return redirect()->route('project-priorities.index');
  }

  public function update(Request $request, ProjectPriority $projectPriority)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);
    $projectPriority->update($request->all());
    return redirect()->route('project-priorities.index');
  }

  public function destroy(ProjectPriority $projectPriority)
  {
    $projectPriority->delete();
    return redirect()->route('project-priorities.index');
  }

  public function edit(ProjectPriority $projectPriority)
  {
    return Inertia::render(
      'ProjectPriorities/Edit',
      compact('projectPriority')
    );
  }
}
