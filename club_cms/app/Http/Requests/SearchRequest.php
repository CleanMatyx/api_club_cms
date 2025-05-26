<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
            'sport_name' => 'required|string',
            'date' => 'required|string',
            'member_id' => 'required|integer'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sport_name.required' => 'El nombre del deporte es obligatorio.',
            'sport_name.string' => 'El nombre del deporte debe ser una cadena de texto válida.',
            'date.required' => 'La fecha es obligatoria.',
            'date.string' => 'La fecha debe ser una cadena de texto válida.',
            'member_id.required' => 'El ID del miembro es obligatorio.',
            'member_id.integer' => 'El ID del miembro debe ser un número entero válido.'
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'sport_name' => 'nombre del deporte',
            'date' => 'fecha',
            'member_id' => 'ID del miembro'
        ];
    }
}
