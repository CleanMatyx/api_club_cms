<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Http\Resources\MemberResource;
use App\Http\Requests\MemberRequest;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
