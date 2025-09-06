<?php

namespace App\Imports;

use App\Models\ShiftLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ShiftLogFromWorkOrderImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    public $log_date;

    public function __construct($log_date)
    {
        $this->log_date = $log_date;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $nextPosition = ShiftLog::max('position') + 1;

        foreach ($rows as $row) {
            // Skip entirely empty rows
            if (empty(array_filter($row->toArray()))) {
                continue;
            }

            // Default shift name
            $shiftName = 'day';

            // If work description contains "nightshift" in any case
            if (stripos($row['description'] ?? '', 'nightshift') !== false) {
                $shiftName = 'night';
            }

            ShiftLog::create([
                'shift_name' => $shiftName,
                'wo_number' => $row['wo_no'] ?? null,
                'work_description' => $row['description'] ?? null,
                'duration' => $row['duration'] ?? null,
                'asset_no' => $row['asset_no'] ?? null,
                'trades' => $row['trades'] ?? null, // inc
                'due_start' => $this->parseExcelDate($row['due_start']),
                'status' => $row['status'] ?? null,
                'raised' => $this->parseExcelDate($row['raised']),
                'start_date' => $this->parseExcelDate($row['start_date']),
                'priority' => $row['priority'] ?? null,
                'job_type' => $row['job_type'] ?? null,
                'department' => $row['department'] ?? null,
                'material_cost' => $row['material_costs'] ?? null,
                'labor_cost' => $row['labour_cost'] ?? null, // inc
                'other_cost' => $row['other_costs'] ?? null,
                'asset_description' => $row['asset_description'] ?? null,
                'is_excel_upload' => 1,
                'position' => $nextPosition++,
                'log_date' => $this->log_date,
                'scheduled' => 'yes',
            ]);
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 100;
    }

    private function parseExcelDate($value, $format = 'd-m-Y')
    {
        return isset($value)
            ? Carbon::instance(Date::excelToDateTimeObject((float) $value))->format($format)
            : null;
    }
}
