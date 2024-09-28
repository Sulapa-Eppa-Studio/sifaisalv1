<?php

namespace Database\Factories;

use App\Models\PaymentRequest;
use App\Models\SPM;
use App\Models\TermintSppPpk;
use App\Models\Treasurer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SPMRequest>
 */
class SPMRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker  =   $this->faker;

        $trs_verification_status = $faker->randomElement(['not_available', 'in_progress', 'approved', 'rejected']);
        $kpa_verfication_status = 'not_available';

        if ($trs_verification_status === 'approved') {
            $kpa_verfication_status = $faker->randomElement(['not_available', 'in_progress', 'approved', 'rejected']);
        }

        $trs_rejected_reason = $trs_verification_status ==='rejected'? $this->faker->sentence : null;
        $kpa_rejected_reason = $kpa_verfication_status ==='rejected'? $this->faker->sentence : null;

        // Asumsi bahwa tabel-tabel terkait memiliki data yang cukup
        return [
            'spm_number' => $this->faker->unique()->bothify('SPM-####'),
            'spm_description' => $this->faker->sentence(6, true),
            'spm_document' => 'docs.pdf', // atau path file yang sesuai
            'spm_value' => $this->faker->numberBetween(100000, 10000000),
            'spm_id' => SPM::inRandomOrder()->first()->id ?? SPM::factory(), // Pastikan ada data SPM
            'ppk_request_id' => TermintSppPpk::inRandomOrder()->first()->id ?? TermintSPPPpk::factory(),
            'payment_request_id' => PaymentRequest::inRandomOrder()->first()->id ?? PaymentRequest::factory(),
            'treasurer_verification_status' => $trs_verification_status,
            'treasurer_rejection_reason' => $trs_rejected_reason,
            'treasurer_id' => Treasurer::inRandomOrder()->first()->id ?? Treasurer::factory(),
            'kpa_verification_status' => $kpa_verfication_status,
            'kpa_rejection_reason' => $kpa_rejected_reason,
            'created_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'updated_at' => now(),
        ];
    }
}
