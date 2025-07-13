<?php
namespace App\Http\Controllers;

use App\Events\ActivityLogged;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\TimeReport;
use App\Models\WorkLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TimeReportController extends Controller
{
  public function index(Request $request)
  {
    $query = TimeReport::with(['project', 'user'])
      ->forUser(auth()->id())
      ->latest();

    // Apply filters
    if ($request->has('project_id') && $request->project_id) {
      $query->forProject($request->project_id);
    }

    if ($request->has('period_type') && $request->period_type) {
      $query->byPeriodType($request->period_type);
    }

    if ($request->has('date_from') && $request->date_from) {
      $query->where('start_date', '>=', $request->date_from);
    }

    if ($request->has('date_to') && $request->date_to) {
      $query->where('end_date', '<=', $request->date_to);
    }

    $timeReports = $query->paginate(15);

    // Get projects for filters
    $projects = Project::orderBy('name')->get(['id', 'name']);

    $periodTypes = [
      'daily' => 'Daily',
      'weekly' => 'Weekly',
      'monthly' => 'Monthly',
      'custom' => 'Custom',
    ];

    return Inertia::render('TimeReports/Index', [
      'timeReports' => $timeReports,
      'projects' => $projects,
      'periodTypes' => $periodTypes,
      'filters' => $request->only(['project_id', 'period_type', 'date_from', 'date_to']),
    ]);
  }

  public function generate(Request $request)
  {
    $request->validate([
      'project_id' => 'required|exists:projects,id',
      'start_date' => 'required|date',
      'end_date' => 'required|date|after_or_equal:start_date',
      'period_type' => 'required|in:daily,weekly,monthly,custom',
    ]);

    $report = TimeReport::generateReport(
      auth()->id(),
      $request->project_id,
      $request->start_date,
      $request->end_date,
      $request->period_type
    );

    event(
      new ActivityLogged(
        auth()->user(),
        'generated_time_report',
        'Generated time report for project: ' . $report->project->name,
        $report
      )
    );

    return response()->json([
      'message' => 'Time report generated successfully',
      'report' => $report->load(['project']),
    ]);
  }

  public function show(TimeReport $timeReport)
  {
    if ($timeReport->user_id !== auth()->id()) {
      abort(403, 'Unauthorized');
    }

    $timeReport->load(['project', 'user']);

    return Inertia::render('TimeReports/Show', [
      'timeReport' => $timeReport,
    ]);
  }

  public function destroy(TimeReport $timeReport)
  {
    if ($timeReport->user_id !== auth()->id()) {
      return response()->json(['error' => 'Unauthorized'], 403);
    }

    $projectName = $timeReport->project->name;
    $timeReport->delete();

    event(
      new ActivityLogged(
        auth()->user(),
        'deleted_time_report',
        'Deleted time report for project: ' . $projectName,
        $timeReport
      )
    );

    return response()->json(['message' => 'Time report deleted successfully']);
  }

  public function getWeeklyReport(Request $request)
  {
    $request->validate([
      'project_id' => 'required|exists:projects,id',
      'week_start' => 'nullable|date',
    ]);

    $report = TimeReport::getWeeklyReport(auth()->id(), $request->project_id, $request->week_start);

    return response()->json([
      'report' => $report->load(['project']),
    ]);
  }

  public function getMonthlyReport(Request $request)
  {
    $request->validate([
      'project_id' => 'required|exists:projects,id',
      'month' => 'nullable|integer|min:1|max:12',
      'year' => 'nullable|integer|min:2020|max:2030',
    ]);

    $report = TimeReport::getMonthlyReport(auth()->id(), $request->project_id, $request->month, $request->year);

    return response()->json([
      'report' => $report->load(['project']),
    ]);
  }

  public function getDashboardSummary(Request $request)
  {
    $userId = auth()->id();
    $startDate = $request->has('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
    $endDate = $request->has('end_date') ? Carbon::parse($request->end_date) : Carbon::now();

    // Get work logs in period
    $workLogs = WorkLog::forUser($userId)
      ->inPeriod($startDate, $endDate)
      ->with(['task', 'project'])
      ->get();

    // Get time entries in period
    $timeEntries = TimeEntry::forUser($userId)
      ->inPeriod($startDate, $endDate)
      ->completed()
      ->with(['task', 'project'])
      ->get();

    // Calculate totals
    $totalWorkLogHours = $workLogs->sum('hours_worked');
    $totalTimeEntryHours = $timeEntries->sum('duration_minutes') / 60;
    $totalHours = $totalWorkLogHours + $totalTimeEntryHours;

    // Get project breakdown
    $projectBreakdown = collect();

    $workLogsByProject = $workLogs->groupBy('project_id');
    $timeEntriesByProject = $timeEntries->groupBy('project_id');

    $allProjectIds = $workLogsByProject->keys()->merge($timeEntriesByProject->keys())->unique();

    foreach ($allProjectIds as $projectId) {
      $workLogHours = $workLogsByProject->get($projectId, collect())->sum('hours_worked');
      $timeEntryHours = $timeEntriesByProject->get($projectId, collect())->sum('duration_minutes') / 60;

      $project =
        $workLogs->where('project_id', $projectId)->first()?->project ??
        $timeEntries->where('project_id', $projectId)->first()?->project;

      if ($project) {
        $projectBreakdown->push([
          'project_id' => $projectId,
          'project_name' => $project->name,
          'work_log_hours' => $workLogHours,
          'time_entry_hours' => $timeEntryHours,
          'total_hours' => $workLogHours + $timeEntryHours,
        ]);
      }
    }

    // Get work type breakdown
    $workTypeBreakdown = $workLogs->groupBy('work_type')->map(function ($logs) {
      return $logs->sum('hours_worked');
    });

    // Get current running timer
    $runningTimer = TimeEntry::forUser($userId)
      ->running()
      ->with(['task', 'project'])
      ->first();

    // Get recent activity
    $recentWorkLogs = WorkLog::forUser($userId)
      ->with(['task', 'project'])
      ->latest('date')
      ->limit(5)
      ->get();

    $recentTimeEntries = TimeEntry::forUser($userId)
      ->completed()
      ->with(['task', 'project'])
      ->latest('start_time')
      ->limit(5)
      ->get();

    return response()->json([
      'summary' => [
        'total_hours' => round($totalHours, 2),
        'work_log_hours' => round($totalWorkLogHours, 2),
        'time_entry_hours' => round($totalTimeEntryHours, 2),
        'total_entries' => $workLogs->count() + $timeEntries->count(),
        'project_breakdown' => $projectBreakdown->sortByDesc('total_hours')->values(),
        'work_type_breakdown' => $workTypeBreakdown,
        'running_timer' => $runningTimer,
        'recent_work_logs' => $recentWorkLogs,
        'recent_time_entries' => $recentTimeEntries,
      ],
      'period' => [
        'start' => $startDate->format('Y-m-d'),
        'end' => $endDate->format('Y-m-d'),
      ],
    ]);
  }

  public function exportReport(Request $request, TimeReport $timeReport)
  {
    if ($timeReport->user_id !== auth()->id()) {
      abort(403, 'Unauthorized');
    }

    $request->validate([
      'format' => 'required|in:csv,pdf,json',
    ]);

    $format = $request->format;
    $filename = "time_report_{$timeReport->id}_{$timeReport->start_date->format(
      'Y-m-d'
    )}_{$timeReport->end_date->format('Y-m-d')}.{$format}";

    switch ($format) {
      case 'csv':
        return $this->exportToCsv($timeReport, $filename);
      case 'pdf':
        return $this->exportToPdf($timeReport, $filename);
      case 'json':
        return $this->exportToJson($timeReport, $filename);
      default:
        return response()->json(['error' => 'Invalid format'], 400);
    }
  }

  private function exportToCsv(TimeReport $timeReport, $filename)
  {
    $headers = [
      'Content-Type' => 'text/csv',
      'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ];

    $callback = function () use ($timeReport) {
      $file = fopen('php://output', 'w');

      // Write headers
      fputcsv($file, ['Report Period', 'Project', 'Total Hours', 'Generated At']);
      fputcsv($file, [
        $timeReport->period_display,
        $timeReport->project->name,
        $timeReport->total_hours,
        $timeReport->created_at->format('Y-m-d H:i:s'),
      ]);

      fputcsv($file, []); // Empty row

      // Write work logs breakdown
      if (isset($timeReport->breakdown['work_logs']['by_task'])) {
        fputcsv($file, ['Task', 'Work Log Hours', 'Entries']);
        foreach ($timeReport->breakdown['work_logs']['by_task'] as $taskData) {
          fputcsv($file, [$taskData['task_name'], $taskData['hours'], $taskData['entries']]);
        }
      }

      fclose($file);
    };

    return response()->stream($callback, 200, $headers);
  }

  private function exportToPdf(TimeReport $timeReport, $filename)
  {
    // For PDF export, you would typically use a library like DomPDF or TCPDF
    // This is a simplified JSON response for now
    return response()->json([
      'message' => 'PDF export not implemented yet',
      'data' => $timeReport->toArray(),
    ]);
  }

  private function exportToJson(TimeReport $timeReport, $filename)
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ];

    return response()->json($timeReport->toArray(), 200, $headers);
  }
}
