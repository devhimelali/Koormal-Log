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


    public function getSupervisorNameAttribute()
    {
        return Supervisor::where('date', $this->log_date)
            ->where('shift', $this->shift)
            ->value('name') ?? 'N/A';
    }



    /**
     * Provides a set of default questions for handover completion.
     *
     * This function returns an array of predefined questions intended to
     * assist in the handover process. These questions ensure that key
     * areas such as workshop cleanliness, work order completion, parts
     * requests, and safety information are addressed between shifts.
     *
     * @return array An array of default questions for the handover process.
     */
    public static function defaultQuestions()
    {
        return [
            'Is the workshop clean and tidy for the next shift?',
            'Is the crib room clean and tidy?',
            'Have all finished work orders been completed in MEX and history updated?',
            'Have you read all work order attachments and raised the necessary work orders or work requests?',
            'Have all work order parts requests been viewed and noted on the main menu?',
            'Have all parts requests been assessed as valid and necessary?',
            'Is the workshop bus clean and full of fuel?',
            'Have you relayed all safety information to the oncoming shift supervisor?',
        ];
    }
}
