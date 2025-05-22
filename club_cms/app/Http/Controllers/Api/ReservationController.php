<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use App\Http\Requests\ReservationRequest;

/**
 * @OA\Tag(
 *   name="Reservations",
 *   description="Operaciones sobre reservas"
 * )
 */
class ReservationController extends Controller
{
    /**
     * @OA\Get(
     *   path="/reservations",
     *   summary="Listar reservas",
     *   tags={"Reservations"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Listado de reservas",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="ok",                type="boolean", example=true),
     *       @OA\Property(
     *         property="reservations",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/ReservationResource")
     *       ),
     *       @OA\Property(property="page",              type="integer", example=1),
     *       @OA\Property(property="total_pages",       type="integer", example=5),
     *       @OA\Property(property="total_reservations", type="integer", example=50)
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="No hay reservas disponibles",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Error al obtener las reservas",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   )
     * )
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
     * @OA\Post(
     *   path="/reservations",
     *   summary="Crear una nueva reserva",
     *   tags={"Reservations"},
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/ReservationRequest")
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Reserva creada correctamente",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="ok",          type="boolean", example=true),
     *       @OA\Property(property="reservation", ref="#/components/schemas/ReservationResource")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Error al crear la reserva",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   )
     * )
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
     * @OA\Get(
     *   path="/reservations/{id}",
     *   summary="Mostrar detalles de una reserva",
     *   tags={"Reservations"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID de la reserva",
     *     required=true,
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Detalle de la reserva",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="ok",         type="boolean", example=true),
     *       @OA\Property(property="reservation", ref="#/components/schemas/ReservationResource")
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Reserva no encontrada",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Error al obtener la reserva",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   )
     * )
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
     * @OA\Put(
     *   path="/reservations/{id}",
     *   summary="Actualizar una reserva existente",
     *   tags={"Reservations"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID de la reserva a actualizar",
     *     required=true,
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/ReservationRequest")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Reserva actualizada correctamente",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="ok",         type="boolean", example=true),
     *       @OA\Property(property="reservation",ref="#/components/schemas/ReservationResource")
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Reserva no encontrada",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Error al actualizar la reserva",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   )
     * )
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * @OA\Delete(
     *   path="/reservations/{id}",
     *   summary="Eliminar una reserva",
     *   tags={"Reservations"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID de la reserva a eliminar",
     *     required=true,
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Reserva eliminada correctamente",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="ok",      type="boolean", example=true),
     *       @OA\Property(property="message", type="string",  example="Reserva eliminada correctamente")
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Reserva no encontrada",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Error al eliminar la reserva",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   )
     * )
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
