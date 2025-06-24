<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderMove extends Model
{
    protected $fillable = [
        'wo_number',
        'from_date',
        'from_shift',
        'to_date',
        'to_shift',
        'reason',
        'moved_by',
    ];
}
