<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $userId = $this->route('id') ?? $this->route('user');
        
        return [
            'name' => 'required|string|max:255',
            'email' => ['required','string','email','max:255',Rule::unique('users', 'email')->ignore($userId)],
            'password' => $this->isMethod('POST') 
                ? 'required|string|min:8|confirmed' 
                : 'nullable|string|min:8|confirmed',
            'role' => 'nullable|string|in:user,admin',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'email.required' => 'El campo email es obligatorio.',
            'email.string' => 'El email debe ser una cadena de texto.',
            'email.email' => 'El formato del email no es válido.',
            'email.max' => 'El email no puede tener más de 255 caracteres.',
            'email.unique' => 'Este email ya está registrado.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'role.string' => 'El rol debe ser una cadena de texto.',
            'role.in' => 'El rol debe ser: user o admin.',
        ];
    }
}
