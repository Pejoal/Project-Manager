<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxConfiguration extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'type',
    'rate',
    'brackets',
    'minimum_income',
    'maximum_income',
    'is_active',
    'applies_to',
    'priority',
    'description',
  ];

  protected $casts = [
    'rate' => 'decimal:4',
    'brackets' => 'array',
    'minimum_income' => 'decimal:2',
    'maximum_income' => 'decimal:2',
    'is_active' => 'boolean',
  ];

  // Business Logic Methods
  public function calculateTax(float $grossIncome): float
  {
    if (!$this->is_active || $grossIncome < $this->minimum_income) {
      return 0;
    }

    if ($this->maximum_income && $grossIncome > $this->maximum_income) {
      $grossIncome = $this->maximum_income;
    }

    switch ($this->type) {
      case 'flat':
        return $this->rate;

      case 'percentage':
        return round($grossIncome * ($this->rate / 100), 2);

      case 'progressive':
        return $this->calculateProgressiveTax($grossIncome);

      default:
        return 0;
    }
  }

  protected function calculateProgressiveTax(float $grossIncome): float
  {
    if (!$this->brackets || !is_array($this->brackets)) {
      return 0;
    }

    $totalTax = 0;
    $remainingIncome = $grossIncome;

    foreach ($this->brackets as $bracket) {
      if ($remainingIncome <= 0) {
        break;
      }

      $bracketMin = $bracket['min'] ?? 0;
      $bracketMax = $bracket['max'] ?? PHP_FLOAT_MAX;
      $bracketRate = $bracket['rate'] ?? 0;

      if ($grossIncome <= $bracketMin) {
        continue;
      }

      $taxableInThisBracket = min($remainingIncome, $bracketMax - $bracketMin);
      $taxForThisBracket = $taxableInThisBracket * ($bracketRate / 100);

      $totalTax += $taxForThisBracket;
      $remainingIncome -= $taxableInThisBracket;
    }

    return round($totalTax, 2);
  }

  public function isApplicableTo(string $employeeType): bool
  {
    return $this->applies_to === 'all' || $this->applies_to === $employeeType;
  }

  // Scopes
  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }

  public function scopeByPriority($query)
  {
    return $query->orderBy('priority', 'asc');
  }

  public function scopeForEmployeeType($query, string $employeeType)
  {
    return $query->where(function ($q) use ($employeeType) {
      $q->where('applies_to', 'all')->orWhere('applies_to', $employeeType);
    });
  }

  // Static method to calculate total taxes for an income
  public static function calculateTotalTaxes(float $grossIncome, string $employeeType = 'all'): array
  {
    $taxes = static::active()->forEmployeeType($employeeType)->byPriority()->get();

    $taxBreakdown = [];
    $totalTax = 0;

    foreach ($taxes as $taxConfig) {
      $taxAmount = $taxConfig->calculateTax($grossIncome);

      if ($taxAmount > 0) {
        $taxBreakdown[] = [
          'name' => $taxConfig->name,
          'type' => $taxConfig->type,
          'rate' => $taxConfig->rate,
          'amount' => $taxAmount,
        ];

        $totalTax += $taxAmount;
      }
    }

    return [
      'breakdown' => $taxBreakdown,
      'total' => round($totalTax, 2),
    ];
  }
}
