<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EmployeeProfile extends Model
{
  use HasFactory, LogsActivity;

  protected $fillable = [
    'user_id',
    'employee_id',
    'base_hourly_rate',
    'overtime_rate_multiplier',
    'standard_hours_per_day',
    'standard_hours_per_week',
    'payment_method',
    'bank_account_number',
    'bank_name',
    'tax_id',
    'tax_exemptions',
    'is_active',
    'hire_date',
    'termination_date',
  ];

  protected $casts = [
    'base_hourly_rate' => 'decimal:2',
    'overtime_rate_multiplier' => 'decimal:2',
    'tax_exemptions' => 'array',
    'is_active' => 'boolean',
    'hire_date' => 'date',
    'termination_date' => 'date',
  ];

  // Relationships
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function timeEntries()
  {
    return $this->hasMany(TimeEntry::class, 'user_id', 'user_id');
  }

  public function payslips()
  {
    return $this->hasMany(Payslip::class, 'user_id', 'user_id');
  }

  // Business Logic Methods
  public function getOvertimeRate(): float
  {
    return $this->base_hourly_rate * $this->overtime_rate_multiplier;
  }

  public function getCurrentHourlyRate(): float
  {
    return $this->base_hourly_rate;
  }

  public function getMonthlyStandardHours(): int
  {
    // Approximate monthly hours based on weekly standard
    return intval($this->standard_hours_per_week * 4.33);
  }

  public function isActiveEmployee(): bool
  {
    return $this->is_active &&
      $this->hire_date <= now() &&
      ($this->termination_date === null || $this->termination_date > now());
  }

  // Scopes
  public function scopeActive($query)
  {
    return $query
      ->where('is_active', true)
      ->where('hire_date', '<=', now())
      ->where(function ($q) {
        $q->whereNull('termination_date')->orWhere('termination_date', '>', now());
      });
  }

  public function scopeInactive($query)
  {
    return $query
      ->where('is_active', false)
      ->orWhere('hire_date', '>', now())
      ->orWhere('termination_date', '<=', now());
  }

  // Generate unique employee ID if not provided
  protected static function boot()
  {
    parent::boot();

    static::creating(function ($employeeProfile) {
      if (empty($employeeProfile->employee_id)) {
        $employeeProfile->employee_id = 'EMP' . str_pad(static::max('id') + 1, 4, '0', STR_PAD_LEFT);
      }
    });
  }

  public function getActivitylogOptions(): LogOptions
  {
    return LogOptions::defaults()
      ->logOnly([
        'employee_id',
        'base_hourly_rate',
        'overtime_rate_multiplier',
        'is_active',
        'hire_date',
        'termination_date',
        'payment_method',
      ])
      ->logOnlyDirty()
      ->dontSubmitEmptyLogs()
      ->setDescriptionForEvent(fn(string $eventName) => "Employee profile has been {$eventName}");
  }

  /**
   * The accessors to append to the model's array form.
   *
   * THIS IS THE CRUCIAL FIX.
   *
   * @var array
   */
  protected $appends = ['payment_method_text', 'status_text'];

  /**
   * Get the translated text for the payment method.
   *
   * @return \Illuminate\Database\Eloquent\Casts\Attribute
   */
  protected function paymentMethodText(): Attribute
  {
    return Attribute::make(get: fn() => trans("payroll.payment_methods.{$this->payment_method}"));
  }

  /**
   * Get the translated text for the status.
   *
   * @return \Illuminate\Database\Eloquent\Casts\Attribute
   */
  protected function statusText(): Attribute
  {
    return Attribute::make(get: fn() => $this->is_active ? trans('words.active') : trans('words.inactive'));
  }
}
