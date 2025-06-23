<?php

namespace Database\Seeders;

use App\Models\HandoverCompletionQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HandoverCompletionQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            [
                'question' => 'Is the workshop bus clean and full of fuel?',
                'sort_by' => 1,
            ],
            [
                'question' => 'Is the workshop clean and tidy for the next shift?',
                'sort_by' => 2,
            ],
            [
                'question' => 'Have all parts requests submitted been assessed as valid and necessary?',
                'sort_by' => 3,
            ],
            [
                'question' => 'Is the crib room clean and tidy?',
                'sort_by' => 4,
            ],
            [
                'question' => 'Have all finished work orders been completed in MEX and history updated?',
                'sort_by' => 5,
            ],
            [
                'question' => 'Have you relayed all safety information to the oncoming shift supervisor?',
                'sort_by' => 6,
            ],
            [
                'question' => 'Have you read all work order attachments and raised the necessary work orders or work requests?',
                'sort_by' => 7,
            ],
            [
                'question' => 'Have all work order parts requests been viewed and noted on the main menu?',
                'sort_by' => 8,
            ]
        ];

        foreach ($questions as $question) {
            HandoverCompletionQuestion::create($question);
        }
    }
}
