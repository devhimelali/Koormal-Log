<?php

namespace Database\Seeders;

use App\Models\Labour;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LabourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $labours = [
            [
                'crew_id' => 4,
                'name' => 'Alex Herbertson'
            ],
            [
                'crew_id' => 2,
                'name' => 'Bill Smith'
            ],
            [
                'crew_id' => 4,
                'name' => 'John Thompson'
            ],
            [
                'crew_id' => 3,
                'name' => 'Frank Lewis'
            ],
            [
                'crew_id' => 2,
                'name' => 'Aldo Grimaldi'
            ],
            [
                'crew_id' => 1,
                'name' => 'Mark Green'
            ]
        ];

        foreach ($labours as $labour) {
            Labour::create($labour);
        }
    }
}
