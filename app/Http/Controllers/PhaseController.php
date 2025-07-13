<?php

namespace App\Http\Controllers;

use App\Events\ActivityLogged;
use App\Models\Phase;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PhaseController extends Controller
{
  public function index(Project $project, Request $request)
  {
    $phases = $project->phases()->with('tasks')->latest()->paginate(5);
    return Inertia::render('Phases/Index', compact('phases', 'project'));
  }

  public function store(Request $request, Project $project)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
    ]);

    $phase = $project->phases()->create($request->all());
    event(new ActivityLogged(auth()->user(), 'created_phase', 'Created a phase', $phase));

    return redirect()->route('phases.index', $project);
  }

  public function show(Project $project, Phase $phase)
  {
    if ($phase->project_id !== $project->id) {
      abort(403, 'Phase not found in this project');
    }

    $phase->load(['project', 'milestones']);
    return Inertia::render('Phases/Show', compact('phase'));
  }

  public function edit(Project $project, Phase $phase)
  {
    if ($phase->project_id !== $project->id) {
      abort(403, 'Phase not found in this project');
    }

    return Inertia::render('Phases/Edit', compact('phase', 'project'));
  }

  public function update(Request $request, Project $project, Phase $phase)
  {
    if ($phase->project_id !== $project->id) {
      abort(403, 'Phase not found in this project');
    }

    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
    ]);

    $phase->update($request->all());
    event(new ActivityLogged(auth()->user(), 'updated_phase', 'Updated a phase', $phase));

    return redirect()->route('phases.index', $project);
  }

  public function destroy(Project $project, Phase $phase)
  {
    if ($phase->project_id !== $project->id) {
      abort(403, 'Phase not found in this project');
    }

    $phase->delete();
    event(new ActivityLogged(auth()->user(), 'deleted_phase', 'Deleted a phase', $phase));
    return redirect()->route('phases.index', $project);
  }

  public function sync(Request $request)
  {
    $request->validate([
      'phases' => ['required', 'array'],
    ]);

    foreach ($request->phases as $i => $phase) {
      $order = $i + 1;
      if ($phase['order'] !== $order) {
        Phase::find($phase['id'])->update(['order' => $order]);
      }
      foreach ($phase['tasks'] as $i => $task) {
        $order = $i + 1;
        if ($task['phase_id'] !== $phase['id'] || $task['order'] !== $order) {
          Task::find($task['id'])->update([
            'phase_id' => $phase['id'],
            'order' => $order,
          ]);
        }
      }
    }
  }
}
