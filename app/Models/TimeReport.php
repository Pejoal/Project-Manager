<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeReport extends Model
{
  use HasFactory;

  protected $fillable = ['user_id', 'project_id', 'start_date', 'end_date', 'total_hours', 'breakdown', 'period_type'];

  protected $casts = [
    'start_date' => 'datetime',
    'end_date' => 'datetime',
    'total_hours' => 'decimal:2',
    'breakdown' => 'array',
  ];

  // Relationships
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function project()
  {
    return $this->belongsTo(Project::class);
  }

  // Scopes
  public function scopeForUser($query, $userId)
  {
    return $query->where('user_id', $userId);
  }

  public function scopeForProject($query, $projectId)
  {
    return $query->where('project_id', $projectId);
  }

  public function scopeByPeriodType($query, $periodType)
  {
    return $query->where('period_type', $periodType);
  }

  // Static methods for generating reports
  public static function generateReport($userId, $projectId, $startDate, $endDate, $periodType = 'custom')
  {
    $workLogsQuery = WorkLog::forUser($userId)
      ->inPeriod($startDate, $endDate)
      ->with(['task', 'project']);
    $timeEntriesQuery = TimeEntry::forUser($userId)
      ->inPeriod($startDate, $endDate)
      ->completed()
      ->with(['task', 'project']);

    // If projectId is provided, filter by project
    if ($projectId) {
      $workLogsQuery->forProject($projectId);
      $timeEntriesQuery->forProject($projectId);
    }

    $workLogs = $workLogsQuery->get();
    $timeEntries = $timeEntriesQuery->get();

    $totalHours = $workLogs->sum('hours_worked');
    $totalTimeEntryHours = $timeEntries->sum('duration_minutes') / 60;

    $breakdown = [
      'work_logs' => [
        'total_hours' => $totalHours,
        'by_task' => $workLogs->groupBy('task_id')->map(function ($logs) {
          return [
            'task_name' => $logs->first()->task->name,
            'hours' => $logs->sum('hours_worked'),
            'entries' => $logs->count(),
          ];
        }),
        'by_work_type' => $workLogs->groupBy('work_type')->map(function ($logs) {
          return $logs->sum('hours_worked');
        }),
        'by_date' => $workLogs
          ->groupBy(function ($workLog) {
            return $workLog->date->format('Y-m-d');
          })
          ->map(function ($logs) {
            return $logs->sum('hours_worked');
          }),
      ],
      'time_entries' => [
        'total_hours' => $totalTimeEntryHours,
        'by_task' => $timeEntries->groupBy('task_id')->map(function ($entries) {
          return [
            'task_name' => $entries->first()->task->name,
            'hours' => $entries->sum('duration_minutes') / 60,
            'entries' => $entries->count(),
          ];
        }),
        'by_date' => $timeEntries
          ->groupBy(function ($entry) {
            return $entry->start_time->format('Y-m-d');
          })
          ->map(function ($entries) {
            return $entries->sum('duration_minutes') / 60;
          }),
      ],
    ];

    // If no specific project, use the first project found or create a virtual "All Projects" entry
    $finalProjectId = $projectId;
    if (!$projectId) {
      $firstProject = $workLogs->first()?->project ?? $timeEntries->first()?->project;
      if ($firstProject) {
        $finalProjectId = $firstProject->id;
      } else {
        // No data found, create a dummy project entry
        $finalProjectId = Project::first()?->id ?? 1;
      }
    }

    return static::create([
      'user_id' => $userId,
      'project_id' => $finalProjectId,
      'start_date' => $startDate,
      'end_date' => $endDate,
      'total_hours' => $totalHours + $totalTimeEntryHours,
      'breakdown' => $breakdown,
      'period_type' => $periodType,
    ]);
  }

  public static function getWeeklyReport($userId, $projectId, $weekStart = null)
  {
    $weekStart = $weekStart ? Carbon::parse($weekStart) : Carbon::now()->startOfWeek();
    $weekEnd = $weekStart->copy()->endOfWeek();

    return static::generateReport($userId, $projectId, $weekStart, $weekEnd, 'weekly');
  }

  public static function getMonthlyReport($userId, $projectId, $month = null, $year = null)
  {
    $month = $month ?: Carbon::now()->month;
    $year = $year ?: Carbon::now()->year;

    $monthStart = Carbon::create($year, $month, 1);
    $monthEnd = $monthStart->copy()->endOfMonth();

    return static::generateReport($userId, $projectId, $monthStart, $monthEnd, 'monthly');
  }

  // Accessors
  public function getPeriodDisplayAttribute()
  {
    return $this->start_date->format('M d, Y') . ' - ' . $this->end_date->format('M d, Y');
  }

  public function getFormattedTotalHoursAttribute()
  {
    $hours = floor((float) $this->total_hours);
    $minutes = ((float) $this->total_hours - $hours) * 60;

    if ($hours > 0) {
      return sprintf('%dh %dm', $hours, $minutes);
    }
    return sprintf('%dm', $minutes);
  }
}
