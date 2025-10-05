<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Campaign extends Model
{
  use HasFactory, Searchable, LogsActivity;

  protected $guarded = [];

  protected $casts = [
    'budget' => 'decimal:2',
    'actual_cost' => 'decimal:2',
    'revenue_generated' => 'decimal:2',
    'start_date' => 'date',
    'end_date' => 'date',
    'goals' => 'array',
    'channels' => 'array',
    'demographics' => 'array',
    'attachments' => 'array',
  ];

  protected $appends = ['roi', 'conversion_rate', 'cost_per_lead', 'is_active'];

  // Relationships
  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function contacts()
  {
    return $this->belongsToMany(Contact::class, 'campaign_contact')
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

  public function interactions()
  {
    return $this->hasMany(Interaction::class);
  }

  // Accessors
  public function getRoiAttribute()
  {
    if ($this->actual_cost == 0) {
      return 0;
    }
    return (($this->revenue_generated - $this->actual_cost) / $this->actual_cost) * 100;
  }

  public function getConversionRateAttribute()
  {
    if ($this->actual_audience_size == 0) {
      return 0;
    }
    return ($this->opportunities_created / $this->actual_audience_size) * 100;
  }

  public function getCostPerLeadAttribute()
  {
    if ($this->leads_generated == 0) {
      return 0;
    }
    return $this->actual_cost / $this->leads_generated;
  }

  public function getIsActiveAttribute()
  {
    return $this->status === 'active' && (!$this->end_date || $this->end_date >= now()->toDateString());
  }

  // Scopes
  public function scopeActive($query)
  {
    return $query->where('status', 'active');
  }

  public function scopeCompleted($query)
  {
    return $query->where('status', 'completed');
  }

  public function scopeByType($query, $type)
  {
    return $query->where('type', $type);
  }

  public function scopeInProgress($query)
  {
    return $query->where('status', 'active')->where(function ($q) {
      $q->whereNull('start_date')
        ->orWhere('start_date', '<=', now())
        ->whereNull('end_date')
        ->orWhere('end_date', '>=', now());
    });
  }

  public function scopeUpcoming($query)
  {
    return $query->where('start_date', '>', now());
  }

  public function scopeHighPerforming($query, $minRoi = 100)
  {
    return $query->whereRaw('(revenue_generated - actual_cost) / actual_cost * 100 >= ?', [$minRoi]);
  }

  // Business Logic Methods
  public function launch()
  {
    if ($this->status === 'draft') {
      $this->update([
        'status' => 'active',
        'start_date' => $this->start_date ?? now()->toDateString(),
      ]);

      activity()->performedOn($this)->log('Campaign launched');
    }

    return $this;
  }

  public function pause()
  {
    if ($this->status === 'active') {
      $this->update(['status' => 'paused']);

      activity()->performedOn($this)->log('Campaign paused');
    }

    return $this;
  }

  public function resume()
  {
    if ($this->status === 'paused') {
      $this->update(['status' => 'active']);

      activity()->performedOn($this)->log('Campaign resumed');
    }

    return $this;
  }

  public function complete()
  {
    $this->update([
      'status' => 'completed',
      'end_date' => $this->end_date ?? now()->toDateString(),
    ]);

    activity()->performedOn($this)->log('Campaign completed');

    return $this;
  }

  public function addContact($contact, $data = [])
  {
    if (!$this->contacts()->where('contact_id', $contact->id)->exists()) {
      $this->contacts()->attach($contact->id, $data);
      $this->increment('actual_audience_size');
    }

    return $this;
  }

  public function removeContact($contact)
  {
    if ($this->contacts()->detach($contact->id)) {
      $this->decrement('actual_audience_size');
    }

    return $this;
  }

  public function trackEmail($contact, $action, $data = [])
  {
    $pivot = $this->contacts()->where('contact_id', $contact->id)->first();

    if ($pivot) {
      $updateData = [];

      switch ($action) {
        case 'sent':
          $updateData = ['status' => 'sent', 'sent_at' => now()];
          break;
        case 'delivered':
          $updateData = ['status' => 'delivered', 'delivered_at' => now()];
          break;
        case 'opened':
          $updateData = [
            'status' => 'opened',
            'opened_at' => $pivot->pivot->opened_at ?? now(),
            'open_count' => $pivot->pivot->open_count + 1,
          ];
          break;
        case 'clicked':
          $updateData = [
            'status' => 'clicked',
            'clicked_at' => $pivot->pivot->clicked_at ?? now(),
            'click_count' => $pivot->pivot->click_count + 1,
          ];
          break;
        case 'responded':
          $updateData = ['status' => 'responded', 'responded_at' => now()];
          break;
        case 'unsubscribed':
          $updateData = ['status' => 'unsubscribed'];
          break;
        case 'bounced':
          $updateData = ['status' => 'bounced'];
          break;
      }

      if ($data) {
        $updateData['tracking_data'] = array_merge($pivot->pivot->tracking_data ?? [], $data);
      }

      $this->contacts()->updateExistingPivot($contact->id, $updateData);
    }

    return $this;
  }

  public function getEngagementMetrics()
  {
    $metrics = $this->contacts()
      ->selectRaw(
        '
                COUNT(*) as total_sent,
                SUM(CASE WHEN pivot.status IN ("delivered", "opened", "clicked", "responded") THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN pivot.status IN ("opened", "clicked", "responded") THEN 1 ELSE 0 END) as opened,
                SUM(CASE WHEN pivot.status IN ("clicked", "responded") THEN 1 ELSE 0 END) as clicked,
                SUM(CASE WHEN pivot.status = "responded" THEN 1 ELSE 0 END) as responded,
                SUM(CASE WHEN pivot.status = "unsubscribed" THEN 1 ELSE 0 END) as unsubscribed,
                SUM(CASE WHEN pivot.status = "bounced" THEN 1 ELSE 0 END) as bounced
            '
      )
      ->first();

    return [
      'total_sent' => $metrics->total_sent ?? 0,
      'delivered' => $metrics->delivered ?? 0,
      'opened' => $metrics->opened ?? 0,
      'clicked' => $metrics->clicked ?? 0,
      'responded' => $metrics->responded ?? 0,
      'unsubscribed' => $metrics->unsubscribed ?? 0,
      'bounced' => $metrics->bounced ?? 0,
      'delivery_rate' => $metrics->total_sent > 0 ? ($metrics->delivered / $metrics->total_sent) * 100 : 0,
      'open_rate' => $metrics->delivered > 0 ? ($metrics->opened / $metrics->delivered) * 100 : 0,
      'click_rate' => $metrics->opened > 0 ? ($metrics->clicked / $metrics->opened) * 100 : 0,
      'response_rate' => $metrics->total_sent > 0 ? ($metrics->responded / $metrics->total_sent) * 100 : 0,
    ];
  }

  public function getStatusColorAttribute()
  {
    return match ($this->status) {
      'draft' => '#6B7280',
      'active' => '#10B981',
      'paused' => '#F59E0B',
      'completed' => '#3B82F6',
      'cancelled' => '#EF4444',
      default => '#6B7280',
    };
  }

  public function getTypeColorAttribute()
  {
    return match ($this->type) {
      'email' => '#3B82F6',
      'social_media' => '#8B5CF6',
      'webinar' => '#10B981',
      'trade_show' => '#F59E0B',
      'direct_mail' => '#EF4444',
      'telemarketing' => '#6B7280',
      'other' => '#6B7280',
      default => '#6B7280',
    };
  }

  // Scout Configuration
  public function toSearchableArray()
  {
    return [
      'name' => $this->name,
      'description' => $this->description,
      'type' => $this->type,
      'status' => $this->status,
      'content' => $this->content,
    ];
  }

  public function shouldBeSearchable()
  {
    return $this->status !== 'cancelled';
  }

  // Activity Log Configuration
  public function getActivitylogOptions(): LogOptions
  {
    return LogOptions::defaults()
      ->logOnly([
        'name',
        'type',
        'status',
        'budget',
        'actual_cost',
        'start_date',
        'end_date',
        'leads_generated',
        'opportunities_created',
        'revenue_generated',
      ])
      ->logOnlyDirty()
      ->dontSubmitEmptyLogs()
      ->setDescriptionForEvent(fn(string $eventName) => "Campaign has been {$eventName}");
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($campaign) {
      $campaign->name = self::sanitize($campaign->name);
      $campaign->description = self::sanitize($campaign->description);
      $campaign->content = self::sanitize($campaign->content);
    });

    static::updating(function ($campaign) {
      $campaign->name = self::sanitize($campaign->name);
      $campaign->description = self::sanitize($campaign->description);
      $campaign->content = self::sanitize($campaign->content);
    });
  }

  protected static function sanitize($input)
  {
    return $input ? htmlspecialchars($input, ENT_QUOTES, 'UTF-8') : $input;
  }
}
