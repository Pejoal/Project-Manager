<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class DataTableExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
  protected $data;
  protected $columns;
  protected $headings;

  public function __construct($data, $columns)
  {
    $this->data = $data;
    $this->columns = $columns;
    $this->headings = collect($columns)->pluck('label')->toArray();
  }

  public function collection()
  {
    return collect($this->data);
  }

  public function headings(): array
  {
    return $this->headings;
  }

  public function map($row): array
  {
    $mapped = [];
    foreach ($this->columns as $column) {
      $value = $this->getCellValue($row, $column['key']);
      $mapped[] = $this->formatValue($value, $column['key']);
    }
    return $mapped;
  }

  public function styles(Worksheet $sheet)
  {
    return [
      1 => ['font' => ['bold' => true]],
    ];
  }

  protected function getCellValue($item, $key)
  {
    $keys = explode('.', $key);
    $value = $item;

    foreach ($keys as $k) {
      if (is_array($value)) {
        $value = $value[$k] ?? null;
      } elseif (is_object($value)) {
        $value = $value->$k ?? null;
      } else {
        return null;
      }

      if ($value === null) {
        break;
      }
    }

    return $value;
  }

  protected function formatValue($value, $key = '')
  {
    // Handle null values
    if ($value === null) {
      return '';
    }

    // Handle boolean values
    if (is_bool($value)) {
      return $value ? 'Yes' : 'No';
    }

    // Handle arrays (for relationships or multiple values)
    if (is_array($value)) {
      // If it's an array of objects/arrays with 'name' property (relationships)
      if (isset($value[0]) && (is_array($value[0]) || is_object($value[0]))) {
        return implode(
          ', ',
          array_map(function ($item) {
            if (is_array($item)) {
              return $item['name'] ?? ($item['label'] ?? ($item['title'] ?? json_encode($item)));
            } elseif (is_object($item)) {
              return $item->name ?? ($item->label ?? ($item->title ?? (string) $item));
            }
            return $item;
          }, $value)
        );
      }

      // Simple array of values
      return implode(', ', $value);
    }

    // Handle objects (relationships)
    if (is_object($value)) {
      // Try to get a meaningful display value
      if (isset($value->name)) {
        return $value->name;
      } elseif (isset($value->label)) {
        return $value->label;
      } elseif (isset($value->title)) {
        return $value->title;
      } elseif (method_exists($value, '__toString')) {
        return (string) $value;
      }

      // For date objects
      if ($value instanceof \DateTime || $value instanceof Carbon) {
        return $value->format('Y-m-d H:i:s');
      }

      return '';
    }

    // Handle date strings
    if ($this->isDateField($key) && $this->isValidDate($value)) {
      try {
        $date = Carbon::parse($value);
        // Format based on if it has time component
        if ($date->format('H:i:s') !== '00:00:00') {
          return $date->format('Y-m-d H:i');
        }
        return $date->format('Y-m-d');
      } catch (\Exception $e) {
        return $value;
      }
    }

    // Handle numeric values
    if (is_numeric($value)) {
      // Check if it's a currency field
      if ($this->isCurrencyField($key)) {
        return number_format((float) $value, 2);
      }

      // Check if it's a percentage field
      if ($this->isPercentageField($key)) {
        return number_format((float) $value, 2) . '%';
      }

      return $value;
    }

    // Strip HTML tags for text fields
    if (is_string($value)) {
      $cleaned = strip_tags($value);
      // Decode HTML entities
      $cleaned = html_entity_decode($cleaned, ENT_QUOTES | ENT_HTML5, 'UTF-8');
      return trim($cleaned);
    }

    return $value;
  }

  /**
   * Check if a field is likely a date field based on key name
   */
  protected function isDateField($key)
  {
    $datePatterns = [
      'date',
      'datetime',
      '_at',
      'time',
      'created',
      'updated',
      'deleted',
      'published',
      'start',
      'end',
      'due',
      'birth',
      'hire',
      'termination',
      'pay_period',
    ];

    foreach ($datePatterns as $pattern) {
      if (stripos($key, $pattern) !== false) {
        return true;
      }
    }

    return false;
  }

  /**
   * Check if a value is a valid date
   */
  protected function isValidDate($value)
  {
    if (!is_string($value)) {
      return false;
    }

    try {
      $date = Carbon::parse($value);
      return $date->year >= 1900 && $date->year <= 2100;
    } catch (\Exception $e) {
      return false;
    }
  }

  /**
   * Check if a field is likely a currency field
   */
  protected function isCurrencyField($key)
  {
    $currencyPatterns = ['amount', 'price', 'cost', 'total', 'salary', 'rate', 'pay', 'earning', 'gross', 'net', 'tax'];

    foreach ($currencyPatterns as $pattern) {
      if (stripos($key, $pattern) !== false) {
        return true;
      }
    }

    return false;
  }

  /**
   * Check if a field is likely a percentage field
   */
  protected function isPercentageField($key)
  {
    $percentagePatterns = ['percent', 'percentage', 'rate', 'multiplier'];

    foreach ($percentagePatterns as $pattern) {
      if (stripos($key, $pattern) !== false && !$this->isCurrencyField($key)) {
        return true;
      }
    }

    return false;
  }
}
