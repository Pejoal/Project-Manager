<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Interaction extends Model
{
  use HasFactory, Searchable, LogsActivity;

  protected $guarded = [];

  protected $casts = [
    'interaction_date' => 'datetime',
    'follow_up_date' => 'datetime',
    'attendees' => 'array',
    'attachments' => 'array',
  ];

  // Relationships
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function campaign()
  {
    return $this->belongsTo(Campaign::class);
  }

  public function interactable()
  {
    return $this->morphTo();
  }

  // Scopes
  public function scopeByType($query, $type)
  {
    return $query->where('type', $type);
  }

  public function scopeByDirection($query, $direction)
  {
    return $query->where('direction', $direction);
  }

  public function scopeByUser($query, $userId)
  {
    return $query->where('user_id', $userId);
  }

  public function scopeSuccessful($query)
  {
    return $query->where('outcome', 'successful');
  }

  public function scopeNeedsFollowUp($query)
  {
    return $query->whereNotNull('follow_up_date')->where('follow_up_date', '<=', now());
  }

  public function scopeUpcomingFollowUps($query, $days = 7)
  {
    return $query->whereNotNull('follow_up_date')->whereBetween('follow_up_date', [now(), now()->addDays($days)]);
  }

  public function scopeRecent($query, $days = 30)
  {
    return $query->where('interaction_date', '>=', now()->subDays($days));
  }

  public function scopeForEntity($query, $entityType, $entityId)
  {
    return $query->where('interactable_type', $entityType)->where('interactable_id', $entityId);
  }

  // Business Logic Methods
  public function markCompleted($outcome = 'successful', $notes = null)
  {
    $this->update([
      'outcome' => $outcome,
      'follow_up_notes' => $notes,
    ]);

    return $this;
  }

  public function scheduleFollowUp($date, $notes = null)
  {
    $this->update([
      'follow_up_date' => $date,
      'follow_up_notes' => $notes,
    ]);

    return $this;
  }

  public function addAttendee($attendee)
  {
    $attendees = $this->attendees ?? [];
    if (!in_array($attendee, $attendees)) {
      $attendees[] = $attendee;
      $this->update(['attendees' => $attendees]);
    }
    return $this;
  }

  public function removeAttendee($attendee)
  {
    $attendees = $this->attendees ?? [];
    $attendees = array_values(array_filter($attendees, fn($a) => $a !== $attendee));
    $this->update(['attendees' => $attendees]);
    return $this;
  }

  public function addAttachment($attachment)
  {
    $attachments = $this->attachments ?? [];
    $attachments[] = $attachment;
    $this->update(['attachments' => $attachments]);
    return $this;
  }

  public function getTypeColorAttribute()
  {
    return match ($this->type) {
      'call' => '#3B82F6',
      'email' => '#10B981',
      'meeting' => '#8B5CF6',
      'task' => '#F59E0B',
      'note' => '#6B7280',
      'sms' => '#EF4444',
      'chat' => '#06B6D4',
      'social_media' => '#8B5CF6',
      'other' => '#6B7280',
      default => '#6B7280',
    };
  }

  public function getDirectionColorAttribute()
  {
    return match ($this->direction) {
      'inbound' => '#10B981',
      'outbound' => '#3B82F6',
      default => '#6B7280',
    };
  }

  public function getOutcomeColorAttribute()
  {
    return match ($this->outcome) {
      'successful' => '#10B981',
      'no_answer' => '#F59E0B',
      'busy' => '#EF4444',
      'voicemail' => '#8B5CF6',
      'email_bounced' => '#EF4444',
      'meeting_scheduled' => '#10B981',
      'follow_up_required' => '#F59E0B',
      'other' => '#6B7280',
      default => '#6B7280',
    };
  }

  public function getDurationFormattedAttribute()
  {
    if (!$this->duration_minutes) {
      return null;
    }

    $hours = intval($this->duration_minutes / 60);
    $minutes = $this->duration_minutes % 60;

    if ($hours > 0) {
      return "{$hours}h {$minutes}m";
    }

    return "{$minutes}m";
  }

  // Scout Configuration
  public function toSearchableArray()
  {
    return [
      'subject' => $this->subject,
      'description' => $this->description,
      'type' => $this->type,
      'direction' => $this->direction,
      'outcome' => $this->outcome,
      'follow_up_notes' => $this->follow_up_notes,
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
        'type',
        'direction',
        'subject',
        'interaction_date',
        'duration_minutes',
        'outcome',
        'follow_up_date',
        'interactable_type',
        'interactable_id',
      ])
      ->logOnlyDirty()
      ->dontSubmitEmptyLogs()
      ->setDescriptionForEvent(fn(string $eventName) => "Interaction has been {$eventName}");
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($interaction) {
      $interaction->subject = self::sanitize($interaction->subject);
      $interaction->description = self::sanitize($interaction->description);
      $interaction->follow_up_notes = self::sanitize($interaction->follow_up_notes);
    });

    static::updating(function ($interaction) {
      $interaction->subject = self::sanitize($interaction->subject);
      $interaction->description = self::sanitize($interaction->description);
      $interaction->follow_up_notes = self::sanitize($interaction->follow_up_notes);
    });
  }

  protected static function sanitize($input)
  {
    return $input ? htmlspecialchars($input, ENT_QUOTES, 'UTF-8') : $input;
  }
}
