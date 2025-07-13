<?php

namespace App\Http\Controllers;

use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\ActivityLogged;

class TaskStatusController extends Controller
{
  public function index()
  {
    $statuses = TaskStatus::latest()->get();
    return Inertia::render('TaskStatuses/Index', compact('statuses'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
      'completed_field' => 'boolean',
    ]);

    if ($request->completed_field) {
      TaskStatus::where('completed_field', true)->update([
        'completed_field' => false,
      ]);
    }

    $taskStatus = TaskStatus::create($request->all());
    event(new ActivityLogged(auth()->user(), 'created_task_status', 'Created a task status', $taskStatus));
    return redirect()->route('task-statuses.index')->with('success', 'Task status created successfully!');
  }

  public function update(Request $request, TaskStatus $taskStatus)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
      'completed_field' => 'boolean',
    ]);

    if ($request->completed_field && !$taskStatus->completed_field) {
      TaskStatus::where('completed_field', true)->update([
        'completed_field' => false,
      ]);
    }

    $taskStatus->update($request->all());
    event(new ActivityLogged(auth()->user(), 'updated_task_status', 'Updated a task status', $taskStatus));
    return redirect()->route('task-statuses.index')->with('success', 'Task status updated successfully!');
  }

  public function destroy(TaskStatus $taskStatus)
  {
    $taskStatus->delete();
    event(new ActivityLogged(auth()->user(), 'deleted_task_status', 'Deleted a task status', $taskStatus));
    return redirect()->route('task-statuses.index')->with('success', 'Task status deleted successfully!');
  }

  public function edit(TaskStatus $taskStatus)
  {
    return Inertia::render('TaskStatuses/Edit', compact('taskStatus'));
  }
}
