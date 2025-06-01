<?php

namespace App\Imports;

use App\Models\ShiftLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ShiftLogImport implements ToCollection
{
    /**
     * @param Collection $rows
     * @throws \Exception
     */
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw new \Exception("Imported file is empty.");
        }

        $filteredRows = $rows->slice(1);

        foreach ($filteredRows as $row) {
            ShiftLog::create([
                'shift_name' => null,
                'wo_number' => $row[0] ?? null,
                'asset_no' => $row[3] ?? null,
                'asset_description' => $row[15] ?? null,
                'work_description' => $row[1] ?? null,
                'labour' => $row[5] ?? null,
                'duration' => $row[2] ?? null,
                'trades' => $row[4] ?? null,
                'due_start' => $this->formatExcelDate($row[5] ?? null),
                'status' => $row[6] ?? null,
                'raised' => $this->formatExcelDate($row[7] ?? null),
                'start_date' => $this->formatExcelDate($row[8] ?? null),
                'priority' => $row[9] ?? null,
                'job_type' => $row[10] ?? null,
                'department' => $row[11] ?? null,
                'material_cost' => $row[12] ?? null,
                'labor_cost' => $row[13] ?? null,
                'other_cost' => $row[14] ?? null,
                'is_excel_upload' => 1,
            ]);
        }
    }

    protected function formatExcelDate($value)
    {
        if (!$value) {
            return null;
        }

        try {
            // Convert dd/mm/yyyy to Carbon and reformat to dd-mm-Y
            return Carbon::createFromFormat('d/m/Y', trim($value))->format('d-m-Y');
        } catch (\Exception $e) {
            // You can log or handle invalid dates here if needed
            return null;
        }
    }
}
