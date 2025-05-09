<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pegawai>
 */
class PegawaiFactory extends Factory
{

    protected $model = \App\Models\Pegawai::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_pegawai' => $this->faker->name(),
            'tanggal_lahir' => $this->faker->date('Y-m-d', '2000-12-12'),
            'id_jabatan' => null,
        ];
    }
}
