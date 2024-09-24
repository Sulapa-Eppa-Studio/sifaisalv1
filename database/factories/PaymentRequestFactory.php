<?php

namespace Database\Factories;

use App\Models\PaymentRequest;
use App\Models\Contract;
use App\Models\KPA;
use App\Models\PPK;
use App\Models\Treasurer;
use App\Models\SPM;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentRequest>
 */
class PaymentRequestFactory extends Factory
{
    protected $model = PaymentRequest::class;

    public function definition()
    {
        // Pastikan tabel terkait memiliki data. Jika belum, Anda perlu membuat seeder untuk tabel tersebut terlebih dahulu.
        return [
            'contract_number' => Contract::inRandomOrder()->first()->contract_number ?? 'CONTRACT-' . $this->faker->unique()->numberBetween(1000, 9999),
            'request_number' => 'REQ-' . $this->faker->unique()->numberBetween(1000, 9999),
            'payment_stage' => $this->faker->randomElement(['Down Payment', 'Phase I', 'Phase II', 'Phase III']),
            'payment_value' => $this->faker->numberBetween(1000000, 100000000), // Nilai pembayaran antara 1.000.000 hingga 100.000.000
            'payment_description' => $this->faker->sentence(),
            'verification_progress' => $this->faker->randomElement(['ppk', 'ppspm', 'treasurer', 'kpa']),
            'ppk_verification_status' => $this->faker->randomElement(['in_progress', 'approved', 'rejected']),
            'ppk_rejection_reason' => $this->faker->optional()->sentence(),
            'ppk_id' => PPK::inRandomOrder()->first()->id ?? null,
            'ppspm_verification_status' => $this->faker->randomElement(['not_available', 'in_progress', 'approved', 'rejected']),
            'ppspm_rejection_reason' => $this->faker->optional()->sentence(),
            'ppspm_id' => SPM::inRandomOrder()->first()->id ?? null,
            'treasurer_verification_status' => $this->faker->randomElement(['not_available', 'in_progress', 'approved', 'rejected']),
            'treasurer_rejection_reason' => $this->faker->optional()->sentence(),
            'treasurer_id' => Treasurer::inRandomOrder()->first()->id ?? null,
            'kpa_verification_status' => $this->faker->randomElement(['not_available', 'in_progress', 'approved', 'rejected']),
            'kpa_rejection_reason' => $this->faker->optional()->sentence(),
            'kpa_id' => KPA::inRandomOrder()->first()->id ?? null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}
