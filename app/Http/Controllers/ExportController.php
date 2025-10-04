<?php

namespace App\Http\Controllers;

use App\Exports\DataTableExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ExportController extends Controller
{
    /**
     * Export data in the requested format
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv,pdf',
            'data' => 'required|array',
            'columns' => 'required|array',
            'filename' => 'required|string',
            'title' => 'nullable|string',
        ]);

        $format = $request->format;
        $data = $request->data;
        $columns = $request->columns;
        $filename = Str::slug($request->filename);
        $title = $request->title ?? $request->filename;

        // Remove columns that shouldn't be exported (like actions)
        $exportColumns = array_filter($columns, function($column) {
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
                $rowData[] = $this->formatValue($value);
            }
            $tableData[] = $rowData;
        }

        $headings = array_map(function($column) {
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

    protected function formatValue($value)
    {
        if (is_array($value)) {
            return implode(', ', array_map(function($item) {
                return is_array($item) ? ($item['name'] ?? json_encode($item)) : $item;
            }, $value));
        }

        if (is_object($value)) {
            return $value->name ?? (method_exists($value, '__toString') ? (string) $value : '');
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        return $value ?? '';
    }
}
