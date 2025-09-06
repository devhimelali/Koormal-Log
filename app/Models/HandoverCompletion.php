<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HandoverCompletion extends Model
{
    protected $fillable = [
        'log_date',
        'shift',
        'answers',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
        ];
    }


    /**
     * Gets the name of the supervisor who completed the handover for the given log date and shift.
     *
     * @return string The name of the supervisor, or 'N/A' if no supervisor can be found.
     */
    public function getSupervisorNameAttribute()
    {
        return Supervisor::where('date', $this->log_date)
            ->where('shift', $this->shift)
            ->value('name') ?? 'N/A';
    }
}
