<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\Member;
use App\Http\Resources\UserResource;
use App\Http\Resources\MemberResource;
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

    /**
     * @OA\Post(
     *  path="/auth/register",
     *  summary="Crear un nuevo usuario",
     *  tags={"Auth"},
     *  security={{"bearerAuth":{}}},
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
     *    @OA\Property(property="court", ref="#/components/schemas/CourtResource")
     *   )
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
    public function register(UserRequest $request) {
        try {
            $user = User::create($request->validated());
            $member = Member::create([
                'user_id' => $user->id,
                'membership_date' => now()->toDateString(),
                'status' => 'active'
            ]);
            return response()->json([
                'ok' =>true,
                'message' => 'Usuario registrado correctamente',
                'user' => new UserResource($user),
                'member' => new MemberResource($member)
            ]);
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
