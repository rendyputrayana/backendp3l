<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donasi>
 */
class DonasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_organisasi' => $this->faker->numberBetween(1, 20),
            'tanggal_donasi' => $this->faker->dateTimeBetween('-5 year', 'now'),
            'nama_penerima' => $this->faker->name(),
        ];
    }
}
