<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequestDonasi>
 */
class RequestDonasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $barangBekas = [
            'Laptop', 'Handphone', 'TV tabung', 'Kulkas',
            'Sepeda', 'Kamera analog', 'Mesin jahit',
            'Jam dinding', 'Kursi kayu', 'Meja kantor',
            'Printer', 'Monitor CRT', 'Kompor gas', 'Sofa',
            'Karpet', 'Rak besi', 'Setrika', 'Kaset VHS',  
            'Tas kulit', 'Lemari',
            'Peralatan makan', 'Gitar akustik'
        ];
        
        return [
            'detail_request' => $this->faker->randomElement($barangBekas),
            'id_organisasi' => $this->faker->numberBetween(1, 20),
        ];
    }

}
