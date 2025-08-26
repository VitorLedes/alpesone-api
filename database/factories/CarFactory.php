<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // fill with car test
            'type' => 'carro',
            'brand' => 'Hyundai',
            'model' => 'CRETA',
            'version' => 'CRETA 16A ACTION',
            'model_year' => '2025',
            'build_year' => '2025',
            'doors' => 5,
            'board' => 'JCU2I93',
            'chassi' => '',
            'transmission' => 'Automática',
            'km' => '24208',
            'description' => '*revisado\r\n*procedência\r\n*garantia \r\n\r\nPegamos trocas mediante avaliação\r\nvalor do anuncio para vendas avista !\r\…9931-6648 /  Araranguá - SC \r\n\r\nJefersson  48- 8427-9763 / Criciuma - SC\r\n\r\nLucas - 48-48 9177-1511 /  Tubarão - SC',
            'sold' => '0',
            'category' => 'SUV',
            'url_car' => 'hyundai-creta-2025-automatica-125306',
            'old_price' => '',
            'price' => '115900.00',
            'color' => 'Branco',
            'fuel' => 'Gasolina',
            'external_id' => 125306,
        ];
    }
}
