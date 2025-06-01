<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftLog extends Model
{
    protected $fillable = [
        'shift_name',
        'wo_number',
        'asset_no',
        'asset_description',
        'work_description',
        'labour',
        'duration',
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
        'is_excel_upload',
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
