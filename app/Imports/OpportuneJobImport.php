<?php

namespace App\Imports;

use App\Models\OpportuneJob;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class OpportuneJobImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        if ($collection->isEmpty()) {
            throw new \Exception("Imported file is empty.");
        }

        //slice first row and last row
        $collection = $collection->slice(1, -1);

        foreach ($collection as $row) {
            OpportuneJob::create([
                'wo_number' => $row[1] ?? null,
                'asset_no' => $row[2] ?? null,
                'asset_description' => $row[4] ?? null,
                'work_description' => $row[3] ?? null,
                'status' => $row[5] ?? null,
                'due_start' => $row[6] ?? null,
                'job_type' => $row[8] ?? null,
                'priority' => $row[10] ?? null,
                'raised' => $row[11] ?? null,
                'start_date' => $row[12] ?? null,
                'duration' => $row[14] ?? null,
                'department' => $row[16] ?? null,
                'material_cost' => $row[18] ?? null,
                'other_cost' => $row[19] ?? null,
            ]);
        }
    }
}
