<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Function to login
     */
    public function login(Request $request) {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

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
     * Function to logout
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
