<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\Court;
use App\Models\Sport;
use App\Models\Reservation;
use Carbon\Carbon;
use Laravel\Passport\Passport;

class ReservationValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $member;
    protected $court;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear un usuario y miembro para las pruebas
        $this->user = User::factory()->create();
        $this->member = Member::factory()->create(['user_id' => $this->user->id]);
        
        // Crear un deporte y cancha
        $sport = Sport::factory()->create();
        $this->court = Court::factory()->create(['sport_id' => $sport->id]);
        
        // Autenticar al usuario
        Passport::actingAs($this->user);
    }

    /** @test */
    public function it_prevents_more_than_three_reservations_per_day()
    {
        $date = Carbon::tomorrow()->format('Y-m-d');
        
        // Crear 3 reservas para el mismo día
        for ($i = 0; $i < 3; $i++) {
            Reservation::factory()->create([
                'member_id' => $this->member->id,
                'court_id' => $this->court->id,
                'date' => $date,
                'hour' => sprintf('%02d:00', 9 + $i),
            ]);
        }

        // Intentar crear una cuarta reserva
        $response = $this->postJson('/api/reservations', [
            'member_id' => $this->member->id,
            'court_id' => $this->court->id,
            'date' => $date,
            'hour' => '12:00',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['member_id']);
        $response->assertJsonFragment([
            'member_id' => ['El miembro ya tiene el máximo de 3 reservas permitidas para esta fecha.']
        ]);
    }

    /** @test */
    public function it_prevents_duplicate_reservations_same_court_date_hour()
    {
        $date = Carbon::tomorrow()->format('Y-m-d');
        $hour = '14:00';
        
        // Crear una reserva
        Reservation::factory()->create([
            'member_id' => $this->member->id,
            'court_id' => $this->court->id,
            'date' => $date,
            'hour' => $hour,
        ]);

        // Intentar crear otra reserva para la misma cancha, fecha y hora
        $response = $this->postJson('/api/reservations', [
            'member_id' => $this->member->id,
            'court_id' => $this->court->id,
            'date' => $date,
            'hour' => $hour,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['court_id']);
        $response->assertJsonFragment([
            'court_id' => ['Ya existe una reserva para esta cancha en la misma fecha y hora.']
        ]);
    }

    /** @test */
    public function it_allows_reservations_same_court_different_hour()
    {
        $date = Carbon::tomorrow()->format('Y-m-d');
        
        // Crear una reserva
        Reservation::factory()->create([
            'member_id' => $this->member->id,
            'court_id' => $this->court->id,
            'date' => $date,
            'hour' => '14:00',
        ]);

        // Crear otra reserva para la misma cancha y fecha pero diferente hora
        $response = $this->postJson('/api/reservations', [
            'member_id' => $this->member->id,
            'court_id' => $this->court->id,
            'date' => $date,
            'hour' => '15:00',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'ok',
            'message',
            'reservation'
        ]);
    }

    /** @test */
    public function it_prevents_reservations_in_the_past()
    {
        $pastDate = Carbon::yesterday()->format('Y-m-d');

        $response = $this->postJson('/api/reservations', [
            'member_id' => $this->member->id,
            'court_id' => $this->court->id,
            'date' => $pastDate,
            'hour' => '14:00',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['date']);
        $response->assertJsonFragment([
            'date' => ['La fecha no puede ser anterior a hoy.']
        ]);
    }

    /** @test */
    public function it_allows_updating_existing_reservation_to_same_slot()
    {
        $date = Carbon::tomorrow()->format('Y-m-d');
        $hour = '14:00';
        
        // Crear una reserva
        $reservation = Reservation::factory()->create([
            'member_id' => $this->member->id,
            'court_id' => $this->court->id,
            'date' => $date,
            'hour' => $hour,
        ]);

        // Actualizar la misma reserva manteniendo la fecha y hora
        $response = $this->putJson("/api/reservations/{$reservation->id}", [
            'member_id' => $this->member->id,
            'court_id' => $this->court->id,
            'date' => $date,
            'hour' => $hour,
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/reservations', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['member_id', 'court_id', 'date', 'hour']);
    }

    /** @test */
    public function it_validates_existing_member_and_court()
    {
        $response = $this->postJson('/api/reservations', [
            'member_id' => 999, // ID no existente
            'court_id' => 999,  // ID no existente
            'date' => Carbon::tomorrow()->format('Y-m-d'),
            'hour' => '14:00',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['member_id', 'court_id']);
    }
}
