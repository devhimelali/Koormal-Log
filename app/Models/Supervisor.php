<?php

namespace App\Models;

use App\Enum\ShiftEnum;
use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    protected $fillable = [
        'name',
        'date',
        'shift',
    ];

    protected function casts()
    {
        return [
            'shift' => ShiftEnum::class,
        ];
    }
}
