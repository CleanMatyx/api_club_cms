<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use App\Http\Requests\ReservationRequest;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservation::paginate(15);

        try {
            if(!$reservations->isEmpty()) {
                return response()->json([
                    'ok' => true,
                    'reservations' => ReservationResource::collection($reservations),
                    'page' => $reservations->currentPage(),
                    'total_pages' => $reservations->lastPage(),
                    'total_reservations' => $reservations->total()
                ], 200);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'No hay reservas disponibles'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al obtener las reservas debido a un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReservationRequest $request)
    {
        $request->validated();

        $reservation = Reservation::create($request->all());

        try {
            if($reservation) {
                return response()->json([
                    'ok' => true,
                    'reservation' => new ReservationResource($reservation)
                ], 201);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'Error al crear la reserva'
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al crear la reserva debido a un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $reservation = Reservation::findOrFail($id);

            return response()->json([
                'ok' => true,
                'reservation' => new ReservationResource($reservation)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Reserva no encontrada'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al obtener la reserva debido a un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReservationRequest $request, string $id)
    {
        $request->validated();
        $reservation = Reservation::findOrFail($id);
        $reservation->update($request->all());

        try {
            if($reservation) {
                return response()->json([
                    'ok' => true,
                    'reservation' => new ReservationResource($reservation)
                ], 200);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'Error al actualizar la reserva'
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al actualizar la reserva debido a un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        try {
            if($reservation) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Reserva eliminada correctamente'
                ], 200);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'Error al eliminar la reserva'
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al eliminar la reserva debido a un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
