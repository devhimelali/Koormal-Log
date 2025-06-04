<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\ShiftLog;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ShiftLogCsvExport implements FromCollection, WithHeadings
{
    protected $date;
    protected $shift;

    public function __construct($date, $shift = null)
    {
        $this->date = $date;
        $this->shift = $shift;
    }

    public function collection()
    {
        $queryDate = Carbon::createFromFormat('d-m-Y', $this->date)->format('Y-m-d');
        $query = ShiftLog::whereDate('created_at', $queryDate);

        if ($this->shift !== null && $this->shift != 'both') {
            $query->where('shift_name', $this->shift);
        }


        return $query->get([
            'shift_name',
            'wo_number',
            'asset_no',
            'work_description',
            'labour',
            'progress',
            'priority',
            'department',
            'duration',
            'asset_description',
            'supervisor_notes',
        ]);
    }

    public function headings(): array
    {
        return [
            'Shift Name',
            'Wo Number',
            'Asset No',
            'Work Description',
            'Labour',
            'Completed (%)',
            'Priority',
            'Department',
            'Duration',
            'Asset Description',
            'Supervisor Notes',
        ];
    }
}
