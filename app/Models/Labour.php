<?php

namespace App\Models;

use Carbon\Carbon;
use App\Enum\ShiftEnum;
use Illuminate\Database\Eloquent\Model;

class Labour extends Model
{
    protected $fillable = [
        'name',
        'date',
        'shift'
    ];

    protected function casts()
    {
        return [
            'shift' => ShiftEnum::class,
        ];
    }
}
