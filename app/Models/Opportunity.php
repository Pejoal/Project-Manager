<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Opportunity extends Model
{
  use HasFactory, Searchable, LogsActivity;

  protected $guarded = [];

  protected $casts = [
    'value' => 'decimal:2',
    'expected_close_date' => 'date',
    'actual_close_date' => 'date',
    'competitors' => 'array',
  ];

  protected $appends = ['weighted_value', 'is_overdue', 'days_to_close'];

  // Relationships
  public function contact()
  {
    return $this->belongsTo(Contact::class);
  }

  public function assignedTo()
  {
    return $this->belongsTo(User::class, 'assigned_to');
  }

  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function interactions()
  {
    return $this->morphMany(Interaction::class, 'interactable');
  }

  // Accessors
  public function getWeightedValueAttribute()
  {
    return $this->value * ($this->probability / 100);
  }

  public function getIsOverdueAttribute()
  {
    return $this->expected_close_date < now() && !in_array($this->stage, ['closed_won', 'closed_lost']);
  }

  public function getDaysToCloseAttribute()
  {
    if (in_array($this->stage, ['closed_won', 'closed_lost'])) {
      return null;
    }
    return now()->diffInDays($this->expected_close_date, false);
  }

  // Scopes
  public function scopeOpen($query)
  {
    return $query->whereNotIn('stage', ['closed_won', 'closed_lost']);
  }

  public function scopeWon($query)
  {
    return $query->where('stage', 'closed_won');
  }

  public function scopeLost($query)
  {
    return $query->where('stage', 'closed_lost');
  }

  public function scopeByStage($query, $stage)
  {
    return $query->where('stage', $stage);
  }

  public function scopeAssignedTo($query, $userId)
  {
    return $query->where('assigned_to', $userId);
  }

  public function scopeOverdue($query)
  {
    return $query->where('expected_close_date', '<', now())->whereNotIn('stage', ['closed_won', 'closed_lost']);
  }

  public function scopeClosingSoon($query, $days = 30)
  {
    return $query
      ->whereBetween('expected_close_date', [now(), now()->addDays($days)])
      ->whereNotIn('stage', ['closed_won', 'closed_lost']);
  }

  public function scopeHighValue($query, $minValue = 10000)
  {
    return $query->where('value', '>=', $minValue);
  }

  public function scopeHighProbability($query, $minProbability = 70)
  {
    return $query->where('probability', '>=', $minProbability);
  }

  // Business Logic Methods
  public function updateStage($newStage, $data = [])
  {
    $oldStage = $this->stage;

    $updateData = array_merge(['stage' => $newStage], $data);

    // Auto-update probability based on stage
    $stageProbabilities = [
      'prospecting' => 10,
      'qualification' => 25,
      'needs_analysis' => 40,
      'proposal' => 60,
      'negotiation' => 80,
      'closed_won' => 100,
      'closed_lost' => 0,
    ];

    if (!isset($data['probability']) && isset($stageProbabilities[$newStage])) {
      $updateData['probability'] = $stageProbabilities[$newStage];
    }

    // Set close date for closed opportunities
    if (in_array($newStage, ['closed_won', 'closed_lost']) && !$this->actual_close_date) {
      $updateData['actual_close_date'] = now();
    }

    $this->update($updateData);

    // Log stage change
    activity()
      ->performedOn($this)
      ->withProperties([
        'old_stage' => $oldStage,
        'new_stage' => $newStage,
        'probability' => $this->probability,
      ])
      ->log('Opportunity stage changed');

    return $this;
  }

  public function win($data = [])
  {
    return $this->updateStage('closed_won', $data);
  }

  public function lose($reason = null, $data = [])
  {
    $updateData = $data;
    if ($reason) {
      $updateData['loss_reason'] = $reason;
    }
    return $this->updateStage('closed_lost', $updateData);
  }

  public function addCompetitor($competitor)
  {
    $competitors = $this->competitors ?? [];
    if (!in_array($competitor, $competitors)) {
      $competitors[] = $competitor;
      $this->update(['competitors' => $competitors]);
    }
    return $this;
  }

  public function removeCompetitor($competitor)
  {
    $competitors = $this->competitors ?? [];
    $competitors = array_values(array_filter($competitors, fn($c) => $c !== $competitor));
    $this->update(['competitors' => $competitors]);
    return $this;
  }

  public function getStageColorAttribute()
  {
    return match ($this->stage) {
      'prospecting' => '#6B7280',
      'qualification' => '#3B82F6',
      'needs_analysis' => '#8B5CF6',
      'proposal' => '#F59E0B',
      'negotiation' => '#EF4444',
      'closed_won' => '#10B981',
      'closed_lost' => '#6B7280',
      default => '#6B7280',
    };
  }

  public function getTypeColorAttribute()
  {
    return match ($this->type) {
      'new_business' => '#10B981',
      'existing_business' => '#3B82F6',
      'renewal' => '#F59E0B',
      default => '#6B7280',
    };
  }

  public function getProbabilityColorAttribute()
  {
    if ($this->probability >= 80) {
      return '#10B981';
    }
    if ($this->probability >= 60) {
      return '#F59E0B';
    }
    if ($this->probability >= 40) {
      return '#EF4444';
    }
    return '#6B7280';
  }

  // Scout Configuration
  public function toSearchableArray()
  {
    return [
      'name' => $this->name,
      'description' => $this->description,
      'stage' => $this->stage,
      'type' => $this->type,
      'lead_source' => $this->lead_source,
      'next_steps' => $this->next_steps,
      'loss_reason' => $this->loss_reason,
    ];
  }

  public function shouldBeSearchable()
  {
    return true;
  }

  // Activity Log Configuration
  public function getActivitylogOptions(): LogOptions
  {
    return LogOptions::defaults()
      ->logOnly([
        'name',
        'value',
        'probability',
        'stage',
        'expected_close_date',
        'actual_close_date',
        'type',
        'assigned_to',
        'next_steps',
        'loss_reason',
      ])
      ->logOnlyDirty()
      ->dontSubmitEmptyLogs()
      ->setDescriptionForEvent(fn(string $eventName) => "Opportunity has been {$eventName}");
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($opportunity) {
      $opportunity->name = self::sanitize($opportunity->name);
      $opportunity->description = self::sanitize($opportunity->description);
      $opportunity->next_steps = self::sanitize($opportunity->next_steps);
      $opportunity->loss_reason = self::sanitize($opportunity->loss_reason);
    });

    static::updating(function ($opportunity) {
      $opportunity->name = self::sanitize($opportunity->name);
      $opportunity->description = self::sanitize($opportunity->description);
      $opportunity->next_steps = self::sanitize($opportunity->next_steps);
      $opportunity->loss_reason = self::sanitize($opportunity->loss_reason);
    });
  }

  protected static function sanitize($input)
  {
    return $input ? htmlspecialchars($input, ENT_QUOTES, 'UTF-8') : $input;
  }
}
