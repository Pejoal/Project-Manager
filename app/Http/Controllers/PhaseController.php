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
    $phases = Phase::latest('created_at')->get();
    return Inertia::render('Phases/Index', compact('phases', 'project'));
  }

  public function store(Request $request, Project $project)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
    ]);

    return redirect()->route('phases.index', $project);
  }

  public function show(Project $project, Phase $task)
  {
    if ($task->project_id !== $project->id) {
      abort(403, 'Phase not found in this project');
    }

    return Inertia::render('Phases/Show', compact('task'));
  }

  public function edit(Project $project, Phase $task)
  {
    return Inertia::render('Phases/Edit');
  }

  public function update(Request $request, Project $project, Phase $phase)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
    ]);

    return redirect()->route('phases.index', $project);
  }

  public function destroy(Project $project, Phase $task)
  {
    $task->delete();

    return redirect()->route('phases.index', $project);
  }
}
