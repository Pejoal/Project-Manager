<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'task_id',
    'project_id',
    'start_time',
    'end_time',
    'description',
    'is_running',
    'duration_minutes',
  ];

  protected $casts = [
    'start_time' => 'datetime',
    'end_time' => 'datetime',
    'is_running' => 'boolean',
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
  public function scopeRunning($query)
  {
    return $query->where('is_running', true);
  }

  public function scopeCompleted($query)
  {
    return $query->where('is_running', false)->whereNotNull('end_time');
  }

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
    return $query->whereBetween('start_time', [$startDate, $endDate]);
  }

  // Mutators and Accessors
  public function getDurationAttribute()
  {
    if ($this->is_running) {
      return Carbon::parse($this->start_time)->diffInMinutes(now());
    }
    return $this->duration_minutes;
  }

  public function getFormattedDurationAttribute()
  {
    $minutes = $this->duration;
    $hours = floor($minutes / 60);
    $remainingMinutes = $minutes % 60;

    if ($hours > 0) {
      return sprintf('%dh %dm', $hours, $remainingMinutes);
    }
    return sprintf('%dm', $remainingMinutes);
  }

  // Methods
  public function stop()
  {
    if ($this->is_running) {
      $this->end_time = now();
      $this->is_running = false;
      $this->duration_minutes = Carbon::parse($this->start_time)->diffInMinutes($this->end_time);
      $this->save();
    }
  }

  public function start()
  {
    if (!$this->is_running) {
      $this->start_time = now();
      $this->end_time = null;
      $this->is_running = true;
      $this->duration_minutes = 0;
      $this->save();
    }
  }

  protected static function boot()
  {
    parent::boot();

    static::saving(function ($timeEntry) {
      if (!$timeEntry->is_running && $timeEntry->end_time) {
        $timeEntry->duration_minutes = Carbon::parse($timeEntry->start_time)->diffInMinutes($timeEntry->end_time);
      }
    });
  }
}
