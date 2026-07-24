<?php

namespace Database\Seeders;

use App\Models\Warranty;
use Illuminate\Database\Seeder;

class SampleQrSeeder extends Seeder
{
    public function run(): void
    {
        Warranty::factory()->count(10)->create();
    }
}
