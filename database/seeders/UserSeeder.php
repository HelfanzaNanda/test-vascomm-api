<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@yopmail.com',
            'phone' => fake()->unique()->phoneNumber(),
            'status' => StatusEnum::ACTIVE->value,
            'password' => Hash::make('password')
        ]);

        $user->assignRole('admin');

    }
}
