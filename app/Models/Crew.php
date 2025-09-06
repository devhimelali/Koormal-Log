<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crew extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * The labours that are part of this crew.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Labour>
     */
    public function labours(): HasMany
    {
        return $this->hasMany(Labour::class);
    }
}
