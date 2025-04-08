<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Penitipan>
 */
class PenitipanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_penitip' => $this->faker->unique()->numberBetween(1, 50),
            'id_pegawai' => $this->faker->numberBetween(1, 28),
            'id_hunter' => $this->faker->numberBetween(1, 10),
            'tanggal_penitipan' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
