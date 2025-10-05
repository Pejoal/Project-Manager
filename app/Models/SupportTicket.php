<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SupportTicket extends Model
{
  use HasFactory, Searchable, LogsActivity;

  protected $guarded = [];

  protected $casts = [
    'tags' => 'array',
    'custom_fields' => 'array',
    'first_response_at' => 'datetime',
    'resolved_at' => 'datetime',
    'closed_at' => 'datetime',
  ];

  protected $appends = ['response_time_hours', 'resolution_time_hours', 'is_overdue'];

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
  public function getResponseTimeHoursAttribute()
  {
    if (!$this->first_response_at) {
      return null;
    }
    return $this->created_at->diffInHours($this->first_response_at);
  }

  public function getResolutionTimeHoursAttribute()
  {
    if (!$this->resolved_at) {
      return null;
    }
    return $this->created_at->diffInHours($this->resolved_at);
  }

  public function getIsOverdueAttribute()
  {
    if (in_array($this->status, ['resolved', 'closed'])) {
      return false;
    }

    // Define SLA targets based on priority (in hours)
    $slaTargets = [
      'urgent' => 2,
      'high' => 8,
      'medium' => 24,
      'low' => 72,
    ];

    $targetHours = $slaTargets[$this->priority] ?? 24;
    $hoursOpen = $this->created_at->diffInHours(now());

    return $hoursOpen > $targetHours;
  }

  // Scopes
  public function scopeOpen($query)
  {
    return $query->whereNotIn('status', ['resolved', 'closed']);
  }

  public function scopeResolved($query)
  {
    return $query->where('status', 'resolved');
  }

  public function scopeClosed($query)
  {
    return $query->where('status', 'closed');
  }

  public function scopeByStatus($query, $status)
  {
    return $query->where('status', $status);
  }

  public function scopeByPriority($query, $priority)
  {
    return $query->where('priority', $priority);
  }

  public function scopeByType($query, $type)
  {
    return $query->where('type', $type);
  }

  public function scopeAssignedTo($query, $userId)
  {
    return $query->where('assigned_to', $userId);
  }

  public function scopeUnassigned($query)
  {
    return $query->whereNull('assigned_to');
  }

  public function scopeOverdue($query)
  {
    return $query
      ->where(function ($q) {
        $q->where('priority', 'urgent')
          ->where('created_at', '<', now()->subHours(2))
          ->orWhere(function ($sq) {
            $sq->where('priority', 'high')->where('created_at', '<', now()->subHours(8));
          })
          ->orWhere(function ($sq) {
            $sq->where('priority', 'medium')->where('created_at', '<', now()->subHours(24));
          })
          ->orWhere(function ($sq) {
            $sq->where('priority', 'low')->where('created_at', '<', now()->subHours(72));
          });
      })
      ->whereNotIn('status', ['resolved', 'closed']);
  }

  public function scopeWithTag($query, $tag)
  {
    return $query->whereJsonContains('tags', $tag);
  }

  // Business Logic Methods
  public function assign($userId)
  {
    $this->update(['assigned_to' => $userId]);

    activity()
      ->performedOn($this)
      ->withProperties(['assigned_to' => $userId])
      ->log('Ticket assigned');

    return $this;
  }

  public function updateStatus($newStatus, $notes = null)
  {
    $oldStatus = $this->status;
    $updateData = ['status' => $newStatus];

    // Set timestamps based on status
    switch ($newStatus) {
      case 'in_progress':
        if (!$this->first_response_at) {
          $updateData['first_response_at'] = now();
        }
        break;
      case 'resolved':
        if (!$this->resolved_at) {
          $updateData['resolved_at'] = now();
        }
        break;
      case 'closed':
        if (!$this->closed_at) {
          $updateData['closed_at'] = now();
        }
        if (!$this->resolved_at) {
          $updateData['resolved_at'] = now();
        }
        break;
    }

    $this->update($updateData);

    // Log status change
    activity()
      ->performedOn($this)
      ->withProperties([
        'old_status' => $oldStatus,
        'new_status' => $newStatus,
        'notes' => $notes,
      ])
      ->log('Ticket status updated');

    return $this;
  }

  public function resolve($notes = null)
  {
    return $this->updateStatus('resolved', $notes);
  }

  public function close($notes = null)
  {
    return $this->updateStatus('closed', $notes);
  }

  public function reopen($notes = null)
  {
    $this->update([
      'status' => 'open',
      'resolved_at' => null,
      'closed_at' => null,
    ]);

    activity()
      ->performedOn($this)
      ->withProperties(['notes' => $notes])
      ->log('Ticket reopened');

    return $this;
  }

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

  public function rateSatisfaction($rating, $comment = null)
  {
    $this->update([
      'satisfaction_rating' => $rating,
      'satisfaction_comment' => $comment,
    ]);

    activity()
      ->performedOn($this)
      ->withProperties([
        'rating' => $rating,
        'comment' => $comment,
      ])
      ->log('Customer satisfaction rating added');

    return $this;
  }

  public function getStatusColorAttribute()
  {
    return match ($this->status) {
      'open' => '#EF4444',
      'in_progress' => '#F59E0B',
      'pending_customer' => '#8B5CF6',
      'resolved' => '#10B981',
      'closed' => '#6B7280',
      default => '#6B7280',
    };
  }

  public function getPriorityColorAttribute()
  {
    return match ($this->priority) {
      'urgent' => '#EF4444',
      'high' => '#F59E0B',
      'medium' => '#3B82F6',
      'low' => '#10B981',
      default => '#6B7280',
    };
  }

  public function getTypeColorAttribute()
  {
    return match ($this->type) {
      'bug' => '#EF4444',
      'feature_request' => '#10B981',
      'question' => '#3B82F6',
      'complaint' => '#F59E0B',
      'other' => '#6B7280',
      default => '#6B7280',
    };
  }

  // Scout Configuration
  public function toSearchableArray()
  {
    return [
      'ticket_number' => $this->ticket_number,
      'subject' => $this->subject,
      'description' => $this->description,
      'status' => $this->status,
      'priority' => $this->priority,
      'type' => $this->type,
      'tags' => $this->tags,
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
      ->logOnly(['subject', 'status', 'priority', 'type', 'assigned_to', 'satisfaction_rating', 'tags'])
      ->logOnlyDirty()
      ->dontSubmitEmptyLogs()
      ->setDescriptionForEvent(fn(string $eventName) => "Support ticket has been {$eventName}");
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($ticket) {
      // Generate unique ticket number
      if (!$ticket->ticket_number) {
        $ticket->ticket_number =
          'TK-' .
          now()->format('Y') .
          '-' .
          str_pad(static::whereYear('created_at', now()->year)->count() + 1, 6, '0', STR_PAD_LEFT);
      }

      $ticket->subject = self::sanitize($ticket->subject);
      $ticket->description = self::sanitize($ticket->description);
    });

    static::updating(function ($ticket) {
      $ticket->subject = self::sanitize($ticket->subject);
      $ticket->description = self::sanitize($ticket->description);
    });
  }

  protected static function sanitize($input)
  {
    return $input ? htmlspecialchars($input, ENT_QUOTES, 'UTF-8') : $input;
  }
}
