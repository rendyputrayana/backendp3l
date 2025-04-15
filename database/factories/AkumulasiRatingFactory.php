<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AkumulasiRating>
 */
class AkumulasiRatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'akumulasi' => $this->faker->randomFloat(1,0,5),
            'id_penitip' => $this->faker->unique()->numberBetween(1, 50),
        ];
    }
}
