<?php
namespace App\Http\Controllers;

use App\Events\ActivityLogged;
use App\Models\Project;
use App\Models\Task;
use App\Models\WorkLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkLogController extends Controller
{
  public function index(Request $request)
  {
    $query = WorkLog::with(['task', 'project', 'user'])
      ->forUser(auth()->id())
      ->latest('date');

    // Apply filters
    if ($request->has('project_id') && $request->project_id) {
      $query->forProject($request->project_id);
    }

    if ($request->has('task_id') && $request->task_id) {
      $query->forTask($request->task_id);
    }

    if ($request->has('work_type') && $request->work_type) {
      $query->byWorkType($request->work_type);
    }

    if ($request->has('date_from') && $request->date_from) {
      $query->where('date', '>=', $request->date_from);
    }

    if ($request->has('date_to') && $request->date_to) {
      $query->where('date', '<=', $request->date_to);
    }

    $workLogs = $query->paginate(15);

    // Get projects and tasks for filters
    $projects = Project::orderBy('name')->get(['id', 'name']);
    $tasks = Task::with('project:id,name')->orderBy('name')->get();

    // Get work type options
    $workTypes = [
      'development' => 'Development',
      'design' => 'Design',
      'testing' => 'Testing',
      'meeting' => 'Meeting',
      'research' => 'Research',
      'documentation' => 'Documentation',
      'other' => 'Other',
    ];

    // Get today's work logs summary
    $todaysLogs = WorkLog::forUser(auth()->id())->where('date', today())->get();

    $todaysSummary = [
      'total_hours' => $todaysLogs->sum('hours_worked'),
      'total_entries' => $todaysLogs->count(),
      'by_work_type' => $todaysLogs->groupBy('work_type')->map(function ($logs) {
        return $logs->sum('hours_worked');
      }),
    ];

    return Inertia::render('WorkLogs/Index', [
      'workLogs' => $workLogs,
      'projects' => $projects,
      'tasks' => $tasks,
      'workTypes' => $workTypes,
      'todaysSummary' => $todaysSummary,
      'filters' => $request->only(['project_id', 'task_id', 'work_type', 'date_from', 'date_to']),
    ]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'task_id' => 'required|exists:tasks,id',
      'date' => 'required|date',
      'hours_worked' => 'required|numeric|min:0.1|max:24',
      'description' => 'nullable|string|max:1000',
      'work_type' => 'required|in:development,design,testing,meeting,research,documentation,other',
    ]);

    $task = Task::findOrFail($request->task_id);

    // Check if work log already exists for this user, task, and date
    $existingLog = WorkLog::forUser(auth()->id())
      ->forTask($request->task_id)
      ->where('date', $request->date)
      ->first();

    if ($existingLog) {
      return back()->withErrors([
        'task_id' => 'Work log already exists for this task and date. Please update the existing entry.',
      ]);
    }

    $workLog = WorkLog::create([
      'user_id' => auth()->id(),
      'task_id' => $task->id,
      'project_id' => $task->project_id,
      'date' => $request->date,
      'hours_worked' => $request->hours_worked,
      'description' => $request->description,
      'work_type' => $request->work_type,
    ]);

    event(
      new ActivityLogged(
        auth()->user(),
        'created_work_log',
        'Logged ' . $request->hours_worked . ' hours for task: ' . $task->name,
        $workLog
      )
    );

    session()->flash('flash.banner', 'Work log created successfully!');
    session()->flash('flash.bannerStyle', 'success');
  }

  public function update(Request $request, WorkLog $workLog)
  {
    if ($workLog->user_id !== auth()->id()) {
      abort(403, 'Unauthorized');
    }

    $request->validate([
      'hours_worked' => 'required|numeric|min:0.1|max:24',
      'description' => 'nullable|string|max:1000',
      'work_type' => 'required|in:development,design,testing,meeting,research,documentation,other',
    ]);

    $workLog->update([
      'hours_worked' => $request->hours_worked,
      'description' => $request->description,
      'work_type' => $request->work_type,
    ]);

    event(
      new ActivityLogged(
        auth()->user(),
        'updated_work_log',
        'Updated work log for task: ' . $workLog->task->name,
        $workLog
      )
    );

    return redirect()->route('work-logs.index')->with('success', 'Work log updated successfully');
  }

  public function destroy(WorkLog $workLog)
  {
    if ($workLog->user_id !== auth()->id()) {
      abort(403, 'Unauthorized');
    }

    $taskName = $workLog->task->name;
    $workLog->delete();

    event(new ActivityLogged(auth()->user(), 'deleted_work_log', 'Deleted work log for task: ' . $taskName, $workLog));

    return redirect()->route('work-logs.index')->with('success', 'Work log deleted successfully');
  }

  public function getWorkLogsByDate(Request $request)
  {
    $request->validate([
      'date' => 'required|date',
    ]);

    $workLogs = WorkLog::forUser(auth()->id())
      ->where('date', $request->date)
      ->with(['task', 'project'])
      ->get();

    return response()->json(['workLogs' => $workLogs]);
  }

  public function getWeeklySummary(Request $request)
  {
    $weekStart = $request->has('week_start')
      ? Carbon::parse($request->week_start)->startOfWeek()
      : Carbon::now()->startOfWeek();

    $weekEnd = $weekStart->copy()->endOfWeek();

    $workLogs = WorkLog::forUser(auth()->id())
      ->inPeriod($weekStart, $weekEnd)
      ->with(['task', 'project'])
      ->get();

    $summary = [
      'total_hours' => $workLogs->sum('hours_worked'),
      'total_entries' => $workLogs->count(),
      'by_project' => $workLogs->groupBy('project_id')->map(function ($logs) {
        return [
          'project_name' => $logs->first()->project->name,
          'hours' => $logs->sum('hours_worked'),
          'entries' => $logs->count(),
        ];
      }),
      'by_work_type' => $workLogs->groupBy('work_type')->map(function ($logs) {
        return $logs->sum('hours_worked');
      }),
      'by_day' => $workLogs->groupBy('date')->map(function ($logs) {
        return $logs->sum('hours_worked');
      }),
    ];

    return response()->json([
      'summary' => $summary,
      'period' => [
        'start' => $weekStart->format('Y-m-d'),
        'end' => $weekEnd->format('Y-m-d'),
      ],
    ]);
  }
}
