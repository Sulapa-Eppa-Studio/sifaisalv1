<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TermintSppPpk;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TermintSppPpkFile>
 */
class TermintSppPpkFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'termint_spp_ppk_id' => TermintSppPpk::factory(), // Relasi ke tabel termint_spp_ppk
            'file_type' => str_replace(' ', '_', $this->faker->sentence), // Tipe file acak
            'file_path' => 'docs.pdf', // Path file acak
        ];
    }
}
