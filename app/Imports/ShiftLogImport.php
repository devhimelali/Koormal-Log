<?php

namespace App\Imports;

use App\Models\ShiftLog;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ShiftLogImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    /**
     * @param Collection $rows
     */
    protected $shiftName;
    public function __construct($shiftName)
    {
        $this->shiftName = $shiftName;
    }
    public function collection(Collection $rows)
    {
        $total = $rows->count();
        foreach ($rows as $index => $row) {
            if ($index === $total - 1) {
                continue;
            }
            ShiftLog::create([
                'shift_name'          => $this->shiftName,
                'wo_number'           => $row['wo_no'] ?? null,
                'work_description'    => $row['description'] ?? null,
                'duration'            => $row['duration'] ?? null,
                'asset_no'            => $row['asset_no'] ?? null,
                'trades'              => $row['trades'] ?? null,
                'due_start'           => $row['due_start'] ?? null,
                'status'              => $row['work_order_status_description'] ?? null,
                'raised'              => $row['raised'] ?? null,
                'start_date'          => $row['start_date'] ?? null,
                'priority'            => $row['priority'] ?? null,
                'job_type'            => $row['job_type'] ?? null,
                'department'          => $row['department'] ?? null,
                'material_cost'       => $row['materials_cost'] ?? null,
                'labor_cost'          => $row['labour_cost'] ?? null,
                'other_cost'          => $row['other_cost'] ?? null,
                'asset_description'   => $row['asset_description'] ?? null,
                'is_excel_upload'     => 1
                // 'labour' and 'is_excel_upload' can be added here if needed
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
}
