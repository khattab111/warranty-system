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
                 $this->call([
            UserSeeder::class,
        ]);


        // Warranty::factory(5)->inactive()->create();

        // Warranty::factory(5)->active()->create();

        // Warranty::factory(3)->expired()->create();

        // Warranty::factory()->expiringSoon(7)->create();
    }
}
