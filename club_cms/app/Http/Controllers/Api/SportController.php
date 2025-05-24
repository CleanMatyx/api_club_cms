<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use App\Http\Resources\SportResource;
use App\Http\Requests\SportRequest;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @OA\Tag(
 *   name="Sports",
 *   description="Operaciones sobre deportes"
 * )
 */
class SportController extends Controller
{
    /**
     * @OA\Get(
     *   path="/sports",
     *   summary="Listar deportes",
     *   description="Obtiene una lista paginada de todos los deportes. Accesible para usuarios y administradores autenticados.",
     *   tags={"Sports"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Listado de deportes",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="ok", type="boolean", example=true),
     *       @OA\Property(
     *         property="sports",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/SportResource")
     *       ),
     *       @OA\Property(property="page", type="integer", example=1),
     *       @OA\Property(property="total_pages", type="integer", example=5),
     *       @OA\Property(property="total_sports", type="integer", example=50)
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="No autorizado - Token inválido o faltante",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="No hay deportes disponibles",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Error al obtener los deportes",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   )
     * )
     */
    public function index()
    {
        try {
            $sports = Sport::paginate(15);
            if(!$sports->isEmpty()) {
                return response()->json([
                    'ok' => true,
                    'sports' => SportResource::collection($sports),
                    'page' => $sports->currentPage(),
                    'total_pages' => $sports->lastPage(),
                    'total_sports' => $sports->total()
                ], 200);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'No hay deportes disponibles'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al obtener los deportes debido a un error inesperado',
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
     *  path="/sports",
     *  summary="Crear un nuevo deporte (Solo Admins)",
     *  description="Crea un nuevo deporte en el sistema. Requiere rol de administrador.",
     *  tags={"Sports"},
     *  security={{"bearerAuth":{}}},
     *  @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(ref="#/components/schemas/SportRequest")
     *  ),
     *  @OA\Response(
     *   response=201,
     *   description="Deporte creado correctamente",
     *   @OA\JsonContent(
     *    type="object",
     *    @OA\Property(property="ok", type="boolean", example=true),
     *    @OA\Property(property="sport", ref="#/components/schemas/SportResource")
     *   )
     *  ),
     *  @OA\Response(
     *   response=401,
     *   description="No autorizado - Token inválido o faltante",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="Deporte no encontrado",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=422,
     *   description="Error de validación",
     *   @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error al crear el deporte",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
     */
    public function store(SportRequest $request)
    {
        try {
            $request->validated();
            $sport = Sport::create($request->all());

            if($sport) {
                return response()->json([
                    'ok' => true,
                    'sport' => new SportResource($sport)
                ], 201);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al crear el deporte',
                'error' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al crear el deporte debido a un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *  path="/sports/{id}",
     *  summary="Mostrar detalles de un deporte",
     *  description="Obtiene los detalles de un deporte específico. Accesible para usuarios y administradores autenticados.",
     *  tags={"Sports"},
     *  security={{"bearerAuth":{}}},
     *  @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="ID del deporte",
     *   required=true,
     *   @OA\Schema(type="integer", example=1)
     *  ),
     *  @OA\Response(
     *   response=200,
     *   description="Detalle del deporte",
     *   @OA\JsonContent(
     *    type="object",
     *    @OA\Property(property="ok", type="boolean", example=true),
     *    @OA\Property(property="sport", ref="#/components/schemas/SportResource")
     *   )
     *  ),
     *  @OA\Response(
     *   response=401,
     *   description="No autorizado - Token inválido o faltante",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="Deporte no encontrado",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error al obtener el deporte",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
     */
    public function show(string $id)
    {
        try {
            $sport = Sport::findOrFail($id);

            return response()->json([
                'ok' => true,
                'sport' => new SportResource($sport)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Deporte no encontrado'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al obtener el deporte debido a un error inesperado',
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
     *  path="/sports/{id}",
     *  summary="Actualizar un deporte existente (Solo Admins)",
     *  description="Actualiza los datos de un deporte existente. Requiere rol de administrador.",
     *  tags={"Sports"},
     *  security={{"bearerAuth":{}}},
     *  @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="ID del deporte a actualizar",
     *   required=true,
     *   @OA\Schema(type="integer", example=1)
     *  ),
     *  @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(ref="#/components/schemas/SportRequest")
     *  ),
     *  @OA\Response(
     *   response=200,
     *   description="Deporte actualizado correctamente",
     *   @OA\JsonContent(
     *    type="object",
     *    @OA\Property(property="ok", type="boolean", example=true),
     *    @OA\Property(property="sport", ref="#/components/schemas/SportResource")
     *   )
     *  ),
     *  @OA\Response(
     *   response=401,
     *   description="No autorizado - Token inválido o faltante",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="Deporte no encontrado",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *    response=422,
     *    description="Error de validación",
     *    @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error al actualizar el deporte",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
     */
    public function update(SportRequest $request, string $id)
    {
        try {
            $request->validated();
            $sport = Sport::findOrFail($id);
            $sport->update($request->all());
            return response()->json([
                'ok' => true,
                'sport' => new SportResource($sport)
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Deporte no encontrado'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al actualizar el deporte debido a un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *   path="/sports/{id}",
     *   summary="Eliminar un deporte (Solo Admins)",
     *   description="Elimina un deporte del sistema. Requiere rol de administrador.",
     *   tags={"Sports"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID del deporte a eliminar",
     *     required=true,
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Deporte eliminado correctamente",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="ok", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="Deporte eliminado correctamente")
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="No autorizado - Token inválido o faltante",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Error al eliminar el deporte",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $sport = Sport::findOrFail($id);
            $sport->delete();
            if($sport) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Deporte eliminado correctamente'
                ], 200);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'Error al eliminar el deporte'
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al eliminar el deporte debido a un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
