<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarPictures extends Model
{
    /**
     * Pega o carro
     *
     * @return void
     */
    public function car() {
        return $this->belongsTo(Car::class);
    }

    protected $fillable = [
        'car_id',
        'pic_url'
    ];

}
