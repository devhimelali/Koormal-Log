<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ShiftLog extends Model
{
    protected $fillable = [
        'position',
        'shift_name',

        'wo_number',
        'work_description',
        'duration',
        'asset_no',
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
        'asset_description',
        'supervisor_notes',
        'mark_as_complete',
        'progress',
        'note_id',

        'labour',
        'is_excel_upload', // if it is excel uploaded user can not edit
    ];

    public function scopeDayShift($query)
    {
        return $query->where('shift_name', 'Day');
    }

    public function scopeNightShift($query)
    {
        return $query->where('shift_name', 'Night');
    }

    /**
     * Get all media (files) associated with the shift log.
     *
     * @return MorphMany
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'imageable');
    }

    public function note()
    {
        return $this->belongsTo(Note::class);
    }
}
