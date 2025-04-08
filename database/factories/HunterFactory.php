<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hunter>
 */
class HunterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_hunter' => $this->faker->name(),
            'saldo' => $this->faker->numberBetween(0, 10000000),
            'no_telepon' => $this->generateIndonesianPhoneNumber()
        ];
    }

    private function generateIndonesianPhoneNumber(): string
    {
        $prefixes = ['0812', '0813', '0821', '0822', '0823', '0852', '0853', '0857', '0858', '0896', '0897', '0898', '0899'];
        $prefix = $this->faker->randomElement($prefixes);
        $number = $this->faker->numberBetween(1000000, 9999999); // 7 digit angka

        return $prefix . $number;
    }
}
