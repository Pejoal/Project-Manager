<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TimeEntry extends Model
{
  use HasFactory, LogsActivity;

  protected $fillable = [
    'user_id',
    'task_id',
    'project_id',
    'start_datetime',
    'end_datetime',
    'hours_worked',
    'hourly_rate',
    'gross_amount',
    'description',
    'is_approved',
    'approved_by',
    'approved_at',
  ];

  protected $casts = [
    'start_datetime' => 'datetime',
    'end_datetime' => 'datetime',
    'approved_at' => 'datetime',
    'is_approved' => 'boolean',
    'hours_worked' => 'decimal:2',
    'hourly_rate' => 'decimal:2',
    'gross_amount' => 'decimal:2',
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

  public function approvedBy()
  {
    return $this->belongsTo(User::class, 'approved_by');
  }

  // Business Logic Methods
  public function calculateHours(): float
  {
    if (!$this->start_datetime || !$this->end_datetime) {
      return 0;
    }

    $start = Carbon::parse($this->start_datetime);
    $end = Carbon::parse($this->end_datetime);

    return round($end->diffInMinutes($start) / 60, 2);
  }

  public function calculateGrossAmount(): float
  {
    return round($this->hours_worked * $this->hourly_rate, 2);
  }

  public function isOvertime(): bool
  {
    $employeeProfile = $this->user->employeeProfile;
    if (!$employeeProfile) {
      return false;
    }

    // Check if daily hours exceed standard
    $dailyHours = static::where('user_id', $this->user_id)
      ->whereDate('start_datetime', $this->start_datetime->format('Y-m-d'))
      ->sum('hours_worked');

    return $dailyHours > $employeeProfile->standard_hours_per_day;
  }

  // Scopes
  public function scopeApproved($query)
  {
    return $query->where('is_approved', true);
  }

  public function scopePending($query)
  {
    return $query->where('is_approved', false);
  }

  public function scopeForPeriod($query, $startDate, $endDate)
  {
    return $query->whereBetween('start_datetime', [$startDate, $endDate]);
  }

  public function scopeForUser($query, $userId)
  {
    return $query->where('user_id', $userId);
  }

  // Automatically calculate hours and gross amount when saving
  protected static function boot()
  {
    parent::boot();

    static::saving(function ($timeEntry) {
      if ($timeEntry->start_datetime && $timeEntry->end_datetime) {
        $timeEntry->hours_worked = $timeEntry->calculateHours();
        $timeEntry->gross_amount = $timeEntry->calculateGrossAmount();
      }
    });
  }

  public function getActivitylogOptions(): LogOptions
  {
    return LogOptions::defaults()
      ->logOnly([
        'user_id',
        'task_id',
        'project_id',
        'start_datetime',
        'end_datetime',
        'hours_worked',
        'hourly_rate',
        'gross_amount',
        'is_approved',
      ])
      ->logOnlyDirty()
      ->dontSubmitEmptyLogs()
      ->setDescriptionForEvent(fn(string $eventName) => "Time entry has been {$eventName}");
  }
}
