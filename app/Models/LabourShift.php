<?php

namespace App\Models;

use App\Enum\ShiftEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabourShift extends Model
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


    /**
     * Get the labour associated with this labour shift.
     *
     * @return BelongsTo<Labour>
     */
    public function labour(): BelongsTo
    {
        return $this->belongsTo(Labour::class);
    }
}
