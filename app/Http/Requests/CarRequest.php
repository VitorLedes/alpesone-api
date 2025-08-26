<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $externalIdRule = $this->isMethod('POST') ? 'unique:cars,external_id' : 'unique:cars,external_id,' . $this->route('id');

        return [
            'type' => 'required|string',
            'brand' => 'required|string',
            'model' => 'required|string',
            'version' => 'required|string',
            'model_year' => 'required|string',
            'build_year' => 'required|string',
            'doors' => 'required|integer',
            'board' => 'required|string',
            'chassi' => 'nullable|string',
            'transmission' => 'required|string',
            'km' => 'required|string',
            'description' => 'nullable|string',
            'sold' => 'required|string',
            'category' => 'required|string',
            'url_car' => 'required|string',
            'old_price' => 'nullable|string',
            'price' => 'required|string',
            'color' => 'required|string',
            'fuel' => 'required|string',
            'external_id' => 'required|integer| ' . $externalIdRule,
            'fotos' => 'required|array',
            'fotos.*' => 'url|max:500',
            'optionals' => 'nullable|array',
            'optionals.*' => 'integer'
        ];
    }
}
