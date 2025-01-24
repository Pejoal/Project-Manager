<?php

namespace App\Http\Controllers;

use App\Models\ProjectStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\ActivityLogged;

class ProjectStatusController extends Controller
{
  public function index()
  {
    $statuses = ProjectStatus::latest()->get();
    return Inertia::render('ProjectStatuses/Index', compact('statuses'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);
    $projectStatus = ProjectStatus::create($request->all());
    event(
      new ActivityLogged(
        auth()->user(),
        'created_project_status',
        'Created a project status',
        $projectStatus
      )
    );
    return redirect()->route('project-statuses.index');
  }

  public function update(Request $request, ProjectStatus $projectStatus)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);
    $projectStatus->update($request->all());
    event(
      new ActivityLogged(
        auth()->user(),
        'updated_project_status',
        'Updated a project status',
        $projectStatus
      )
    );
    return redirect()->route('project-statuses.index');
  }

  public function destroy(ProjectStatus $projectStatus)
  {
    $projectStatus->delete();
    event(
      new ActivityLogged(
        auth()->user(),
        'deleted_project_status',
        'Deleted a project status',
        $projectStatus
      )
    );
    return redirect()->route('project-statuses.index');
  }

  public function edit(ProjectStatus $projectStatus)
  {
    return Inertia::render('ProjectStatuses/Edit', compact('projectStatus'));
  }
}
