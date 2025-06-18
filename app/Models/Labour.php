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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string> An array where the key is the attribute name and the value is the type to cast to.
     */
    protected function casts(): array
    {
        return [
            'shift' => ShiftEnum::class,
        ];
    }
}
