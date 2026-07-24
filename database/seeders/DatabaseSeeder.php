<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warranty;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'admin@example.com')->doesntExist()) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Warranty::factory(5)->inactive()->create();

        // Warranty::factory(5)->active()->create();

        // Warranty::factory(3)->expired()->create();

        // Warranty::factory()->expiringSoon(7)->create();
    }
}
