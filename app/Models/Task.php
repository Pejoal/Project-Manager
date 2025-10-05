<?php
namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Task extends Model
{
  use HasFactory, Searchable, LogsActivity;

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

  // Payroll relationships
  public function timeEntries()
  {
    return $this->hasMany(TimeEntry::class);
  }

  // Attachment relationships
  public function attachments()
  {
    return $this->hasMany(TaskAttachment::class)->latest();
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

  public function getActivitylogOptions(): LogOptions
  {
    return LogOptions::defaults()
      ->logOnly([
        'name',
        'description',
        'status_id',
        'priority_id',
        'phase_id',
        'milestone_id',
        'start_datetime',
        'end_datetime',
      ])
      ->logOnlyDirty()
      ->dontSubmitEmptyLogs()
      ->setDescriptionForEvent(fn(string $eventName) => "Task has been {$eventName}");
  }
}
