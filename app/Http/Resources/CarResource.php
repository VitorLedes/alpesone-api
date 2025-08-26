<?php

namespace App\Http\Resources;

use App\Models\CarOptionals;
use App\Models\CarPictures;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->type,
            'brand' => $this->brand,
            'model' => $this->model,
            'version' => $this->version,
            'model_year' => $this->model,
            'build_year' => $this->build,
            'doors' => $this->doors,
            'board' => $this->board,
            'chassi' => $this->chassi,
            'transmission' => $this->transmission,
            'km' => $this->km,
            'description' => $this->description,
            'sold' => $this->sold,
            'category' => $this->category,
            'url_car' => $this->url_car,
            'old_price' => $this->old_price,
            'price' => $this->price,
            'color' => $this->color,
            'fuel' => $this->fuel,
            'external_id' => $this->external_id,
            'pictures' => $this->pictures->pluck('pic_url'),
            'optionals' => $this->optionals
        ];
    }
}
