<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpportuneJob extends Model
{
    protected $fillable = [
        'wo_number',
        'asset_no',
        'asset_description',
        'work_description',
        'status',
        'due_start',
        'job_type',
        'priority',
        'raised',
        'start_date',
        'duration',
        'department',
        'material_cost',
        'other_cost',
    ];
}
