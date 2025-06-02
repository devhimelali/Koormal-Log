<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftLog extends Model
{
    protected $fillable = [
        'position',
        'shift_name',

        'wo_number',
        'work_description',
        'duration',
        'asset_no',
        'trades',
        'due_start',
        'status',
        'raised',
        'start_date',
        'priority',
        'job_type',
        'department',
        'material_cost',
        'labor_cost',
        'other_cost',
        'asset_description',

        'labour',
        'is_excel_upload', // if it is excel uploaded user can not edit
    ];

    public function scopeDayShift($query)
    {
        return $query->where('shift_name', 'Day');
    }

    public function scopeNightShift($query)
    {
        return $query->where('shift_name', 'Night');
    }
}
