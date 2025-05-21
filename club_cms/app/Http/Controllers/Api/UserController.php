<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(15);

        try {
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
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $request->validated();
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        try {
            if($user) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Usuario creado correctamente',
                    'user' => new UserResource($user)
                ], 201);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'Error al crear el usuario'
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al crear el usuario debido a un error inesperado',
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
     * Update the specified resource in storage.
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
                unset($data['password']); // No actualizar si no viene
            }
            $user->update($data);

            return response()->json([
                'ok' => true,
                'message' => 'Usuario actualizado correctamente',
                'user' => new UserResource($user)
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al actualizar el usuario debido a un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        try {
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
