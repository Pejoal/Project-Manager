<?php

namespace App\Http\Controllers;

use App\Models\Phase;
use App\Models\Project;
use App\Models\User;
use App\Models\PhaseStatus;
use App\Models\PhasePriority;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Builder;

class PhaseController extends Controller
{
  public function index(Project $project, Request $request)
  {
    $phases = $project->phases()->with('tasks')->latest('created_at')->get();
    return Inertia::render('Phases/Index', compact('phases', 'project'));
  }

  public function store(Request $request, Project $project)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
    ]);

    $project->phases()->create($request->except(['project_slug']));

    return redirect()->route('phases.index', $project);
  }

  public function show(Project $project, Phase $phase)
  {
    if ($phase->project_id !== $project->id) {
      abort(403, 'Phase not found in this project');
    }

    $phase->load([
      'project',
      'tasks.status',
      'tasks.priority',
      'tasks.assignedTo',
    ]);
    return Inertia::render('Phases/Show', compact('phase'));
  }

  public function edit(Project $project, Phase $phase)
  {
    return Inertia::render('Phases/Edit', compact('phase', 'project'));
  }

  public function update(Request $request, Project $project, Phase $phase)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
    ]);

    $phase->update($request->all());
    return redirect()->route('phases.index', $project);
  }

  public function destroy(Project $project, Phase $phase)
  {
    $phase->delete();
    return redirect()->route('phases.index', $project);
  }

  public function sync(Request $request)
  {
    $request->validate([
      'phases' => ['required', 'array'],
    ]);

    foreach ($request->phases as $i => $phase) {
      $order = $i + 10;
      if ($phase['order'] !== $order) {
        Phase::find($phase['id'])->update(['order' => $order]);
      }
    }
  }
}
