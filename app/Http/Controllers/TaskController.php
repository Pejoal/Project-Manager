<?php namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
  public function all()
  {
    $tasks = Task::with('project')->get();
    return Inertia::render('Tasks/All', compact('tasks'));
  }

  public function index(Project $project)
  {
    $tasks = $project->tasks;
    return Inertia::render('Tasks/Index', compact('tasks', 'project'));
  }

  public function create(Project $project)
  {
    return Inertia::render('Tasks/Create', compact('project'));
  }

  public function store(Request $request, Project $project)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'assigned_to' => 'nullable|string|max:255',
    ]);

    $project->tasks()->create($request->all());

    return redirect()->route('tasks.index', $project);
  }

  public function show(Project $project, Task $task)
  {
    return Inertia::render('Tasks/Show', compact('task', 'project'));
  }

  public function edit(Project $project, Task $task)
  {
    return Inertia::render('Tasks/Edit', compact('task', 'project'));
  }

  public function update(Request $request, Project $project, Task $task)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'assigned_to' => 'nullable|string|max:255',
    ]);

    $task->update($request->all());

    return redirect()->route('tasks.index', $project);
  }

  public function destroy(Project $project, Task $task)
  {
    $task->delete();

    return redirect()->route('tasks.index', $project);
  }
}
