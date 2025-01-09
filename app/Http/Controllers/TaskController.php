<?php namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
  public function all()
  {
    $users = User::orderBy('id', 'desc')->get();
    $projects = Project::orderBy('id', 'desc')->get();
    $tasks = Task::with('project')->orderBy('id', 'desc')->get();
    return Inertia::render('Tasks/All', compact('tasks', 'projects', 'users'));
  }

  public function index(Project $project)
  {
    $users = User::orderBy('id', 'desc')->get();
    $tasks = $project->tasks()->orderBy('id', 'desc')->get();
    return Inertia::render('Tasks/Index', compact('tasks', 'project', 'users'));
  }

  public function store(Request $request, Project $project)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'assigned_to' => 'nullable|array',
      'assigned_to.*' => 'exists:users,id',
    ]);

    $task = $project->tasks()->create($request->except('assigned_to'));
    $task->assignedTo()->sync($request->assigned_to);

    return redirect()->route('tasks.index', $project);
  }

  public function show(Project $project, Task $task)
  {
    $task->load('assignedTo');
    return Inertia::render('Tasks/Show', compact('task', 'project'));
  }

  public function edit(Project $project, Task $task)
  {
    $users = User::orderBy('id', 'desc')->get();
    $task->load('assignedTo');
    return Inertia::render('Tasks/Edit', compact('task', 'project', 'users'));
  }

  public function update(Request $request, Project $project, Task $task)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'assigned_to' => 'nullable|array',
      'assigned_to.*' => 'exists:users,id',
    ]);

    $task->update($request->except('assigned_to'));
    $task->assignedTo()->sync($request->assigned_to);

    return redirect()->route('tasks.index', $project);
  }

  public function destroy(Project $project, Task $task)
  {
    $task->delete();

    return redirect()->route('tasks.index', $project);
  }

  public function updateStatus(Request $request, Task $task)
  {
    $request->validate(['status' => 'required|string']);
    $task->update(['status' => $request->status]);
    return redirect()->back();
  }

  public function updatePriority(Request $request, Task $task)
  {
    $request->validate(['priority' => 'required|string']);
    $task->update(['priority' => $request->priority]);
    return redirect()->back();
  }
}
