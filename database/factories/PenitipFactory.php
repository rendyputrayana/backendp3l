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
        $streetNames = [
            'Jl. Malioboro',
            'Jl. Prawirotaman',
            'Jl. Parangtritis',
            'Jl. Affandi',
            'Jl. Kaliurang',
            'Jl. Magelang',
            'Jl. Suroto',
            'Jl. C. Simanjuntak',
            'Jl. Sultan Agung',
            'Jl. Taman Siswa',
            'Jl. Ngadisuryan',
            'Jl. Tirtodipuran',
            'Jl. Gedongkuning',
            'Jl. Solo',
            'Jl. Bantul',
            'Jl. Imogiri',
            'Jl. Wonosari',
            'Jl. Godean',
            'Jl. Ringroad Utara',
            'Jl. Ringroad Selatan',
            'Jl. Adisucipto',
            'Jl. Diponegoro',
            'Jl. Babarsari',
            'Jl. Gejayan',
            'Jl. Candi Borobudur',
        ];

        return [
            'nama_penitip' => $this->faker->name(),
            'no_ktp' => $this->faker->unique()->numerify('################'),
            'no_telepon' => $this->faker->unique()->numerify('08#########'),
            'alamat_penitip' => $this->faker->randomElement($streetNames) . 
                                ' No. ' . $this->faker->numberBetween(1, 200) . 
                                ', Yogyakarta, Indonesia',
            'foto_ktp' => $this->faker->imageUrl(640, 480, 'people', true, 'KTP'),
            'saldo' => $this->faker->numberBetween(10000, 10000000),
            'poin' => $this->faker->numberBetween(0, 5000),
        ];
    }
}
