<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Http\Resources\UserResource;
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
     *  description="Autentica un usuario en el sistema utilizando email y contraseña.",
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
            $errorMsg = $e->getMessage();
            if (strpos($errorMsg, "Personal access client not found") !== false) {
                $errorMsg .= " Ejecuta: php artisan passport:client --personal";
            }
            return response()->json([
                'ok' => false,
                'message' => 'Error al iniciar sesión por error inesperado',
                'error' => $errorMsg
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *  path="/auth/logout",
     *  summary="Cerrar sesión",
     *  description="Cierra la sesión del usuario autenticado invalidando su token. Accesible para usuarios y administradores autenticados.",
     *  tags={"Auth"},
     *  security={{"bearerAuth":{}}},
     *  @OA\Response(
     *    response=200,
     *    description="Logout exitoso",
     *    @OA\JsonContent(ref="#/components/schemas/LogoutResponse")
     *  ),
     *  @OA\Response(
     *    response=401,
     *    description="No autorizado - Token inválido o faltante",
     *    @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
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

    /**
     * @OA\Post(
     *  path="/auth/register",
     *  summary="Registrar un nuevo usuario del sistema",
     *  description="Registra un nuevo usuario del sistema (empleado/administrador) para acceso al CMS. Los socios se gestionan por separado.",
     *  tags={"Auth"},
     *  @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(ref="#/components/schemas/UserRequest")
     *  ),
     *  @OA\Response(
     *   response=201,
     *   description="Usuario registrado correctamente",
     *   @OA\JsonContent(
     *    type="object",
     *    @OA\Property(property="ok", type="boolean", example=true),
     *    @OA\Property(property="message", type="string", example="Usuario registrado correctamente"),
     *    @OA\Property(property="user", ref="#/components/schemas/UserResource")
     *   )
     *  ),
     *  @OA\Response(
     *    response=422,
     *    description="Error de validación",
     *    @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *  ),
     *  @OA\Response(
     *   response=500,
     *   description="Error al registrar usuario",
     *   @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *  )
     * )
     */
    public function register(UserRequest $request) {
    try {
        $user_info = $request->validated();
        $user_info['password'] = bcrypt($user_info['password']);
        $user_info['role'] = $user_info['role'] ?? 'user';

        if (User::where('email', $user_info['email'])->exists()) {
            return response()->json([
                'ok' => false,
                'message' => 'El correo electrónico ya está registrado'
            ], 422);
        }

        $user = User::create($user_info);
        
        return response()->json([
            'ok' => true,
            'message' => 'Usuario registrado correctamente',
            'user' => new UserResource($user)
        ], 201);
    } catch (ValidationException $e) {
        return response()->json([
            'ok' => false,
            'message' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);
    } catch (Exception $e) {
        return response()->json([
            'ok' => false,
            'message' => 'Error al registrar usuario por error inesperado',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
