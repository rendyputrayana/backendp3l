<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PenukaranReward>
 */
class PenukaranRewardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_pembeli' => $this->faker->numberBetween(1, 50), 
            'id_merchandise' => $this->faker->numberBetween(1, 10),
            'tanggal_penukaran' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'tanggal_pengambilan' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
        ];
    }
}
