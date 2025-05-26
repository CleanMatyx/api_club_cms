<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourtRequest extends FormRequest
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
        return [
            'sport_id' => 'required|integer|exists:sports,id',
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
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
            'sport_id.required' => 'El campo deporte es obligatorio.',
            'sport_id.integer' => 'El deporte debe ser un número entero válido.',
            'sport_id.exists' => 'El deporte seleccionado no existe.',
            'name.required' => 'El campo nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'location.string' => 'La ubicación debe ser una cadena de texto.',
            'location.max' => 'La ubicación no puede tener más de 255 caracteres.',
        ];
    }
}
