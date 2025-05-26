<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Member;
use App\Models\Reservation;
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

        $reservationDate = Carbon::parse($this->date);
        
        if ($reservationDate->isPast()) {
            return;
        }

        $hour = Carbon::createFromFormat('H:i', $this->hour);
        $businessStart = Carbon::createFromFormat('H:i', '08:00');
        $businessEnd = Carbon::createFromFormat('H:i', '21:00');
        
        if ($hour->lt($businessStart) || $hour->gt($businessEnd)) {
            $validator->errors()->add(
                'hour', 
                'Las reservas solo están permitidas entre las 08:00 y las 21:00.'
            );
            return;
        }

        $member = Member::find($this->member_id);
        if (!$member) {
            return;
        }

        $reservationId = $this->route('reservation');

        $existingReservationsCount = $member->reservations()
            ->whereDate('date', $reservationDate)
            ->when($reservationId, function ($query) use ($reservationId) {
                return $query->where('id', '!=', $reservationId);
            })
            ->count();

        if ($existingReservationsCount >= 3) {
            $validator->errors()->add(
                'member_id', 
                'El miembro ya tiene el máximo de 3 reservas permitidas para esta fecha.'
            );
        }

        $courtOccupied = Reservation::where('court_id', $this->court_id)
            ->whereDate('date', $reservationDate)
            ->whereTime('hour', $this->hour)
            ->when($reservationId, function ($query) use ($reservationId) {
                return $query->where('id', '!=', $reservationId);
            })
            ->first();

        if ($courtOccupied) {
            $validator->errors()->add(
                'court_id', 
                'La cancha ya está ocupada en esta fecha y hora.'
            );
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
