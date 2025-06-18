<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SupervisorNote extends Model
{
    protected $fillable = [
        'note',
        'log_date',
        'note_type',
    ];

    /**
     * Get all media (files) associated with the supervisor note.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'imageable');
    }

}
