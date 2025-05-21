<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\CourtRequest;
use App\Models\Court;
use App\Models\Sport;
use App\Http\Controllers\Resources\CourtResource;
use Carbon\Carbon;

class CourtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courts = Court::paginate(15);

        try {
            if (!$courts->isEmpty()) {
                return response()->json([
                    'ok' => true,
                    'courts' => CourtResource::collection($courts),
                    'page' => $courts->currentPage(),
                    'total' => $courts->total()
                ], 200);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'No hay pistas disponibles'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al obtener las pistas debido a un error inesperado',
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
    public function store(CourtRequest $request)
    {
        $request->validated();

        $court = Court::create($request->all());

        try {
            if ($court) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Pista creada correctamente',
                    'court' => $court
                ], 201);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'Error al crear la pista'
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al crear la pista por error insesperado',
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
            $court = Court::findOrFail($id);

            if ($court) {
                return response()->json([
                    'ok' => true,
                    'court' => $court
                ], 200);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'Pista no encontrada'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al obtener la pista por error inesperado',
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
    public function update(CourtRequest $request, string $id)
    {
        $request->validated();
        $court = Court::findOrFail($id);
        $court->update($request->all());

        try {
            if ($court) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Pista actualizada correctamente',
                    'court' => $court
                ], 200);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'Error al actualizar la pista'
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al actualizar la pista por error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $court = Court::findOrFail($id);
        $court->delete();

        try {
            if ($court) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Pista eliminada correctamente'
                ], 200);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'Error al eliminar la pista'
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al eliminar la pista por error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search available fields for a sport, a member and a day
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'sport_name' => 'required|string',
                'date' => 'required|string',
                'member_id' => 'required|integer'
            ]);

            try {
                $validated['date'] = Carbon::createFromFormat('d/m/Y', $validated['date'])->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json([
                    'ok' => false,
                    'message' => 'El formato de la fecha es inválido. Use el formato d/m/Y.'
                ], 400);
            }

            $sportExists = Sport::where('name', $validated['sport_name'])->exists();

            if (!$sportExists) {
                $availableSports = Sport::pluck('name');
                return response()->json([
                    'ok' => false,
                    'message' => 'El deporte consultado no está disponible.',
                    'available_sports' => $availableSports
                ], 404);
            }

            $availableCourts = Court::whereHas('sport', fn($query) => $query->where('name', $validated['sport_name']))->get();

            if ($availableCourts->isEmpty()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No hay pistas disponibles para el deporte especificado.'
                ], 404);
            }

            $availableHours = [];
            foreach ($availableCourts as $court) {
                $reservedHours = $court->reservations()
                    ->whereDate('date', $validated['date'])
                    ->pluck('hour')->map(function($h) { return (int) $h; });

                $allHours = collect(range(8, 21));
                $freeHours = $allHours->diff($reservedHours)->values();

                $availableHours[] = [
                    'id' => $court->id,
                    'name' => $court->name,
                    'hours_free' => $freeHours,
                    'hours_reserved' => $reservedHours->values()
                ];
            }

            if (empty($availableHours)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No hay horas disponibles para el deporte especificado.'
                ], 404);
            } else {
                return response()->json([
                    'ok' => true,
                    'available_hours' => $availableHours
                ], 200);
            }

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Ocurrió un error inesperado.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
