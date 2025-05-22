<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Tag(
 *   name="Users",
 *   description="Operaciones sobre usuarios"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *   path="/users",
     *   summary="Listar usuarios",
     *   tags={"Users"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Listado de usuarios",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="ok", type="boolean", example=true),
     *       @OA\Property(
     *         property="users",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/UserResource")
     *       ),
     *       @OA\Property(property="page", type="integer", example=1),
     *       @OA\Property(property="total_pages", type="integer", example=5),
     *       @OA\Property(property="total_users", type="integer", example=50)
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="No hay usuarios disponibles",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Error al obtener los usuarios",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   )
     * )
     */
    public function index()
    {
        try {
            $users = User::paginate(15);
            if(!$users->isEmpty()) {
                return response()->json([
                    'ok' => true,
                    'users' => UserResource::collection($users),
                    'page' => $users->currentPage(),
                    'total_pages' => $users->lastPage(),
                    'total_users' => $users->total()
                ], 200);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'No hay usuarios disponibles'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al obtener los usuarios debido a un error inesperado',
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
     *  path="/users",
     *  summary="Crear un nuevo usuario",
     *  tags={"Users"},
     *  security={{"bearerAuth":{}}},
     *  @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(ref="#/components/schemas/UserRequest")
     *  ),
     *  @OA\Response(
     *   response=201,
     *   description="Usuario creado correctamente",
     *   @OA\JsonContent(
     *    type="object",
     *    @OA\Property(property="ok", type="boolean", example=true),
     *    @OA\Property(property="message", type="string", example="Usuario creado correctamente"),
     *    @OA\Property(property="user", ref="#/components/schemas/UserResource")
     *   )
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="Usuario no encontrado",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *    response=422,
     *    description="Error de validación",
     *    @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error al crear el usuario",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
     */
    public function store(UserRequest $request)
    {
        try {
            $request->validated();
            $data = $request->all();
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);

            return response()->json([
                'ok' => true,
                'message' => 'Usuario creado correctamente',
                'user' => new UserResource($user)
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al crear el usuario',
                'error' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al crear el usuario debido a un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *   path="/users/{id}",
     *   summary="Mostrar detalles de un usuario",
     *   tags={"Users"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID del usuario",
     *     required=true,
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Detalle del usuario",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="ok", type="boolean", example=true),
     *       @OA\Property(property="user", ref="#/components/schemas/UserResource")
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Usuario no encontrado",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Error al obtener el usuario",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   )
     * )
     */
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'ok' => true,
                'user' => new UserResource($user)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al obtener el usuario debido a un error inesperado',
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
     *  path="/users/{id}",
     *  summary="Actualizar un usuario existente",
     *  tags={"Users"},
     *  security={{"bearerAuth":{}}},
     *  @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="ID del usuario a actualizar",
     *   required=true,
     *   @OA\Schema(type="integer", example=1)
     *  ),
     *  @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(ref="#/components/schemas/UserRequest")
     *  ),
     *  @OA\Response(
     *   response=200,
     *   description="Usuario actualizado correctamente",
     *   @OA\JsonContent(
     *    type="object",
     *    @OA\Property(property="ok", type="boolean", example=true),
     *    @OA\Property(property="message", type="string", example="Usuario actualizado correctamente"),
     *    @OA\Property(property="user", ref="#/components/schemas/UserResource")
     *   )
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="Usuario no encontrado",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *    response=422,
     *    description="Error de validación",
     *    @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error al actualizar el usuario",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
     */
    public function update(UserRequest $request, string $id)
    {
        try {
            $request->validated();
            $user = User::findOrFail($id);
            $data = $request->all();

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            $user->update($data);

            return response()->json([
                'ok' => true,
                'message' => 'Usuario actualizado correctamente',
                'user' => new UserResource($user)
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
                'message' => 'Usuario no encontrado'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al actualizar el usuario debido a un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *   path="/users/{id}",
     *   summary="Eliminar un usuario",
     *   tags={"Users"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID del usuario a eliminar",
     *     required=true,
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Usuario eliminado correctamente",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="ok", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="Usuario eliminado correctamente")
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Usuario no encontrado",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Error al eliminar el usuario",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *   )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            if ($user) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Usuario eliminado correctamente'
                ], 200);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'Error al eliminar el usuario'
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al eliminar el usuario debido a un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
