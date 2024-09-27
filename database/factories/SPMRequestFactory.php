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
        // Asumsi bahwa tabel-tabel terkait memiliki data yang cukup
        return [
            'spm_number' => $this->faker->unique()->bothify('SPM-####'),
            'spm_description' => $this->faker->sentence(6, true),
            'spm_document' => $this->faker->url, // atau path file yang sesuai
            'spm_value' => $this->faker->numberBetween(100000, 10000000),
            'spm_id' => SPM::inRandomOrder()->first()->id ?? SPM::factory(), // Pastikan ada data SPM
            'ppk_request_id' => TermintSppPpk::inRandomOrder()->first()->id ?? TermintSPPPpk::factory(),
            'payment_request_id' => PaymentRequest::inRandomOrder()->first()->id ?? PaymentRequest::factory(),
            'treasurer_verification_status' => $this->faker->randomElement(['not_available', 'in_progress', 'approved', 'rejected']),
            'treasurer_rejection_reason' => function (array $attributes) {
                return $attributes['treasurer_verification_status'] === 'rejected' ? $this->faker->sentence : null;
            },
            'treasurer_id' => Treasurer::inRandomOrder()->first()->id ?? Treasurer::factory(),
            'kpa_verification_status' => $this->faker->randomElement(['not_available', 'in_progress', 'approved', 'rejected']),
            'kpa_rejection_reason' => function (array $attributes) {
                return $attributes['kpa_verification_status'] === 'rejected' ? $this->faker->sentence : null;
            },
            'created_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'updated_at' => now(),
        ];
    }
}
