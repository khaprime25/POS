<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Kaung Htet Aung',
            'email' => 'owner@pos.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'is_active' => true,
        ]);

        // CASHIER
        User::create([
            'name' => 'Caxper',
            'email' => 'cashier@pos.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'is_active' => true,
        ]);

        // CHEF
        User::create([
            'name' => 'Pyae Bhone Shan',
            'email' => 'chef@pos.com',
            'password' => Hash::make('password'),
            'role' => 'chef',
            'is_active' => true,
        ]);

        // Optional: extra cashiers for testing
        User::factory(3)->create([
            'role' => 'cashier',
        ]);
    }
}
