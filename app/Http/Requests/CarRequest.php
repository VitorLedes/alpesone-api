<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarRequest extends FormRequest
{
    protected $fromCommand = false;

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
        $rules = [
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
            'fotos' => 'required|array',
            'fotos.*' => 'url|max:500',
            'optionals' => 'nullable|array',
            'optionals.*' => 'integer'
        ];

        if ($this->fromCommand) {
            $rules['external_id'] = 'required|integer';
        } else {
            $externalIdRule = $this->isMethod('POST')
                ? 'unique:cars,external_id'
                : 'unique:cars,external_id,' . $this->route('id');
            $rules['external_id'] = 'required|integer|' . $externalIdRule;
        }

        return $rules;
    }

    public function setFromCommand(bool $value = false) {
        $this->fromCommand = $value;
    }

}
