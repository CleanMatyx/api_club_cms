<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Members;
use App\Models\Sport;
use App\Http\Resources\SportResource;
use App\Http\Requests\SportRequest;



class SportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sports = Sport::paginate(15);

        try {
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
     * Store a newly created resource in storage.
     */
    public function store(SportRequest $request)
    {
        $request->validated();

        $sport = Sport::create($request->all());

        try {
            if($sport) {
                return response()->json([
                    'ok' => true,
                    'sport' => new SportResource($sport)
                ], 201);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'Error al crear el deporte'
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al crear el deporte debido a un error inesperado',
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
     * Update the specified resource in storage.
     */
    public function update(SportRequest $request, string $id)
    {
        $request->validated();
        $sport = Sport::findOrFail($id);
        $sport->update($request->all());

        try {
            if ($sport) {
                return response()->json([
                    'ok' => true,
                    'sport' => new SportResource($sport)
                ], 200);
            } else {
                return response()->json([
                    'ok' => false,
                    'message' => 'Error al actualizar el deporte'
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al actualizar el deporte debido a un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sport = Sport::findOrFail($id);
        $sport->delete();

        try {
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
