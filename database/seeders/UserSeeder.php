<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creating 5 teachers
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Teacher $i",
                'email' => "teacher$i@example.com",
                's_number' => "T100$i", // Teacher ID
                'password' => Hash::make('password'),
                'role' => 'teacher',
            ]);
        }

        // Creating 50 students
        for ($i = 1; $i <= 50; $i++) {
            User::create([
                'name' => "Student $i",
                'email' => "student$i@example.com",
                's_number' => "S100$i",
                'password' => Hash::make('password'),
                'role' => 'student',
            ]);
        }
    }
}
