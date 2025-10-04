<?php


namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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
            $mapped[] = $this->formatValue($value);
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

    protected function formatValue($value)
    {
        if (is_array($value)) {
            return implode(', ', array_map(function($item) {
                return is_array($item) ? ($item['name'] ?? json_encode($item)) : $item;
            }, $value));
        }

        if (is_object($value)) {
            return $value->name ?? json_encode($value);
        }

        return $value;
    }
}
