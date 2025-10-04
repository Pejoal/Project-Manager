<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Project extends Model
{
  use HasFactory, LogsActivity;

  protected $guarded = [];

  public function phases()
  {
    return $this->hasMany(Phase::class)->orderBy('order', 'ASC');
  }

  public function milestones()
  {
    return $this->hasMany(Milestone::class)->orderBy('order', 'ASC');
  }

  public function tasks()
  {
    return $this->hasMany(Task::class);
  }

  public function clients()
  {
    return $this->belongsToMany(Client::class);
  }

  public function status()
  {
    return $this->belongsTo(ProjectStatus::class);
  }

  public function priority()
  {
    return $this->belongsTo(ProjectPriority::class);
  }

  public function getActivitylogOptions(): LogOptions
  {
    return LogOptions::defaults()
      ->logOnly(['name', 'description', 'slug', 'status_id', 'priority_id', 'start_date', 'end_date'])
      ->logOnlyDirty()
      ->dontSubmitEmptyLogs()
      ->setDescriptionForEvent(fn(string $eventName) => "Project has been {$eventName}");
  }
}
