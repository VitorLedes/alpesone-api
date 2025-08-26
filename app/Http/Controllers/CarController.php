<?php

namespace App\Http\Controllers;

use App\Http\Requests\CarRequest;
use App\Http\Resources\CarResource;
use App\Models\Car;
use App\Models\CarPictures;
use App\Models\Optionals;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * Função para listar todos os carros.
     *
     * @return void
     */
    public function index(Request $request) {

        $limit = (int) $request->limit ?? 10;

        if ($limit > 100) {
            $limit = 10;
        }

        $cars = Car::getCarData()->paginate($limit);

        return CarResource::collection($cars);
    }

    /**
     * Função para obter um único carro.
     *
     * @param integer $id
     * @return void
     */
    public function show(int $id) {

        $car = Car::findOrFail($id);
        $car = Car::getCarData()->first();

        return (new CarResource($car));
    }

    /**
     * Função para criar um novo carro.
     *
     * @param CarRequest $request
     * @return void
     */
    public function store(CarRequest $request) {
        
        $car = Car::create($request->validated());

        $car->syncRelations($request->validated());

        return (new CarResource($car))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Função para atualizar um carro.
     *
     * @param CarRequest $request
     * @param integer $id
     * @return void
     */
    public function update(CarRequest $request, int $id) {

        $car = Car::findOrFail($id);
        $car->update($request->validated());

        $car->syncRelations($request->validated());

        return (new CarResource($car));
    }

    /**
     * Função para deletar um carro.
     *
     * @param integer $id
     * @return void
     */
    public function destroy(int $id) {

        $car = Car::findOrFail($id);
        $car->delete();

        return response()->noContent();
    }

}
