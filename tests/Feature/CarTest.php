<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarTest extends TestCase
{
    use RefreshDatabase;
    private $url = '/api/cars';

    public function test_if_return_all_cars(): void {
        
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson($this->url);
        $response->assertStatus(200);
    }

    public function test_if_return_all_cars_with_pagination(): void {

        $authUser = User::factory()->create();

        Car::factory()->count(50)->create();

        $response = $this->actingAs($authUser, 'sanctum')->getJson("{$this->url}?limit=25");

        $response->assertStatus(200);
        $response->assertJsonCount(25, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'type',
                    'brand',
                    'model',
                    'version',
                    'model_year',
                    'build_year',
                    'doors',
                    'board',
                    'chassi',
                    'transmission',
                    'km',
                    'description',
                    'sold',
                    'category',
                    'url_car',
                    'old_price',
                    'price',
                    'color',
                    'fuel',
                    'external_id',
                    'pictures' => [],
                    'optionals' => []
                ],
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next'
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'links' => [
                    '*' => [
                        'url',
                        'label',
                        'page',
                        'active'
                    ],
                ],
                'path',
                'per_page',
                'to',
                'total'
            ],
        ]);
    }

    public function test_if_return_a_single_car(): void {
        $user = User::factory()->create();

        $car = Car::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson("{$this->url}/{$car->id}");
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'type',
                'brand',
                'model',
                'version',
                'model_year',
                'build_year',
                'doors',
                'board',
                'chassi',
                'transmission',
                'km',
                'description',
                'sold',
                'category',
                'url_car',
                'old_price',
                'price',
                'color',
                'fuel',
                'external_id',
            ],
        ]);
    }

    public function test_if_car_can_be_created_and_inserted_in_the_database(): void {
        $user = User::factory()->create();

        $body = [
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
            'description' => 'revisado procedência garantia n Pegamos trocas mediante avaliação valor do anuncio para vendas avista 9931-6648 /  Araranguá - SC \r\n\r\nJefersson  48- 8427-9763 / Criciuma - SC\r\n\r\nLucas - 48-48 9177-1511 /  Tubarão - SC',
            'sold' => '0',
            'category' => 'SUV',
            'url_car' => 'hyundai-creta-2025-automatica-125306',
            'old_price' => '',
            'price' => '115900.00',
            'color' => 'Branco',
            'fuel' => 'Gasolina',
            'external_id' => 125306,
            'fotos' => [
                "https://revendaexemplo.com/images/corolla-cross-frente.jpg",
                "https://revendaexemplo.com/images/corolla-cross-traseira.jpg",
                "https://revendaexemplo.com/images/corolla-cross-interior.jpg"
            ]
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson($this->url, $body);
        $response->assertStatus(201);

        $this->assertDatabaseHas('cars', [
            'type' => 'carro',
            'brand' => 'Hyundai',
            'model' => 'CRETA',
            'version' => 'CRETA 16A ACTION',
            'model_year' => '2025',
            'build_year' => '2025',
            'doors' => 5,
            'board' => 'JCU2I93',
            'chassi' => null,
            'transmission' => 'Automática',
            'km' => '24208',
            'description' => 'revisado procedência garantia n Pegamos trocas mediante avaliação valor do anuncio para vendas avista 9931-6648 /  Araranguá - SC \r\n\r\nJefersson  48- 8427-9763 / Criciuma - SC\r\n\r\nLucas - 48-48 9177-1511 /  Tubarão - SC',
            'sold' => '0',
            'category' => 'SUV',
            'url_car' => 'hyundai-creta-2025-automatica-125306',
            'old_price' => null,
            'price' => '115900.00',
            'color' => 'Branco',
            'fuel' => 'Gasolina',
            'external_id' => 125306,
        ]);
    }

    public function test_if_car_can_be_created_with_invalid_data(): void {
        $user = User::factory()->create();

        $body = [
            'type' => '',
            'brand' => '',
            'model' => '',
            'version' => '',
            'model_year' => '',
            'build_year' => '',
            'doors' => '',
            'board' => '',
            'chassi' => 123,
            'transmission' => '',
            'km' => '',
            'description' => 123123,
            'sold' => '',
            'category' => '',
            'url_car' => '',
            'old_price' => 123123,
            'price' => '',
            'color' => '',
            'fuel' => '',
            'external_id' => '',
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson($this->url, $body);
        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'type',
            'brand',
            'model',
            'version',
            'model_year',
            'build_year',
            'doors',
            'board',
            'chassi',
            'transmission',
            'km',
            'description',
            'sold',
            'category',
            'url_car',
            'old_price',
            'price',
            'color',
            'fuel',
            'external_id',
        ]);
    }

    public function test_if_car_can_be_updated_in_the_database(): void {
        $user = User::factory()->create();

        $car = Car::factory()->create();

        $body = [
            'type' => 'carro',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'version' => 'Corolla 2.0 Dynamic',
            'model_year' => '2020',
            'build_year' => '2020',
            'doors' => 4,
            'board' => 'ABC1D23',
            'chassi' => null,
            'transmission' => 'Automática',
            'km' => '15000',
            'description' => '*revisado\r\n*procedência\r\n*garantia \r\n\r\nPegamos trocas mediante avaliação\r\nvalor do anuncio para vendas avista !\r\n48-9931-6648 /  Araranguá - SC \r\n\r\nJefersson  48- 8427-9763 / Criciuma - SC\r\n\r\nLucas - 48-48 9177-1511 /  Tubarão - SC',
            'sold' => '0',
            'category' => 'Sedan',
            'url_car' => 'toyota-corolla-2020-automatica-123456',
            'old_price' => null,
            'price' => '89900.00',
            'color' => 'Prata',
            'fuel' => 'Gasolina',
            'external_id' => 123456,
            'fotos' => [
                "https://revendaexemplo.com/images/corolla-cross-frente.jpg",
            ]
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("{$this->url}/{$car->id}", $body);
        $response->assertStatus(200);

        $this->assertDatabaseHas('cars', [
            'type' => 'carro',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'version' => 'Corolla 2.0 Dynamic',
            'model_year' => '2020',
            'build_year' => '2020',
            'doors' => 4,
            'board' => 'ABC1D23',
            'chassi' => null,
            'transmission' => 'Automática',
            'km' => '15000',
            'description' => '*revisado\r\n*procedência\r\n*garantia \r\n\r\nPegamos trocas mediante avaliação\r\nvalor do anuncio para vendas avista !\r\n48-9931-6648 /  Araranguá - SC \r\n\r\nJefersson  48- 8427-9763 / Criciuma - SC\r\n\r\nLucas - 48-48 9177-1511 /  Tubarão - SC',
            'sold' => '0',
            'category' => 'Sedan',
            'url_car' => 'toyota-corolla-2020-automatica-123456',
            'old_price' => null,
            'price' => '89900.00',
            'color' => 'Prata',
            'fuel' => 'Gasolina',
            'external_id' => 123456,
        ]);

    }

    public function test_if_car_can_be_updated_with_invalid_data(): void {
        $user = User::factory()->create();

        $car = Car::factory()->create();

        $body = [
            'type' => '',
            'brand' => '',
            'model' => '',
            'version' => '',
            'model_year' => '',
            'build_year' => '',
            'doors' => 'asdasd',
            'board' => '',
            'chassi' => 123123,
            'transmission' => '',
            'km' => '',
            'description' => 123123,
            'sold' => 123123,
            'category' => '',
            'url_car' => '',
            'old_price' => 123123,
            'price' => 89900.00,
            'color' => '',
            'fuel' => '',
            'external_id' => null,
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("{$this->url}/{$car->id}", $body);
        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'type',
            'brand',
            'model',
            'version',
            'model_year',
            'build_year',
            'doors',
            'board',
            'chassi',
            'transmission',
            'km',
            'description',
            'sold',
            'category',
            'url_car',
            'old_price',
            'price',
            'color',
            'fuel',
            'external_id',
        ]);
    }

    public function test_if_car_can_not_be_found(): void {

        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson("{$this->url}/9999");
        $response->assertStatus(404);
    }

    public function test_if_car_can_be_deleted_from_the_database(): void {
        $user = User::factory()->create();

        $car = Car::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->deleteJson("{$this->url}/{$car->id}");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('cars', [
            'id' => $car->id,
        ]);
    }

    public function test_if_unauthenticated_user_cannot_access_the_endpoints(): void {
        $response = $this->getJson($this->url);
        $response->assertStatus(401);

        $response = $this->postJson($this->url, []);
        $response->assertStatus(401);

        $response = $this->putJson("{$this->url}/1", []);
        $response->assertStatus(401);

        $response = $this->deleteJson("{$this->url}/1");
        $response->assertStatus(401);
    }

}
