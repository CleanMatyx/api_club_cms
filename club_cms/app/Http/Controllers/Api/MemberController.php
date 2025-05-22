<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Http\Resources\MemberResource;
use App\Http\Requests\MemberRequest;

/**
 * @OA\Tag(
 *  name="Members",
 *  description="Operaciones sobre miembros"
 * )
 */
class MemberController extends Controller
{
    /**
     * @OA\Get(
     *  path="/members",
     *  summary="Listar miembros",
     *  tags={"Members"},
     *  security={{"bearerAuth":{}}},
     *  @OA\Response(
     *   response=200,
     *   description="Listado de miembros",
     *   @OA\JsonContent(
     *    type="object",
     *    @OA\Property(property="ok", type="boolean", example=true),
     *    @OA\Property(
     *     property="members",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/MemberResource")
     *    ),
     *    @OA\Property(property="page", type="integer", example=1),
     *    @OA\Property(property="total_pages", type="integer", example=5),
     *    @OA\Property(property="total_members", type="integer", example=50)
     *   )
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="No hay miembros disponibles",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error al obtener los miembros",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
     */
    public function index()
    {
        $members = Member::paginate(15);

        try {
            if(!$members->isEmpty()) {
                return response()->json([
                    'ok' => true,
                    'members' => MemberResource::collection($members),
                    'page' => $members->currentPage(),
                    'total_pages' => $members->lastPage(),
                    'total_members' => $members->total()
                ], 200);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'No hay miembros disponibles'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al obtener los miembros debido a un error inesperado',
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
     *  path="/members",
     *  summary="Crear un nuevo miembro",
     *  tags={"Members"},
     *  security={{"bearerAuth":{}}},
     *  @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(ref="#/components/schemas/MemberRequest")
     *  ),
     *  @OA\Response(
     *   response=201,
     *   description="Miembro creado correctamente",
     *   @OA\JsonContent(
     *    type="object",
     *    @OA\Property(property="ok", type="boolean", example=true),
     *    @OA\Property(property="message", type="string", example="Miembro creado correctamente"),
     *    @OA\Property(property="member", ref="#/components/schemas/MemberResource")
     *   )
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error al crear el miembro",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
     */
    public function store(MemberRequest $request)
    {
        $request->validated();

        $member = Member::create($request->all());

        try {
            if ($member) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Miembro creado correctamente',
                    'member' => new MemberResource($member)
                ], 201);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'Error al crear el miembro'
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al crear el miembro debido a un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *  path="/members/{id}",
     *  summary="Mostrar detalles de un miembro",
     *  tags={"Members"},
     *  security={{"bearerAuth":{}}},
     *  @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="ID del miembro",
     *   required=true,
     *   @OA\Schema(type="integer", example=42)
     *  ),
     *  @OA\Response(
     *   response=200,
     *   description="Detalle del miembro",
     *   @OA\JsonContent(
     *    type="object",
     *    @OA\Property(property="ok", type="boolean", example=true),
     *    @OA\Property(property="member", ref="#/components/schemas/MemberResource")
     *   )
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="Miembro no encontrado",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error al obtener el miembro",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
     */
    public function show(string $id)
    {
        try {
            $member = Member::findOrFail($id);

            if ($member) {
                return response()->json([
                    'ok' => true,
                    'member' => new MemberResource($member)
                ], 200);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'Miembro no encontrado'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al obtener el miembro debido a un error inesperado',
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
     *  path="/members/{id}",
     *  summary="Actualizar un miembro existente",
     *  tags={"Members"},
     *  security={{"bearerAuth":{}}},
     *  @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="ID del miembro a actualizar",
     *   required=true,
     *   @OA\Schema(type="integer", example=42)
     *  ),
     *   @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(ref="#/components/schemas/MemberRequest")
     *  ),
     *  @OA\Response(
     *   response=200,
     *   description="Miembro actualizado correctamente",
     *   @OA\JsonContent(
     *    type="object",
     *    @OA\Property(property="ok", type="boolean", example=true),
     *    @OA\Property(property="message", type="string", example="Miembro actualizado correctamente"),
     *    @OA\Property(property="member", ref="#/components/schemas/MemberResource")
     *   )
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="Miembro no encontrado",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error al actualizar el miembro",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
     */
    public function update(MemberRequest $request, string $id)
    {
        $request->validated();
        $member = Member::findOrFail($id);
        $member->update($request->all());

        try {
            if ($member) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Miembro actualizado correctamente',
                    'member' => new MemberResource($member)
                ], 200);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'Error al actualizar el miembro'
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al actualizar el miembro debido a un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *  path="/members/{id}",
     *  summary="Eliminar un miembro",
     *  tags={"Members"},
     *  security={{"bearerAuth":{}}},
     *  @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="ID del miembro a eliminar",
     *   required=true,
     *   @OA\Schema(type="integer", example=42)
     *  ),
     *  @OA\Response(
     *   response=200,
     *   description="Miembro eliminado correctamente",
     *   @OA\JsonContent(
     *     type="object",
     *     @OA\Property(property="ok", type="boolean", example=true),
     *     @OA\Property(property="message", type="string", example="Miembro eliminado correctamente")
     *   )
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="Miembro no encontrado",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error al eliminar el miembro",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
     */
    public function destroy(string $id)
    {
        $member = Member::findOrFail($id);
        $member->delete();

        try {
            if($member) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Miembro eliminado correctamente'
                ], 200);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'Error al eliminar el miembro'
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al eliminar el miembro debido a un error inesperado',
            ], 500);
        }
    }
}
