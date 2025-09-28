<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSettings extends Model
{
  use HasFactory;

  protected $fillable = [
    'company_name',
    'company_address',
    'company_tax_id',
    'pay_period',
    'pay_day',
    'default_hourly_rate',
    'working_days',
    'standard_start_time',
    'standard_end_time',
    'break_duration_minutes',
    'auto_calculate_overtime',
    'require_approval_for_overtime',
    'auto_generate_time_entries',
    'currency',
    'timezone',
  ];

  protected $casts = [
    'default_hourly_rate' => 'decimal:2',
    'working_days' => 'array',
    'standard_start_time' => 'datetime:H:i:s',
    'standard_end_time' => 'datetime:H:i:s',
    'auto_calculate_overtime' => 'boolean',
    'require_approval_for_overtime' => 'boolean',
    'auto_generate_time_entries' => 'boolean',
  ];

  // Business Logic Methods
  public function getWorkingDaysArray(): array
  {
    return $this->working_days ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
  }

  public function getStandardWorkingHours(): float
  {
    $start = \Carbon\Carbon::parse($this->standard_start_time);
    $end = \Carbon\Carbon::parse($this->standard_end_time);
    $breakMinutes = $this->break_duration_minutes ?? 0;

    $totalMinutes = $end->diffInMinutes($start) - $breakMinutes;
    return round($totalMinutes / 60, 2);
  }

  public function isWorkingDay(string $dayName): bool
  {
    return in_array(strtolower($dayName), $this->getWorkingDaysArray());
  }

  public function getNextPayDate(): \Carbon\Carbon
  {
    $today = now();

    switch ($this->pay_period) {
      case 'weekly':
        // Pay day is day of week (0 = Sunday, 1 = Monday, etc.)
        return $today->next($this->pay_day);

      case 'bi_weekly':
        // Every two weeks from a reference date
        $referenceDate = $today->startOfYear()->next($this->pay_day);
        while ($referenceDate->isPast()) {
          $referenceDate->addWeeks(2);
        }
        return $referenceDate;

      case 'monthly':
      default:
        // Pay day is day of month
        $nextPayDate = $today->copy()->day($this->pay_day);
        if ($nextPayDate->isPast()) {
          $nextPayDate->addMonth();
        }
        return $nextPayDate;
    }
  }

  // Singleton pattern - only one settings record
  public static function current()
  {
    return static::first() ??
      static::create([
        'company_name' => config('app.name', 'Company'),
        'pay_period' => 'monthly',
        'pay_day' => 30,
        'default_hourly_rate' => 15.0,
      ]);
  }
}
