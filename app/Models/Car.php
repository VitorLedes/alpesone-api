<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    /**
     * Pega as fotos do carro
     *
     * @return void
     */
    public function pictures() {
        return $this->hasMany(CarPictures::class, 'car_id', 'id');
    }

    /**
     * Pega os opcionais do carro
     *
     * @return void
     */
    public function optionals() {
        return $this->belongsToMany(Optionals::class, 'car_optionals', 'car_id', 'optional_id');
    }

    /** @use HasFactory<\Database\Factories\CarFactory> */
    use HasFactory;

    protected $fillable = [
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
    ];

    public function scopeGetCarData($query) {
        return $query->with(['pictures', 'optionals']);
    }

    /**
     * Função para sincronizar (salvar, editar e delatar) fotos do carro
     *
     * @param Car $car
     * @param array $newPictures
     * @return void
     */
    public function syncPictures(array $newPictures) {
        $existingPics = $this->pictures()->pluck('pic_url')->toArray();

        foreach ($existingPics as $pic) {
            if (!in_array($pic, $newPictures)) {
                $this->pictures()->where('pic_url', $pic)->delete();
            }
        }

        $toAdd = array_diff($newPictures, $existingPics);

        foreach($toAdd as $pic) {
            $this->pictures()->create(['pic_url' => $pic]);
        }

    }

    public function syncRelations(array $data) {
        $this->syncPictures($data['fotos']);

        // Sincronizando os opcionais (Pelo que vi o JSON nunca mandava optionals, mas decidi tratá-los como IDS se viessem)
        $this->optionals()->sync($data['optionals'] ?? []);
    }

}
