<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    protected $fillable = [
        'imageable_id',
        'imageable_type',
        'url',
        'level',
    ];

    /**
     * Get the parent imageable model (user, post, etc.).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
