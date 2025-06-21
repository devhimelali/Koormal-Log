<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'note',
        'sort_by',
    ];

    public function shiftLogs()
    {
        return $this->hasMany(ShiftLog::class);
    }
}
