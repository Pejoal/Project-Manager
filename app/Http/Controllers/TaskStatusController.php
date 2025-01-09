<?php namespace App\Http\Controllers;

use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskStatusController extends Controller
{
  public function index()
  {
    $statuses = TaskStatus::all();
    return Inertia::render('TaskStatuses/Index', compact('statuses'));
  }

  public function store(Request $request)
  {
    $request->validate(['name' => 'required|string|max:255']);
    TaskStatus::create($request->all());
    return redirect()->route('task-statuses.index');
  }

  public function update(Request $request, TaskStatus $taskStatus)
  {
    $request->validate(['name' => 'required|string|max:255']);
    $taskStatus->update($request->all());
    return redirect()->route('task-statuses.index');
  }

  public function destroy(TaskStatus $taskStatus)
  {
    $taskStatus->delete();
    return redirect()->route('task-statuses.index');
  }

  public function edit(TaskStatus $taskStatus)
  {
    return Inertia::render('TaskStatuses/Edit', compact('taskStatus'));
  }
}
