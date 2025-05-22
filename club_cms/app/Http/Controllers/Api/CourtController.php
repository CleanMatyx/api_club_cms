<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\CourtRequest;
use App\Models\Court;
use App\Models\Sport;
use App\Http\Resources\CourtResource;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *  name="Courts",
 *  description="Operaciones sobre pistas"
 * )
 */
class CourtController extends Controller
{
    /**
     * @OA\Get(
     *  path="/courts",
     *  summary="Listar pistas",
     *  tags={"Courts"},
     *  security={{"bearerAuth":{}}},
     *  @OA\Response(
     *   response=200,
     *   description="Listado de pistas",
     *   @OA\JsonContent(
     *    type="object",
     *    @OA\Property(property="ok", type="boolean", example=true),
     *    @OA\Property(
     *     property="courts",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/CourtResource")
     *    ),
     *    @OA\Property(property="page", type="integer", example=1),
     *    @OA\Property(property="total_pages", type="integer", example=5),
     *    @OA\Property(property="total_courts", type="integer", example=50)
     *   )
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="No hay pistas disponibles",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error al obtener las pistas",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
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
                    'total_pages' => $courts->lastPage(),
                    'total_courts' => $courts->total()
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
     * @OA\Post(
     *  path="/courts",
     *  summary="Crear una nueva pista",
     *  tags={"Courts"},
     *  security={{"bearerAuth":{}}},
     *  @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(ref="#/components/schemas/CourtRequest")
     *  ),
     *  @OA\Response(
     *   response=201,
     *   description="Pista creada correctamente",
     *   @OA\JsonContent(
     *    type="object",
     *    @OA\Property(property="ok", type="boolean", example=true),
     *    @OA\Property(property="message", type="string", example="Pista creada correctamente"),
     *    @OA\Property(property="court", ref="#/components/schemas/CourtResource")
     *   )
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error al crear la pista",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
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
                    'court' => new CourtResource($court)
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
     * @OA\Get(
     *  path="/courts/{id}",
     *  summary="Mostrar detalles de una pista",
     *  tags={"Courts"},
     *  security={{"bearerAuth":{}}},
     *  @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="ID de la pista",
     *   required=true,
     *   @OA\Schema(type="integer", example=1)
     *  ),
     *  @OA\Response(
     *   response=200,
     *   description="Datos de la pista",
     *   @OA\JsonContent(
     *     type="object",
     *     @OA\Property(property="ok", type="boolean", example=true),
     *     @OA\Property(property="court", ref="#/components/schemas/CourtResource")
     *   )
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="Pista no encontrada",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error al obtener la pista",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
     */
    public function show(string $id)
    {
        try {
            $court = Court::findOrFail($id);

            return response()->json([
                'ok' => true,
                'court' => new CourtResource($court)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Pista no encontrada'
            ], 404);
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
     * @OA\Put(
     *  path="/courts/{id}",
     *  summary="Actualizar una pista existente",
     *  tags={"Courts"},
     *  security={{"bearerAuth":{}}},
     *  @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="ID de la pista a actualizar",
     *   required=true,
     *   @OA\Schema(type="integer", example=1)
     *  ),
     *  @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(ref="#/components/schemas/CourtRequest")
     *  ),
     *  @OA\Response(
     *   response=200,
     *   description="Pista actualizada correctamente",
     *   @OA\JsonContent(
     *    type="object",
     *    @OA\Property(property="ok",      type="boolean", example=true),
     *    @OA\Property(property="message", type="string",  example="Pista actualizada correctamente"),
     *    @OA\Property(property="court",   ref="#/components/schemas/CourtResource")
     *   )
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="Pista no encontrada",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error al actualizar la pista",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
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
                    'court' => new CourtResource($court)
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
     * @OA\Delete(
     *  path="/courts/{id}",
     *  summary="Eliminar una pista",
     *  tags={"Courts"},
     *  security={{"bearerAuth":{}}},
     *  @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="ID de la pista a eliminar",
     *   required=true,
     *   @OA\Schema(type="integer", example=1)
     *  ),
     *  @OA\Response(
     *   response=200,
     *   description="Pista eliminada correctamente",
     *   @OA\JsonContent(
     *    type="object",
     *    @OA\Property(property="ok",      type="boolean", example=true),
     *    @OA\Property(property="message", type="string",  example="Pista eliminada correctamente")
     *   )
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="Pista no encontrada",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error al eliminar la pista",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
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
     * @OA\Get(
     *  path="/search",
     *  summary="Buscar disponibilidad de pistas y horas para un deporte, un socio y una fecha",
     *  tags={"Search Courts"},
     *  security={{"bearerAuth":{}}},
     *  @OA\Parameter(
     *   name="sport_name",
     *   in="query",
     *   description="Nombre del deporte a consultar",
     *   required=true,
     *   @OA\Schema(type="string", example="tenis")
     *  ),
     *  @OA\Parameter(
     *   name="date",
     *   in="query",
     *   description="Fecha en formato d/m/Y",
     *   required=true,
     *   @OA\Schema(type="string", format="date", example="22/05/2025")
     *  ),
     *  @OA\Parameter(
     *   name="member_id",
     *   in="query",
     *   description="ID del socio",
     *   required=true,
     *   @OA\Schema(type="integer", example=42)
     *  ),
     *  @OA\Response(
     *   response=200,
     *   description="Disponibilidad encontrada",
     *   @OA\JsonContent(
     *    @OA\Property(property="ok", type="boolean", example=true),
     *    @OA\Property(
     *     property="available_hours",
     *     type="array",
     *     @OA\Items(
     *      type="object",
     *      @OA\Property(property="id", type="integer", example=1),
     *      @OA\Property(property="name", type="string", example="Pista Central"),
     *      @OA\Property(
     *       property="hours_free",
     *       type="array",
     *       @OA\Items(type="integer"),
     *       example={8,9,10,11}
     *      ),
     *      @OA\Property(
     *       property="hours_reserved",
     *       type="array",
     *       @OA\Items(type="integer"),
     *       example={12,13}
     *      )
     *     )
     *    )
     *   )
     *  ),
     *  @OA\Response(
     *   response=400,
     *   description="Formato de fecha inválido",
     *   @OA\JsonContent(
     *    @OA\Property(property="ok", type="boolean", example=false),
     *    @OA\Property(property="message", type="string", example="El formato de la fecha es inválido. Use el formato d/m/Y.")
     *   )
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="Deporte no encontrado, pistas u horas no disponibles",
     *   @OA\JsonContent(
     *    @OA\Property(property="ok", type="boolean", example=false),
     *    @OA\Property(property="message", type="string", example="El deporte consultado no está disponible."),
     *    @OA\Property(
     *     property="available_sports",
     *     type="array",
     *     @OA\Items(type="string"),
     *     example={"fútbol","pádel","tenis"}
     *    )
     *   )
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error inesperado",
     *   @OA\JsonContent(
     *    @OA\Property(property="ok", type="boolean", example=false),
     *    @OA\Property(property="message", type="string", example="Ocurrió un error inesperado."),
     *    @OA\Property(property="error", type="string", example="Detalle del error interno")
     *   )
     *  )
     * )
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
