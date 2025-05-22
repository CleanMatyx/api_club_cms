<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * @OA\Tag(
 *   name="Auth",
 *   description="Operaciones sobre autenticación"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *  path="/auth/login",
     *  summary="Iniciar sesión",
     *  tags={"Auth"},
     *  @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *  ),
     *  @OA\Response(
     *    response=200,
     *    description="Login exitoso",
     *    @OA\JsonContent(ref="#/components/schemas/LoginResponse")
     *  ),
     *  @OA\Response(
     *    response=401,
     *    description="Credenciales incorrectas",
     *    @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  ),
     *  @OA\Response(
     *    response=500,
     *    description="Error inesperado",
     *    @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
     */
    public function login(AuthRequest $request) {
        try {
            $credentials = $request->validated();

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = Auth::user()->createToken(env('SECRET'))->accessToken;
                
                return response()->json([
                    'ok' => true,
                    'token' => $token
                ], 200);
            }

            return response()->json([
                'ok' => false,
                'message' => 'Correo o contraseña incorrectos'
            ], 401);
        
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al iniciar sesión por error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *  path="/auth/logout",
     *  summary="Cerrar sesión",
     *  tags={"Auth"},
     *  security={{"bearerAuth":{}}},
     *  @OA\Response(
     *    response=200,
     *    description="Logout exitoso",
     *    @OA\JsonContent(ref="#/components/schemas/LogoutResponse")
     *  ),
     *  @OA\Response(
     *    response=500,
     *    description="Error inesperado",
     *    @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
     */
    public function logout(Request $request) {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'ok' => true,
                'message' => 'Sesión cerrada correctamente'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al cerrar sesión por error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
