<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\ActivityLogged;

class MilestoneController extends Controller
{
  public function index(Project $project, Request $request)
  {
    $milestones = $project->milestones()->with('tasks')->latest()->paginate(50);

    $phases = $project->phases;

    return Inertia::render('Milestones/Index', compact('milestones', 'project', 'phases'));
  }

  public function store(Request $request, Project $project)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'phase_id' => 'required|exists:phases,id',
    ]);

    $milestone = $project->milestones()->create($request->all());
    event(new ActivityLogged(auth()->user(), 'created_milestone', 'Created a milestone', $milestone));

    return redirect()->route('milestones.index', $project)->with('success', 'Milestone created successfully!');
  }

  public function show(Project $project, Milestone $milestone)
  {
    if ($milestone->project_id !== $project->id) {
      abort(403, 'Milestone not found in this project');
    }

    $milestone->load(['project:id,name,slug', 'phase:id,name', 'tasks.status', 'tasks.priority', 'tasks.assignedTo']);
    return Inertia::render('Milestones/Show', compact('milestone'));
  }

  public function edit(Project $project, Milestone $milestone)
  {
    if ($milestone->project_id !== $project->id) {
      abort(403, 'Milestone not found in this project');
    }

    $phases = $project->phases()->latest()->select('id', 'name')->get();

    return Inertia::render('Milestones/Edit', compact('milestone', 'project', 'phases'));
  }

  public function update(Request $request, Project $project, Milestone $milestone)
  {
    if ($milestone->project_id !== $project->id) {
      abort(403, 'Milestone not found in this project');
    }

    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
    ]);

    $milestone->update($request->all());
    event(new ActivityLogged(auth()->user(), 'updated_milestone', 'Updated a milestone', $milestone));
    return redirect()->route('milestones.index', $project)->with('success', 'Milestone updated successfully!');
  }

  public function destroy(Project $project, Milestone $milestone)
  {
    if ($milestone->project_id !== $project->id) {
      abort(403, 'Milestone not found in this project');
    }

    $milestone->delete();
    event(new ActivityLogged(auth()->user(), 'deleted_milestone', 'Deleted a milestone', $milestone));
    return redirect()->route('milestones.index', $project)->with('success', 'Milestone deleted successfully!');
  }

  public function sync(Request $request)
  {
    $request->validate([
      'milestones' => ['required', 'array'],
    ]);

    foreach ($request->milestones as $i => $milestone) {
      $order = $i + 1;
      if ($milestone['order'] !== $order) {
        Milestone::find($milestone['id'])->update(['order' => $order]);
      }
      foreach ($milestone['tasks'] as $i => $task) {
        $order = $i + 1;
        if ($task['milestone_id'] !== $milestone['id'] || $task['order'] !== $order) {
          Task::find($task['id'])->update([
            'milestone_id' => $milestone['id'],
            'order' => $order,
          ]);
        }
      }
    }
  }
}
