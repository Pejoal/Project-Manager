<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Payslip extends Model
{
  use HasFactory, LogsActivity;

  protected $fillable = [
    'user_id',
    'payslip_number',
    'pay_period_start',
    'pay_period_end',
    'pay_date',
    'regular_hours',
    'overtime_hours',
    'regular_rate',
    'overtime_rate',
    'gross_regular_pay',
    'gross_overtime_pay',
    'gross_total_pay',
    'tax_deductions',
    'total_tax_deductions',
    'other_deductions',
    'total_other_deductions',
    'net_pay',
    'bonuses',
    'total_bonuses',
    'status',
    'generated_by',
    'approved_by',
    'approved_at',
    'metadata',
  ];

  protected $casts = [
    'pay_period_start' => 'date',
    'pay_period_end' => 'date',
    'pay_date' => 'date',
    'regular_hours' => 'decimal:2',
    'overtime_hours' => 'decimal:2',
    'regular_rate' => 'decimal:2',
    'overtime_rate' => 'decimal:2',
    'gross_regular_pay' => 'decimal:2',
    'gross_overtime_pay' => 'decimal:2',
    'gross_total_pay' => 'decimal:2',
    'tax_deductions' => 'array',
    'total_tax_deductions' => 'decimal:2',
    'other_deductions' => 'array',
    'total_other_deductions' => 'decimal:2',
    'net_pay' => 'decimal:2',
    'bonuses' => 'array',
    'total_bonuses' => 'decimal:2',
    'approved_at' => 'datetime',
    'metadata' => 'array',
  ];

  // Relationships
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function generatedBy()
  {
    return $this->belongsTo(User::class, 'generated_by');
  }

  public function approvedBy()
  {
    return $this->belongsTo(User::class, 'approved_by');
  }

  // Business Logic Methods
  public function getTotalHours(): float
  {
    return $this->regular_hours + $this->overtime_hours;
  }

  public function calculateNetPay(): float
  {
    return $this->gross_total_pay + $this->total_bonuses - $this->total_tax_deductions - $this->total_other_deductions;
  }

  public function getEffectiveHourlyRate(): float
  {
    $totalHours = $this->getTotalHours();
    return $totalHours > 0 ? $this->gross_total_pay / $totalHours : 0;
  }

  public function isPaid(): bool
  {
    return $this->status === 'paid';
  }

  public function isApproved(): bool
  {
    return in_array($this->status, ['approved', 'paid']);
  }

  public function isDraft(): bool
  {
    return $this->status === 'draft';
  }

  // Scopes
  public function scopeDraft($query)
  {
    return $query->where('status', 'draft');
  }

  public function scopeApproved($query)
  {
    return $query->whereIn('status', ['approved', 'paid']);
  }

  public function scopePaid($query)
  {
    return $query->where('status', 'paid');
  }

  public function scopeForPeriod($query, $startDate, $endDate)
  {
    return $query
      ->whereBetween('pay_period_start', [$startDate, $endDate])
      ->orWhereBetween('pay_period_end', [$startDate, $endDate]);
  }

  // Generate unique payslip number
  protected static function boot()
  {
    parent::boot();

    static::creating(function ($payslip) {
      if (empty($payslip->payslip_number)) {
        $year = $payslip->pay_period_start ? $payslip->pay_period_start->format('Y') : now()->format('Y');
        $month = $payslip->pay_period_start ? $payslip->pay_period_start->format('m') : now()->format('m');
        $count = static::whereYear('pay_period_start', $year)->whereMonth('pay_period_start', $month)->count() + 1;

        $payslip->payslip_number = "PAY-{$year}{$month}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
      }
    });

    static::saving(function ($payslip) {
      // Recalculate net pay when saving
      $payslip->net_pay = $payslip->calculateNetPay();
    });
  }

  public function getActivitylogOptions(): LogOptions
  {
    return LogOptions::defaults()
      ->logOnly(['user_id', 'pay_period_start', 'pay_period_end', 'status', 'gross_total_pay', 'net_pay'])
      ->logOnlyDirty()
      ->dontSubmitEmptyLogs()
      ->setDescriptionForEvent(fn(string $eventName) => "Payslip has been {$eventName}");
  }
}
