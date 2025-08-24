<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $isPostMethod = $this->isMethod('post');
        
        $emailRule = $isPostMethod ? 'unique:users' : 'unique:users,email,' . $this->route('id');
        $passwordRule = $isPostMethod ? 'required|string|min:8|max:20' : 'nullable|string|min:8|max:20';

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|' . $emailRule,
            'password' => $passwordRule,
        ];
    }


    public function messages(): array {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O campo nome não deve exceder 255 caracteres.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O campo email deve ser um endereço de email válido.',
            'email.max' => 'O campo email não deve exceder 255 caracteres.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'O campo senha deve ter no mínimo 8 caracteres.',
            'password.max' => 'O campo senha deve ter no máximo 20 caracteres.',
        ];
    }
}
