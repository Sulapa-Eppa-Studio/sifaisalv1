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

        // Tahap verifikasi dimulai dari PPK
        $ppk_verification_status = $this->faker->randomElement(['in_progress', 'approved', 'rejected']);
        $ppspm_verification_status = 'not_available';
        $treasurer_verification_status = 'not_available';
        $kpa_verification_status = 'not_available';
        $verification_progress = 'ppk';

        // Jika PPK disetujui, lanjut ke PPSPM
        if ($ppk_verification_status === 'approved') {
            $ppspm_verification_status = $this->faker->randomElement(['in_progress', 'approved', 'rejected']);

            $verification_progress = 'ppspm';

            // Jika PPSPM disetujui, lanjut ke Bendahara
            if ($ppspm_verification_status === 'approved') {
                $treasurer_verification_status = $this->faker->randomElement(['in_progress', 'approved', 'rejected']);

                $verification_progress = 'treasurer';

                // Jika Bendahara disetujui, lanjut ke KPA
                if ($treasurer_verification_status === 'approved') {
                    $kpa_verification_status = $this->faker->randomElement(['in_progress', 'approved', 'rejected']);

                    $verification_progress = 'kpa';
                }
            }
        }

        return [
            'contract_number' => Contract::inRandomOrder()->first()->contract_number ?? 'CONTRACT-' . $this->faker->unique()->numberBetween(1000, 9999),
            'request_number' => 'REQ-' . $this->faker->unique()->numberBetween(1000, 9999),
            'payment_stage' => $this->faker->randomElement(['Down Payment', 'Phase I', 'Phase II', 'Phase III']),
            'payment_value' => $this->faker->numberBetween(1000000, 100000000), // Nilai pembayaran antara 1.000.000 hingga 100.000.000
            'payment_description' => $this->faker->sentence(),
            'verification_progress' => $verification_progress,
            'ppk_verification_status' => $ppk_verification_status,
            'ppk_rejection_reason' => $ppk_verification_status === 'rejected' ? $this->faker->sentence() : null,
            'ppk_id' => $ppk_verification_status === 'approved' ? PPK::inRandomOrder()->first()->id ?? null : null,
            'ppspm_verification_status' => $ppspm_verification_status,
            'ppspm_rejection_reason' => $ppspm_verification_status === 'rejected' ? $this->faker->sentence() : null,
            'ppspm_id' => $ppspm_verification_status === 'approved' ? SPM::inRandomOrder()->first()->id ?? null : null,
            'treasurer_verification_status' => $treasurer_verification_status,
            'treasurer_rejection_reason' => $treasurer_verification_status === 'rejected' ? $this->faker->sentence() : null,
            'treasurer_id' => $treasurer_verification_status === 'approved' ? Treasurer::inRandomOrder()->first()->id ?? null : null,
            'kpa_verification_status' => $kpa_verification_status,
            'kpa_rejection_reason' => $kpa_verification_status === 'rejected' ? $this->faker->sentence() : null,
            'kpa_id' => $kpa_verification_status === 'approved' ? KPA::inRandomOrder()->first()->id ?? null : null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}
