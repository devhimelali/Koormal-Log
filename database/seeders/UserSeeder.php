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
                'email' => 'aherbertson@hotmail.com',
                'password' => bcrypt('georgeboss'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'supervisor@gmail.com',
                'password' => bcrypt('crew'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'John Doe',
                'email' => 'user@gmail.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);
            if ($userData['email'] === 'aherbertson@hotmail.com') {
                $user->assignRole('admin');
            } else if ($userData['email'] === 'supervisor@gmail.com') {
                $user->assignRole('supervisor');
            } else {
                $user->assignRole('user');
            }
        }
    }
}
