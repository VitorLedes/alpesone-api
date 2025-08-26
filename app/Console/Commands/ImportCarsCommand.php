<?php

namespace App\Console\Commands;

use App\Http\Controllers\CarController;
use App\Http\Requests\CarRequest;
use App\Models\Car;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ImportCarsCommand extends Command
{
    protected $signature = 'import:cars';
    protected $description = 'Pega os dados de um JSON e manipula ele na aplicação';
    protected CarController $carController;

    public function __construct(CarController $carController) {
        parent::__construct();
        $this->carController = $carController;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('[IMPORT] Iniciando command de importação de carros...');

        // Timeout adicionado pq caso a API demore muito, ele não fique travado!
        $response = Http::timeout(30)->get('https://hub.alpes.one/api/v1/integrator/export/1902');

        if ($response->failed()) {
            Log::error('[IMPORT] Falha ao buscar dados do JSON.');
            return 1;
        }

        $carsData = $response->json();

        if (empty($carsData)) {
            Log::warning('[IMPORT] Nenhum dado encontrado no JSON.');
            return 0;
        }

        $imported = 0;
        $updated = 0;
        $errors = 0;

        // Processar todos os dados de carros
        foreach ($carsData as $carData) {

            try {

                $data = $this->processCarData($carData);

                $data == 'updated' ? $updated++ : $imported++;

            } catch (Exception $e) {
                $errors++;
                $this->error('[IMPORT] Erro: ' . $e->getMessage());
            }

        }

        Log::info('[IMPORT] Command de importação de carros finalizado!');
        Log::info('[IMPORT] Quantidade de carros importados: ' . $imported);
        Log::info('[IMPORT] Quantidade de carros atualizados: ' . $updated);
        Log::info('[IMPORT] Quantidade de carros que deram erro: ' . $errors);

    }
    
    private function processCarData(array $carData) {

        // Criar corpo pra validação
        $bodyToValidate = $this->createBodyForValidation($carData);

        $request = new CarRequest();
        $request->setFromCommand(true);

        $validatedData = Validator::make($bodyToValidate, $request->rules())->validate();

        // Verificar se o carro já existe
        $car = Car::where('external_id', $carData['id'])->first();
        $isUpdate = $car != null;

        try {

            DB::beginTransaction();

            if ($isUpdate) {
                $car->update($validatedData);
            } else {
                $car = Car::create($validatedData);
            }

            $car->syncRelations($validatedData);

            DB::commit();
            return $isUpdate ? 'updated' : 'imported';

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    private function createBodyForValidation(array $carData): array {

        return [
            'external_id' => $carData['id'],
            'type' => $carData['type'],
            'brand' => $carData['brand'],
            'model' => $carData['model'],
            'version' => $carData['version'],
            'model_year' => $carData['year']['model'],
            'build_year' => $carData['year']['build'],
            'doors' => $carData['doors'],
            'board' => $carData['board'],
            'chassi' => $carData['chassi'] ?? null,
            'transmission' => $carData['transmission'],
            'km' => $carData['km'],
            'description' => $carData['description'],
            'sold' => $carData['sold'],
            'category' => $carData['category'],
            'url_car' => $carData['url_car'],
            'old_price' => $carData['old_price'] ?? null,
            'price' => $carData['price'],
            'color' => $carData['color'],
            'fuel' => $carData['fuel'],
            'fotos' => $carData['fotos']
        ];

    }

}
