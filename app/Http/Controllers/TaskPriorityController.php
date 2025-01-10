<?php namespace App\Http\Controllers;

use App\Models\TaskPriority;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskPriorityController extends Controller
{
  public function index()
  {
    $priorities = TaskPriority::all();
    return Inertia::render('TaskPriorities/Index', compact('priorities'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);
    TaskPriority::create($request->all());
    return redirect()->route('task-priorities.index');
  }

  public function update(Request $request, TaskPriority $taskPriority)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);
    $taskPriority->update($request->all());
    return redirect()->route('task-priorities.index');
  }

  public function destroy(TaskPriority $taskPriority)
  {
    $taskPriority->delete();
    return redirect()->route('task-priorities.index');
  }

  public function edit(TaskPriority $taskPriority)
  {
    return Inertia::render('TaskPriorities/Edit', compact('taskPriority'));
  }
}
