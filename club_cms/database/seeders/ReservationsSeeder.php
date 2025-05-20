<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\Member;
use App\Models\Court;
use Illuminate\Support\Facades\DB;

class ReservationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = Member::where('status', true)->get();
        $courts = Court::all();

        // Itero miembro a miembro activo para simular reservas
        foreach ($members as $member) {
            for ($day = 0; $day < 7; $day++) {
                $date = now()->addDays($day)->toDateString();
                $reservasHoy = 0;

                foreach ($courts as $court) {
                    // Restricción máximo 2 reservas por día por miembro
                    if ($reservasHoy >= 2) {
                        break;
                    }

                    // Generar una hora aleatoria con un 50% de probabilidad de crear una reserva
                    if (rand(0, 1) === 0) {
                        continue;
                    }

                    $hour = rand(8, 21);

                    // Verificar si la pista ya está reservada a esta hora
                    $existingReservation = DB::table('reservations')
                        ->where('court_id', $court->id)
                        ->where('date', $date)
                        ->where('hour', sprintf('%02d:00:00', $hour))
                        ->exists();

                    if ($existingReservation) {
                        continue;
                    }

                    // Verificar si el miembro ya tiene una reserva a esta hora
                    $memberReservation = DB::table('reservations')
                        ->where('member_id', $member->id)
                        ->where('date', $date)
                        ->where('hour', sprintf('%02d:00:00', $hour))
                        ->exists();

                    if ($memberReservation) {
                        continue;
                    }

                    // Crear la reserva
                    Reservation::create([
                        'member_id' => $member->id,
                        'court_id' => $court->id,
                        'date' => $date,
                        'hour' => sprintf('%02d:00:00', $hour),
                    ]);

                    $reservasHoy++;
                }
            }
        }
    }
}
