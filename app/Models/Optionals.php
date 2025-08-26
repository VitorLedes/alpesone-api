<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Optionals extends Model
{
    use HasFactory;

    /**
     * Pega os carros
     *
     * @return void
     */
    public function cars() {
        return $this->belongsToMany(Car::class, 'car_optionals', 'optional_id', 'car_id');
    }

    protected $fillable = [
        'name'
    ];

}
