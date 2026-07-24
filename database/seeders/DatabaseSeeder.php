<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warranty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
                User::updateOrCreate(
            [
                'email' => env('ADMIN_EMAIL', 'admin@example.com'),
            ],
            [
                'name' => env('ADMIN_NAME', 'Admin'),
                'password' => Hash::make(
                    env('ADMIN_PASSWORD', 'change-this-password')
                ),
                'role' => 'admin',
            ]
        );


        // Warranty::factory(5)->inactive()->create();

        // Warranty::factory(5)->active()->create();

        // Warranty::factory(3)->expired()->create();

        // Warranty::factory()->expiringSoon(7)->create();
    }
}
