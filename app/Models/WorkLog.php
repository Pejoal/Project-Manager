<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkLog extends Model
{
  use HasFactory;

  protected $fillable = ['user_id', 'task_id', 'project_id', 'date', 'hours_worked', 'description', 'work_type'];

  protected $casts = [
    'date' => 'date',
    'hours_worked' => 'decimal:2',
  ];

  // Relationships
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function task()
  {
    return $this->belongsTo(Task::class);
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

  public function scopeForTask($query, $taskId)
  {
    return $query->where('task_id', $taskId);
  }

  public function scopeInPeriod($query, $startDate, $endDate)
  {
    return $query->whereBetween('date', [$startDate, $endDate]);
  }

  public function scopeByWorkType($query, $workType)
  {
    return $query->where('work_type', $workType);
  }

  // Accessors
  public function getWorkTypeDisplayAttribute()
  {
    return ucfirst(str_replace('_', ' ', $this->work_type));
  }

  // Static methods
  public static function getTotalHoursForUser($userId, $startDate = null, $endDate = null)
  {
    $query = static::forUser($userId);

    if ($startDate && $endDate) {
      $query->inPeriod($startDate, $endDate);
    }

    return $query->sum('hours_worked');
  }

  public static function getTotalHoursForProject($projectId, $startDate = null, $endDate = null)
  {
    $query = static::forProject($projectId);

    if ($startDate && $endDate) {
      $query->inPeriod($startDate, $endDate);
    }

    return $query->sum('hours_worked');
  }

  public static function getTotalHoursForTask($taskId, $startDate = null, $endDate = null)
  {
    $query = static::forTask($taskId);

    if ($startDate && $endDate) {
      $query->inPeriod($startDate, $endDate);
    }

    return $query->sum('hours_worked');
  }

  public static function getWorkTypeBreakdown($userId, $startDate = null, $endDate = null)
  {
    $query = static::forUser($userId);

    if ($startDate && $endDate) {
      $query->inPeriod($startDate, $endDate);
    }

    return $query
      ->selectRaw('work_type, SUM(hours_worked) as total_hours')
      ->groupBy('work_type')
      ->get()
      ->pluck('total_hours', 'work_type');
  }
}
