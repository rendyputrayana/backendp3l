<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organisasi>
 */
class OrganisasiFactory extends Factory
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
            'nama_organisasi' => $this->faker->company(),
            'alamat_organisasi' => $this->faker->randomElement($streetNames) . 
                                   ' No. ' . $this->faker->numberBetween(1, 200) . 
                                   ', Yogyakarta, Indonesia',
        ];
    }
}
