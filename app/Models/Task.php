<?php
namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Task extends Model
{
  use HasFactory, Searchable;

  protected $guarded = [];

  public function project()
  {
    return $this->belongsTo(Project::class);
  }

  public function phase()
  {
    return $this->belongsTo(Phase::class);
  }

  public function milestone()
  {
    return $this->belongsTo(Milestone::class);
  }

  public function assignedTo()
  {
    return $this->belongsToMany(User::class, 'task_user');
  }

  public function status()
  {
    return $this->belongsTo(TaskStatus::class);
  }

  public function priority()
  {
    return $this->belongsTo(TaskPriority::class);
  }

  // Time tracking relationships
  public function timeEntries()
  {
    return $this->hasMany(TimeEntry::class);
  }

  public function workLogs()
  {
    return $this->hasMany(WorkLog::class);
  }

  // Time tracking methods
  public function getTotalTrackedHours()
  {
    $timeEntryHours = $this->timeEntries()->sum('duration_minutes') / 60;
    $workLogHours = $this->workLogs()->sum('hours_worked');
    return $timeEntryHours + $workLogHours;
  }

  public function getRunningTimeEntry()
  {
    return $this->timeEntries()->running()->first();
  }

  public function hasRunningTimer()
  {
    return $this->timeEntries()->running()->exists();
  }

  public function toSearchableArray()
  {
    return [
      'name' => $this->name,
      'description' => $this->description,
    ];
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($task) {
      $task->name = self::sanitize($task->name);
      $task->description = self::sanitize($task->description);
    });

    static::updating(function ($task) {
      $task->name = self::sanitize($task->name);
      $task->description = self::sanitize($task->description);
    });
  }

  protected static function sanitize($input)
  {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
  }
}
