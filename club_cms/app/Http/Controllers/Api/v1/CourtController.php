<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\CourtRequest;
use App\Models\Court;
use App\Models\Sport;
use App\Http\Resources\CourtResource;
use App\Http\Requests\SearchRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

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
     *  description="Obtiene una lista paginada de todas las pistas. Accesible para usuarios y administradores autenticados.",
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
     *   response=401,
     *   description="No autorizado - Token inválido o faltante",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
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
        try {
            $courts = Court::paginate(15);
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
                'message' => 'Error interno del servidor al obtener las canchas.'
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
     *  summary="Crear una nueva pista (Solo Admins)",
     *  description="Crea una nueva pista en el sistema. Requiere rol de administrador.",
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
     *   response=401,
     *   description="No autorizado - Token inválido o faltante",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="Pista no encontrada",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *    response=422,
     *    description="Error de validación",
     *    @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
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
        try {
            $validatedData = $request->validated();
            $court = Court::create($validatedData);

            return response()->json([
                'ok' => true,
                'message' => 'Pista creada correctamente',
                'court' => new CourtResource($court)
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error interno del servidor al crear la pista.'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *  path="/courts/{id}",
     *  summary="Mostrar detalles de una pista",
     *  description="Obtiene los detalles de una pista específica. Accesible para usuarios y administradores autenticados.",
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
     *   response=401,
     *   description="No autorizado - Token inválido o faltante",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
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
                'message' => 'Error interno del servidor al obtener la cancha.'
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
     *  summary="Actualizar una pista existente (Solo Admins)",
     *  description="Actualiza los datos de una pista existente. Requiere rol de administrador.",
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
     *    @OA\Property(property="ok", type="boolean", example=true),
     *    @OA\Property(property="message", type="string",  example="Pista actualizada correctamente"),
     *    @OA\Property(property="court", ref="#/components/schemas/CourtResource")
     *   )
     *  ),
     *  @OA\Response(
     *   response=401,
     *   description="No autorizado - Token inválido o faltante",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="Pista no encontrada",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *    response=422,
     *    description="Error de validación",
     *    @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
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
        try {
            $validatedData = $request->validated();
            $court = Court::findOrFail($id);
            $court->update($validatedData);

            return response()->json([
                'ok' => true,
                'message' => 'Pista actualizada correctamente',
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
                'message' => 'Error interno del servidor al actualizar la pista.'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *  path="/courts/{id}",
     *  summary="Eliminar una pista (Solo Admins)",
     *  description="Elimina una pista del sistema. Requiere rol de administrador.",
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
     *    @OA\Property(property="ok", type="boolean", example=true),
     *    @OA\Property(property="message", type="string",  example="Pista eliminada correctamente")
     *   )
     *  ),
     *  @OA\Response(
     *   response=401,
     *   description="No autorizado - Token inválido o faltante",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
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
        try {
            $court = Court::findOrFail($id);
            $court->delete();
            
            return response()->json([
                'ok' => true,
                'message' => 'Pista eliminada correctamente'
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Pista no encontrada'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error interno del servidor al eliminar la pista.'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *  path="/courts/search",
     *  summary="Buscar disponibilidad de pistas y horas para un deporte, un socio y una fecha",
     *  description="Busca la disponibilidad de pistas para un deporte específico en una fecha determinada. Accesible para usuarios y administradores autenticados.",
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
     *   response=401,
     *   description="No autorizado - Token inválido o faltante",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
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
     *   @OA\Response(
     *    response=422,
     *    description="Error de validación",
     *    @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
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
    public function search(SearchRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            try {
                $data['date'] = Carbon::createFromFormat('d/m/Y', $data['date'])->format('Y-m-d');
            } catch (\Exception $e) {
                throw ValidationException::withMessages([
                    'date' => ['El formato de la fecha es inválido. Use el formato d/m/Y.']
                ]);
            }

            $sport = Sport::where('name', $data['sport_name'])->first();
            if (!$sport) {
                $availableSports = Sport::pluck('name')->toArray();
                return response()->json([
                    'ok' => false,
                    'message' => 'El deporte consultado no está disponible.',
                    'available_sports' => $availableSports
                ], 404);
            }

            $courts = Court::where('sport_id', $sport->id)->get();
            if ($courts->isEmpty()) {
                throw new ModelNotFoundException("No hay pistas disponibles para el deporte especificado.");
            }

            $available_hours = [];
            foreach ($courts as $court) {
                $reserved = $court->reservations()->whereDate('date', $data['date'])->pluck('hour')->map(fn($h) => (int) $h);
                $allHours  = collect(range(8, 21));
                $freeHours = $allHours->diff($reserved)->values();

                $available_hours[] = [
                    'id' => $court->id,
                    'name' => $court->name,
                    'hours_free' => $freeHours,
                    'hours_reserved' => $reserved->values(),
                ];
            }

            if (empty($available_hours)) {
                throw new ModelNotFoundException("No hay horas disponibles para el deporte especificado.");
            }

            return response()->json([
                'ok' => true,
                'available_hours' => $available_hours,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'No hay horas disponibles para el deporte especificado.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'ok'=> false,
                'message' => 'Error interno del servidor al buscar horarios disponibles.'
            ], 500);
        }
    }
}
