<?php

namespace App\Http\Controllers;

use App\Models\ProjectStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectStatusController extends Controller
{
  public function index()
  {
    $statuses = ProjectStatus::all();
    return Inertia::render('ProjectStatuses/Index', compact('statuses'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);
    ProjectStatus::create($request->all());
    return redirect()->route('project-statuses.index');
  }

  public function update(Request $request, ProjectStatus $projectStatus)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);
    $projectStatus->update($request->all());
    return redirect()->route('project-statuses.index');
  }

  public function destroy(ProjectStatus $projectStatus)
  {
    $projectStatus->delete();
    return redirect()->route('project-statuses.index');
  }

  public function edit(ProjectStatus $projectStatus)
  {
    return Inertia::render('ProjectStatuses/Edit', compact('projectStatus'));
  }
}
