<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PaymentRequest;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_request_id' => PaymentRequest::inRandomOrder()->first()->id, // Relasi ke tabel payment_requests
            'name' => $this->faker->randomElement(['Surat Permohonan', 'NPWP', 'KTP', 'Invoice']), // Nama dokumen acak
            'type' => $this->faker->randomElement(['pdf', 'jpg', 'png', 'docx']), // Tipe dokumen acak
            'path' => 'docs.pdf', // Path file dokumen (menggunakan faker untuk generate path acak)
        ];
    }
}
