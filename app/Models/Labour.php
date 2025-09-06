<?php

namespace App\Models;

use Carbon\Carbon;
use App\Enum\ShiftEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Labour extends Model
{
    protected $fillable = [
        'crew_id',
        'name',
    ];

    /**
     * The crew that this labour belongs to.
     *
     * @return BelongsTo<Crew>
     */
    public function crew(): BelongsTo
    {
        return $this->belongsTo(Crew::class);
    }

    /**
     * Get the labour shifts associated with this labour.
     *
     * @return HasMany<LabourShift>
     */
    public function labourShifts(): HasMany
    {
        return $this->hasMany(LabourShift::class);
    }
}
