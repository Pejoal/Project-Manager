<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Contact extends Model
{
  use HasFactory, Searchable, LogsActivity;

  protected $guarded = [];

  protected $casts = [
    'custom_fields' => 'array',
    'tags' => 'array',
    'communication_preferences' => 'array',
    'last_contacted_at' => 'datetime',
    'birthday' => 'date',
  ];

  protected $appends = ['full_name', 'full_address'];

  // Relationships
  public function assignedTo()
  {
    return $this->belongsTo(User::class, 'assigned_to');
  }

  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function opportunities()
  {
    return $this->hasMany(Opportunity::class);
  }

  public function supportTickets()
  {
    return $this->hasMany(SupportTicket::class);
  }

  public function interactions()
  {
    return $this->morphMany(Interaction::class, 'interactable');
  }

  public function campaigns()
  {
    return $this->belongsToMany(Campaign::class, 'campaign_contact')
      ->withPivot([
        'status',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'responded_at',
        'open_count',
        'click_count',
        'tracking_data',
      ])
      ->withTimestamps();
  }

  public function convertedFromLead()
  {
    return $this->hasOne(Lead::class, 'converted_contact_id');
  }

  // Accessors
  public function getFullNameAttribute()
  {
    return trim($this->first_name . ' ' . $this->last_name);
  }

  public function getFullAddressAttribute()
  {
    $address = collect([$this->address, $this->city, $this->state, $this->postal_code, $this->country])
      ->filter()
      ->implode(', ');

    return $address ?: null;
  }

  // Scopes
  public function scopeActive($query)
  {
    return $query->where('status', 'active');
  }

  public function scopeCustomers($query)
  {
    return $query->where('type', 'customer');
  }

  public function scopeProspects($query)
  {
    return $query->where('type', 'prospect');
  }

  public function scopeByType($query, $type)
  {
    return $query->where('type', $type);
  }

  public function scopeAssignedTo($query, $userId)
  {
    return $query->where('assigned_to', $userId);
  }

  public function scopeWithTag($query, $tag)
  {
    return $query->whereJsonContains('tags', $tag);
  }

  public function scopeCanContact($query)
  {
    return $query->where('status', '!=', 'do_not_contact');
  }

  public function scopeInLocation($query, $location)
  {
    return $query->where(function ($q) use ($location) {
      $q->where('city', 'like', "%{$location}%")
        ->orWhere('state', 'like', "%{$location}%")
        ->orWhere('country', 'like', "%{$location}%");
    });
  }

  // Business Logic Methods
  public function addTag($tag)
  {
    $tags = $this->tags ?? [];
    if (!in_array($tag, $tags)) {
      $tags[] = $tag;
      $this->update(['tags' => $tags]);
    }
    return $this;
  }

  public function removeTag($tag)
  {
    $tags = $this->tags ?? [];
    $tags = array_values(array_filter($tags, fn($t) => $t !== $tag));
    $this->update(['tags' => $tags]);
    return $this;
  }

  public function updateCommunicationPreference($channel, $preference)
  {
    $preferences = $this->communication_preferences ?? [];
    $preferences[$channel] = $preference;
    $this->update(['communication_preferences' => $preferences]);
    return $this;
  }

  public function canReceive($channel)
  {
    if ($this->status === 'do_not_contact') {
      return false;
    }

    $preferences = $this->communication_preferences ?? [];
    return $preferences[$channel] ?? true; // Default to true if not specified
  }

  public function getEngagementScore()
  {
    $score = 0;

    // Recent interactions boost score
    $recentInteractions = $this->interactions()
      ->where('interaction_date', '>=', now()->subDays(30))
      ->count();
    $score += min($recentInteractions * 10, 50);

    // Campaign engagement
    $campaignEngagement = $this->campaigns()->wherePivot('status', 'opened')->count();
    $score += min($campaignEngagement * 5, 25);

    // Support ticket activity
    $recentTickets = $this->supportTickets()
      ->where('created_at', '>=', now()->subDays(90))
      ->count();
    $score += min($recentTickets * 15, 25);

    return min($score, 100);
  }

  public function getTypeColorAttribute()
  {
    return match ($this->type) {
      'customer' => '#059669',
      'prospect' => '#3B82F6',
      'partner' => '#8B5CF6',
      'vendor' => '#F59E0B',
      'other' => '#6B7280',
      default => '#6B7280',
    };
  }

  public function getStatusColorAttribute()
  {
    return match ($this->status) {
      'active' => '#10B981',
      'inactive' => '#F59E0B',
      'do_not_contact' => '#EF4444',
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
      'phone' => $this->phone,
      'mobile' => $this->mobile,
      'company' => $this->company,
      'job_title' => $this->job_title,
      'department' => $this->department,
      'city' => $this->city,
      'state' => $this->state,
      'country' => $this->country,
      'notes' => $this->notes,
      'tags' => $this->tags,
    ];
  }

  public function shouldBeSearchable()
  {
    return $this->status !== 'do_not_contact';
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
        'mobile',
        'company',
        'job_title',
        'type',
        'status',
        'assigned_to',
        'tags',
      ])
      ->logOnlyDirty()
      ->dontSubmitEmptyLogs()
      ->setDescriptionForEvent(fn(string $eventName) => "Contact has been {$eventName}");
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($contact) {
      $contact->first_name = self::sanitize($contact->first_name);
      $contact->last_name = self::sanitize($contact->last_name);
      $contact->company = self::sanitize($contact->company);
      $contact->job_title = self::sanitize($contact->job_title);
      $contact->department = self::sanitize($contact->department);
      $contact->notes = self::sanitize($contact->notes);
    });

    static::updating(function ($contact) {
      $contact->first_name = self::sanitize($contact->first_name);
      $contact->last_name = self::sanitize($contact->last_name);
      $contact->company = self::sanitize($contact->company);
      $contact->job_title = self::sanitize($contact->job_title);
      $contact->department = self::sanitize($contact->department);
      $contact->notes = self::sanitize($contact->notes);
    });
  }

  protected static function sanitize($input)
  {
    return $input ? htmlspecialchars($input, ENT_QUOTES, 'UTF-8') : $input;
  }
}
