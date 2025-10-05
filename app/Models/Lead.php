<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Lead extends Model
{
  use HasFactory, Searchable, LogsActivity;

  protected $guarded = [];

  protected $casts = [
    'custom_fields' => 'array',
    'last_contacted_at' => 'datetime',
    'converted_at' => 'datetime',
    'estimated_value' => 'decimal:2',
  ];

  protected $appends = ['full_name'];

  // Relationships
  public function assignedTo()
  {
    return $this->belongsTo(User::class, 'assigned_to');
  }

  public function convertedContact()
  {
    return $this->belongsTo(Contact::class, 'converted_contact_id');
  }

  public function interactions()
  {
    return $this->morphMany(Interaction::class, 'interactable');
  }

  // Accessors
  public function getFullNameAttribute()
  {
    return $this->first_name . ' ' . $this->last_name;
  }

  // Scopes
  public function scopeActive($query)
  {
    return $query->whereNotIn('status', ['converted', 'lost']);
  }

  public function scopeQualified($query)
  {
    return $query->where('status', 'qualified');
  }

  public function scopeBySource($query, $source)
  {
    return $query->where('source', $source);
  }

  public function scopeAssignedTo($query, $userId)
  {
    return $query->where('assigned_to', $userId);
  }

  public function scopeHighScore($query, $minScore = 70)
  {
    return $query->where('score', '>=', $minScore);
  }

  // Business Logic Methods
  public function convert($contactData = [])
  {
    $contact = Contact::create(
      array_merge(
        [
          'first_name' => $this->first_name,
          'last_name' => $this->last_name,
          'email' => $this->email,
          'phone' => $this->phone,
          'company' => $this->company,
          'job_title' => $this->job_title,
          'type' => 'customer',
          'assigned_to' => $this->assigned_to,
          'created_by' => auth()->id(),
        ],
        $contactData
      )
    );

    $this->update([
      'status' => 'converted',
      'converted_contact_id' => $contact->id,
      'converted_at' => now(),
    ]);

    return $contact;
  }

  public function updateScore($additionalPoints = 0)
  {
    $score = 0;

    // Basic information scoring
    if ($this->phone) {
      $score += 10;
    }
    if ($this->company) {
      $score += 15;
    }
    if ($this->job_title) {
      $score += 10;
    }

    // Engagement scoring
    if ($this->last_contacted_at) {
      $score += 20;
    }
    if ($this->interactions()->count() > 0) {
      $score += 15;
    }
    if ($this->interactions()->count() > 3) {
      $score += 10;
    }

    // Source scoring
    $sourceScores = [
      'referral' => 30,
      'website' => 20,
      'social_media' => 15,
      'email_campaign' => 10,
      'trade_show' => 25,
      'advertisement' => 5,
    ];
    $score += $sourceScores[$this->source] ?? 0;

    // Estimated value scoring
    if ($this->estimated_value) {
      if ($this->estimated_value >= 50000) {
        $score += 25;
      } elseif ($this->estimated_value >= 10000) {
        $score += 15;
      } elseif ($this->estimated_value >= 5000) {
        $score += 10;
      }
    }

    $this->update(['score' => min(100, $score + $additionalPoints)]);

    return $this->score;
  }

  public function getStatusColorAttribute()
  {
    return match ($this->status) {
      'new' => '#6B7280',
      'contacted' => '#3B82F6',
      'qualified' => '#10B981',
      'unqualified' => '#F59E0B',
      'converted' => '#059669',
      'lost' => '#EF4444',
      default => '#6B7280',
    };
  }

  // Scout Configuration
  public function toSearchableArray()
  {
    return [
      'first_name' => $this->first_name,
      'last_name' => $this->last_name,
      'email' => $this->email,
      'company' => $this->company,
      'job_title' => $this->job_title,
      'notes' => $this->notes,
    ];
  }

  public function shouldBeSearchable()
  {
    return $this->status !== 'lost';
  }

  // Activity Log Configuration
  public function getActivitylogOptions(): LogOptions
  {
    return LogOptions::defaults()
      ->logOnly([
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'job_title',
        'status',
        'source',
        'score',
        'estimated_value',
        'assigned_to',
      ])
      ->logOnlyDirty()
      ->dontSubmitEmptyLogs()
      ->setDescriptionForEvent(fn(string $eventName) => "Lead has been {$eventName}");
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($lead) {
      $lead->first_name = self::sanitize($lead->first_name);
      $lead->last_name = self::sanitize($lead->last_name);
      $lead->company = self::sanitize($lead->company);
      $lead->job_title = self::sanitize($lead->job_title);
      $lead->notes = self::sanitize($lead->notes);
    });

    static::updating(function ($lead) {
      $lead->first_name = self::sanitize($lead->first_name);
      $lead->last_name = self::sanitize($lead->last_name);
      $lead->company = self::sanitize($lead->company);
      $lead->job_title = self::sanitize($lead->job_title);
      $lead->notes = self::sanitize($lead->notes);
    });

    static::updated(function ($lead) {
      // Auto-update score when lead data changes
      if ($lead->isDirty(['phone', 'company', 'job_title', 'estimated_value'])) {
        $lead->updateScore();
      }
    });
  }

  protected static function sanitize($input)
  {
    return $input ? htmlspecialchars($input, ENT_QUOTES, 'UTF-8') : $input;
  }
}
