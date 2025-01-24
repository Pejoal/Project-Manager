<?php namespace App\Http\Controllers;

use App\Models\TaskPriority;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\ActivityLogged;

class TaskPriorityController extends Controller
{
  public function index()
  {
    $priorities = TaskPriority::latest()->get();
    return Inertia::render('TaskPriorities/Index', compact('priorities'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);
    $taskPriority = TaskPriority::create($request->all());
    event(
      new ActivityLogged(
        auth()->user(),
        'created_task_priority',
        'Created a task priority',
        $taskPriority
      )
    );
    return redirect()->route('task-priorities.index');
  }

  public function update(Request $request, TaskPriority $taskPriority)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);
    $taskPriority->update($request->all());
    event(
      new ActivityLogged(
        auth()->user(),
        'updated_task_priority',
        'Updated a task priority',
        $taskPriority
      )
    );
    return redirect()->route('task-priorities.index');
  }

  public function destroy(TaskPriority $taskPriority)
  {
    $taskPriority->delete();
    event(
      new ActivityLogged(
        auth()->user(),
        'deleted_task_priority',
        'Deleted a task priority',
        $taskPriority
      )
    );
    return redirect()->route('task-priorities.index');
  }

  public function edit(TaskPriority $taskPriority)
  {
    return Inertia::render('TaskPriorities/Edit', compact('taskPriority'));
  }
}
