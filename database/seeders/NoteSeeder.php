<?php

namespace Database\Seeders;

use App\Models\Note;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notes = [
            [
                'note' => 'No show'
            ],
            [
                'note' => 'No parts'
            ],
            [
                'note' => 'No labour'
            ],
            [
                'note' => 'Further work required'
            ],
            [
                'note' => 'Could not find asset'
            ]
        ];

        foreach ($notes as $note) {
            Note::create($note);
        }
    }
}
