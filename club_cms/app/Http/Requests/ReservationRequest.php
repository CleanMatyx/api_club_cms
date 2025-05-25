<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Member;
use Carbon\Carbon;

class ReservationRequest extends FormRequest
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
            'member_id' => 'required|integer|exists:members,id',
            'court_id' => 'required|integer|exists:courts,id',
            'date' => 'required|date|after_or_equal:today',
            'hour' => 'required|date_format:H:i',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $this->validateReservationLimits($validator);
        });
    }

    /**
     * Validate reservation limits using Member model methods.
     */
    protected function validateReservationLimits(Validator $validator)
    {
        if (!$this->has(['member_id', 'date', 'hour', 'court_id'])) {
            return;
        }

        $member = Member::find($this->member_id);
        if (!$member) {
            return;
        }

        $reservationDate = Carbon::parse($this->date);

        if (!$member->checkMaxReservations($reservationDate)) {
            $validator->errors()->add(
                'member_id', 
                'El miembro ya tiene el máximo de 3 reservas permitidas para esta fecha.'
            );
        }

        if (!$member->checkReservationsSameDate($this->court_id, $reservationDate, $this->hour)) {
            $isUpdate = $this->route('reservation');
            
            if ($isUpdate) {
                $existingReservation = $member->reservations()
                    ->where('court_id', $this->court_id)
                    ->whereDate('date', $reservationDate)
                    ->whereTime('hour', $this->hour)
                    ->first();
                
                if ($existingReservation && $existingReservation->id != $this->route('reservation')->id) {
                    $validator->errors()->add(
                        'court_id', 
                        'Ya existe una reserva para esta cancha en la misma fecha y hora.'
                    );
                }
            } else {
                $validator->errors()->add(
                    'court_id', 
                    'Ya existe una reserva para esta cancha en la misma fecha y hora.'
                );
            }
        }
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'member_id.required' => 'El ID del miembro es obligatorio.',
            'member_id.integer' => 'El ID del miembro debe ser un número entero.',
            'member_id.exists' => 'El miembro seleccionado no existe.',
            'court_id.required' => 'El ID de la cancha es obligatorio.',
            'court_id.integer' => 'El ID de la cancha debe ser un número entero.',
            'court_id.exists' => 'La cancha seleccionada no existe.',
            'date.required' => 'La fecha es obligatoria.',
            'date.date' => 'La fecha debe tener un formato válido.',
            'date.after_or_equal' => 'La fecha no puede ser anterior a hoy.',
            'hour.required' => 'La hora es obligatoria.',
            'hour.date_format' => 'La hora debe tener el formato HH:MM.',
        ];
    }
}
