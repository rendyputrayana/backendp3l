<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pembeli;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alamat>
 */
class AlamatFactory extends Factory
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
            'detail_alamat' => $this->faker->randomElement($streetNames) . 
                               ' No. ' . $this->faker->numberBetween(1, 200) . 
                               ', Yogyakarta, Indonesia',
            'id_pembeli' => Pembeli::factory(),
            'is_default' => false,
        ];
    }
}
