<?php

namespace Database\Seeders;

use App\Models\SPMRequest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SPMRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SPMRequest::factory()->count(50)->create();
    }
}
