<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
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
        $memberId = $this->route('id');
        
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'membership_date' => 'required|date',
            'status' => 'required|string|in:active,inactive,suspended',
        ];

        if ($this->isMethod('post')) {
            $rules['email'] = 'nullable|email|max:255|unique:members,email';
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['email'] = "nullable|email|max:255|unique:members,email,{$memberId}";
        } else {
            $rules['email'] = 'nullable|email|max:255';
        }

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'El correo electrónico ya está registrado por otro miembro.',
            'name.required' => 'El nombre es obligatorio.',
            'membership_date.required' => 'La fecha de membresía es obligatoria.',
            'membership_date.date' => 'La fecha de membresía debe ser una fecha válida.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser: active, inactive o suspended.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
        ];
    }
}
