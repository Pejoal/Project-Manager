<?php namespace App\Http\Controllers;

use App\Models\ProjectPriority;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\ActivityLogged;

class ProjectPriorityController extends Controller
{
  public function index()
  {
    $priorities = ProjectPriority::latest()->get();
    return Inertia::render('ProjectPriorities/Index', compact('priorities'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);
    $projectPriority = ProjectPriority::create($request->all());
    event(
      new ActivityLogged(auth()->user(), 'created_project_priority', 'Created a project priority', $projectPriority)
    );
    return redirect()->route('project-priorities.index');
  }

  public function update(Request $request, ProjectPriority $projectPriority)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);
    $projectPriority->update($request->all());
    event(
      new ActivityLogged(auth()->user(), 'updated_project_priority', 'Updated a project priority', $projectPriority)
    );
    return redirect()->route('project-priorities.index');
  }

  public function destroy(ProjectPriority $projectPriority)
  {
    $projectPriority->delete();
    event(
      new ActivityLogged(auth()->user(), 'deleted_project_priority', 'Deleted a project priority', $projectPriority)
    );
    return redirect()->route('project-priorities.index');
  }

  public function edit(ProjectPriority $projectPriority)
  {
    return Inertia::render('ProjectPriorities/Edit', compact('projectPriority'));
  }
}
