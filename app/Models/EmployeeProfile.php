<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeProfile extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'user_id',
    'employee_id',
    'base_hourly_rate',
    'payment_method',
    'hire_date',
    'termination_date',
    'is_active',
    // Add other fields as necessary
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'hire_date' => 'date',
    'termination_date' => 'date',
    'is_active' => 'boolean',
    'base_hourly_rate' => 'decimal:2',
  ];

  /**
   * The accessors to append to the model's array form.
   *
   * THIS IS THE CRUCIAL FIX.
   *
   * @var array
   */
  protected $appends = ['payment_method_text', 'status_text'];

  /**
   * Get the user that owns the employee profile.
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

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

  /**
   * Scope a query to only include active employees.
   */
  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }

  /**
   * Check if the employee is currently active.
   */
  public function isActiveEmployee(): bool
  {
    return $this->is_active && (!$this->termination_date || $this->termination_date->isFuture());
  }
}
