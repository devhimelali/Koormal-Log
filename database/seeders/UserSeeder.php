<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Alex Herbert',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'John Doe',
                'email' => 'user@gmail.com',
                'password' => bcrypt('password'),
            ]
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);
            if ($userData['email'] === 'admin@gmail.com') {
                $user->assignRole('admin');
            } else {
                $user->assignRole('user');
            }
        }
    }
}
