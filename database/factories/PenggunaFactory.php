<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pengguna;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pengguna>
 */
class PenggunaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Pengguna::class;

    public function definition(): array
    {
        // Pilih secara acak salah satu foreign key yang akan diisi
        $keys = [
            'id_organisasi' => $this->faker->numberBetween(1, 20),
            'id_hunter' => $this->faker->numberBetween(1, 10),
            'id_pembeli' => $this->faker->numberBetween(1, 50),
            'id_pegawai' => $this->faker->numberBetween(1, 28),
            'id_penitip' => $this->faker->numberBetween(1, 50),
        ];
        
        // Pilih satu kunci secara acak untuk diisi, sisanya biarkan null
        $selectedKey = $this->faker->randomElement(array_keys($keys));
        $foreignKeys = array_map(fn ($key) => $key === $selectedKey ? $keys[$key] : null, array_keys($keys));

        return array_merge([
            'email' => $this->faker->unique()->safeEmail(),
            'username' => $this->faker->unique()->userName(),
            'password' => bcrypt('password'), // Default password
        ], array_combine(array_keys($keys), $foreignKeys));
    }
}
