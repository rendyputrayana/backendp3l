<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Penitip>
 */
class PenitipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_penitip' => $this->faker->name(),
            'no_ktp' => $this->faker->unique()->numerify('################'),
            'no_telepon' => $this->faker->unique()->numerify('08#########'),
            'alamat_penitip' => $this->faker->address(),
            'foto_ktp' => $this->faker->imageUrl(640, 480, 'people', true, 'Faker'),
            'saldo' => $this->faker->numberBetween(10000, 10000000),
        ];
    }
}
