<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TermintSppPpk;
use App\Models\User;
use App\Models\Contract;
use App\Models\SPM;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TermintSppPpk>
 */
class TermintSppPpkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $verifcation_status =   $this->faker->randomElement(['not_available', 'in_progress', 'approved', 'rejected']);

        return [
            'user_id' => User::inRandomOrder()->first()->id, // Relasi ke tabel users
            'contract_id' => Contract::inRandomOrder()->first()->id, // Relasi ke tabel contracts
            'no_termint' => $this->faker->unique()->numerify('TERM-#####'), // No Termint dengan format unik
            'description' => $this->faker->sentence(), // Deskripsi singkat
            'payment_value' => $this->faker->randomFloat(2, 10000, 1000000), // Nilai pembayaran acak
            'has_advance_payment' => $this->faker->boolean(), // Status advance payment
            'ppspm_verification_status' => $verifcation_status, // Status verifikasi
            'ppspm_rejection_reason' => $verifcation_status === 'rejected' ? $this->faker->sentence() : null, // Alasan penolakan (jika ada)
            'ppspm_id' => SPM::inRandomOrder()->first()->id ?? null, // Relasi ke tabel SPM, nullable
        ];
    }
}
