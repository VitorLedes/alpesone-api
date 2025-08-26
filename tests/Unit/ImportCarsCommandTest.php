<?php

namespace Tests\Unit;

use App\Console\Commands\ImportCarsCommand;
use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ImportCarsCommandTest extends TestCase
{
    use RefreshDatabase;

    private ImportCarsCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new ImportCarsCommand(new \App\Http\Controllers\CarController());
    }

    /** @test */
    public function it_creates_body_for_validation_correctly()
    {
        $carData = [
            'id' => 123,
            'type' => 'Usado',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'version' => '2.0 XEi',
            'year' => [
                'model' => '2020',
                'build' => '2019'
            ],
            'doors' => 4,
            'board' => 'ABC-1234',
            'transmission' => 'Manual',
            'km' => '50000',
            'description' => 'Carro em ótimo estado',
            'sold' => '123',
            'category' => 'Sedan',
            'url_car' => 'https://exemplo.com/carro',
            'old_price' => '',
            'price' => '55000',
            'color' => 'Prata',
            'fuel' => 'Flex',
            'fotos' => ['https://alpes-hub.s3.amazonaws.com/uploads/public/680/7c8/4e8/6807c84e83e49541662900.jpeg']
        ];

        // Usar reflexão para acessar método privado
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('createBodyForValidation');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->command, [$carData]);

        $expected = [
            'external_id' => 123,
            'type' => 'Usado',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'version' => '2.0 XEi',
            'model_year' => '2020',
            'build_year' => '2019',
            'doors' => 4,
            'board' => 'ABC-1234',
            'transmission' => 'Manual',
            'km' => '50000',
            'description' => 'Carro em ótimo estado',
            'sold' => '123',
            'category' => 'Sedan',
            'url_car' => 'https://exemplo.com/carro',
            'old_price' => '',
            'price' => '55000',
            'color' => 'Prata',
            'fuel' => 'Flex',
            'fotos' => ['https://alpes-hub.s3.amazonaws.com/uploads/public/680/7c8/4e8/6807c84e83e49541662900.jpeg'],
            'chassi' => null,
        ];

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function it_handles_null_chassi_and_old_price()
    {
        $carData = [
            'id' => 123,
            'type' => 'Usado',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'version' => '2.0 XEi',
            'year' => ['model' => '2020', 'build' => '2019'],
            'doors' => 4,
            'board' => 'ABC-1234',
            'transmission' => 'Manual',
            'km' => '50000',
            'description' => 'Carro em ótimo estado',
            'sold' => '123',
            'category' => 'Sedan',
            'url_car' => 'https://exemplo.com/carro',
            'price' => '55000',
            'color' => 'Prata',
            'fuel' => 'Flex',
            'fotos' => ['https://alpes-hub.s3.amazonaws.com/uploads/public/680/7c8/4e8/6807c84e83e49541662900.jpeg']
        ];

        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('createBodyForValidation');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->command, [$carData]);

        $this->assertNull($result['chassi']);
        $this->assertNull($result['old_price']);
    }

    /** @test */
    public function it_processes_new_car_data_correctly()
    {
        $carData = [
            'id' => 999,
            'type' => 'Usado',
            'brand' => 'Honda',
            'model' => 'Civic',
            'version' => '1.8 LXS',
            'year' => ['model' => '2018', 'build' => '2017'],
            'doors' => 4,
            'board' => 'DEF-5678',
            'transmission' => 'Automático',
            'km' => '30000',
            'description' => 'Carro seminovo',
            'sold' => '123',
            'category' => 'Sedan',
            'url_car' => 'https://exemplo.com/civic',
            'price' => '45000',
            'color' => 'Preto',
            'fuel' => 'Flex',
            'fotos' => ['https://alpes-hub.s3.amazonaws.com/uploads/public/680/7c8/4e8/6807c84e83e49541662900.jpeg']
        ];

        // Mock do Car::where para retornar null (carro não existe)
        $this->mock(Car::class, function ($mock) {
            $mock->shouldReceive('where')
                 ->with('external_id', 999)
                 ->andReturnSelf();
            $mock->shouldReceive('first')
                 ->andReturn(null);
            $mock->shouldReceive('create')
                 ->andReturn(new Car());
        });

        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('processCarData');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->command, [$carData]);

        $this->assertEquals('imported', $result);
    }

    /** @test */
    public function it_processes_existing_car_data_correctly()
    {
        // Criar um carro existente no banco
        $existingCar = Car::factory()->create([
            'external_id' => 888
        ]);

        $carData = [
            'id' => 888,
            'type' => 'Usado',
            'brand' => 'Ford',
            'model' => 'Focus',
            'version' => '2.0 SE',
            'year' => ['model' => '2019', 'build' => '2018'],
            'doors' => 4,
            'board' => 'GHI-9012',
            'transmission' => 'Manual',
            'km' => '25000',
            'description' => 'Carro atualizado',
            'sold' => '123',
            'category' => 'Hatch',
            'url_car' => 'https://exemplo.com/focus',
            'price' => '40000',
            'color' => 'Azul',
            'fuel' => 'Flex',
            'fotos' => ['https://alpes-hub.s3.amazonaws.com/uploads/public/680/7c8/4e8/6807c84e83e49541662900.jpeg']
        ];

        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('processCarData');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->command, [$carData]);

        $this->assertEquals('updated', $result);
    }

    /** @test */
    public function it_handles_empty_response_correctly()
    {
        // Mock resposta vazia
        Http::fake([
            'https://hub.alpes.one/api/v1/integrator/export/1902' => Http::response([])
        ]);

        Log::shouldReceive('info')->once()->with('[IMPORT] Iniciando command de importação de carros...');
        Log::shouldReceive('warning')->once()->with('[IMPORT] Nenhum dado encontrado no JSON.');

        $result = $this->artisan('import:cars');

        $result->assertExitCode(0);
    }

    /** @test */
    public function it_handles_validation_errors()
    {
        // Mock resposta com dados inválidos
        Http::fake([
            'https://hub.alpes.one/api/v1/integrator/export/1902' => Http::response([
                [
                    'id' => 123,
                    // Faltam campos obrigatórios
                    'brand' => 'Toyota'
                ]
            ])
        ]);

        Log::shouldReceive('info')->with('[IMPORT] Iniciando command de importação de carros...');
        Log::shouldReceive('info')->with('[IMPORT] Command de importação de carros finalizado!');
        Log::shouldReceive('info')->with('[IMPORT] Quantidade de carros importados: 0');
        Log::shouldReceive('info')->with('[IMPORT] Quantidade de carros atualizados: 0');
        Log::shouldReceive('info')->with('[IMPORT] Quantidade de carros que deram erro: 1');

        $result = $this->artisan('import:cars');

        $result->assertExitCode(0);
    }
}