<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentRequest;

class PaymentRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Jumlah total data yang akan dibuat
        $total = 100;

        // Menggunakan factory untuk membuat data
        PaymentRequest::factory()->count($total)->create();

        $this->command->info("Seeder PaymentRequestSeeder telah membuat {$total} data PaymentRequest.");
    }
}
