<?php

namespace App\Http\Controllers;

use App\Exports\DataTableExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ExportController extends Controller
{
  /**
   * Export data in the requested format
   */
  public function export(Request $request)
  {
    $validated = $request->validate([
      'format' => 'required|in:xlsx,csv,pdf',
      'data' => 'required|array',
      'columns' => 'required|array',
      'filename' => 'required|string',
      'title' => 'nullable|string',
    ]);

    $format = $validated['format'];
    $data = $validated['data'];
    $columns = $validated['columns'];
    $filename = Str::slug($validated['filename']);
    $title = $validated['title'] ?? $validated['filename'];

    // Remove columns that shouldn't be exported (like actions)
    $exportColumns = array_filter($columns, function ($column) {
      return !in_array($column['key'], ['actions', 'bulk_select']);
    });

    switch ($format) {
      case 'xlsx':
        return $this->exportExcel($data, $exportColumns, $filename, 'xlsx');

      case 'csv':
        return $this->exportExcel($data, $exportColumns, $filename, 'csv');

      case 'pdf':
        return $this->exportPdf($data, $exportColumns, $filename, $title);

      default:
        return response()->json(['error' => 'Invalid format'], 400);
    }
  }

  /**
   * Export to Excel (XLSX or CSV)
   */
  protected function exportExcel($data, $columns, $filename, $type)
  {
    $export = new DataTableExport($data, $columns);

    $extension = $type === 'csv' ? 'csv' : 'xlsx';
    $writerType = $type === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX;

    return Excel::download($export, "{$filename}.{$extension}", $writerType);
  }

  /**
   * Export to PDF
   */
  protected function exportPdf($data, $columns, $filename, $title)
  {
    // Prepare the data for PDF view
    $tableData = [];
    foreach ($data as $row) {
      $rowData = [];
      foreach ($columns as $column) {
        $value = $this->getCellValue($row, $column['key']);
        $rowData[] = $this->formatValue($value, $column['key']);
      }
      $tableData[] = $rowData;
    }

    $headings = array_map(function ($column) {
      return $column['label'];
    }, $columns);

    $pdf = Pdf::loadView('exports.datatable-pdf', [
      'title' => $title,
      'headings' => $headings,
      'data' => $tableData,
      'generatedAt' => now()->format('Y-m-d H:i:s'),
    ]);

    return $pdf->download("{$filename}.pdf");
  }

  protected function getCellValue($item, $key)
  {
    $keys = explode('.', $key);
    $value = is_array($item) ? $item : (array) $item;

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
              return $item['name'] ?? ($item['label'] ?? ($item['title'] ?? ''));
            } elseif (is_object($item)) {
              return $item->name ?? ($item->label ?? ($item->title ?? ''));
            }
            return $item;
          }, $value)
        );
      }

      // Simple array of values
      return implode(', ', array_filter($value));
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
        return '$' . number_format((float) $value, 2);
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

    return $value ?? '';
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
    $percentagePatterns = ['percent', 'percentage', 'multiplier'];

    foreach ($percentagePatterns as $pattern) {
      if (stripos($key, $pattern) !== false && !$this->isCurrencyField($key)) {
        return true;
      }
    }

    return false;
  }
}
