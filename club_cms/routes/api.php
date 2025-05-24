<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourtController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\SportController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\UserController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware(['auth:api', 'role:admin'])->group(function() {
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/members', MemberController::class);
    Route::apiResource('/courts', CourtController::class)->except(['index', 'show']);
    Route::apiResource('/sports', SportController::class)->except(['index', 'show']);
});

Route::middleware(['auth:api', 'role:admin,user'])->group(function() {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/courts', [CourtController::class, 'index']);
    Route::get('/courts/{id}', [CourtController::class, 'show']);
    Route::post('/courts/search', [CourtController::class, 'search']);
    Route::get('/sports', [SportController::class, 'index']);
    Route::get('/sports/{id}', [SportController::class, 'show']);
    Route::get('/members', [MemberController::class, 'index']);
    Route::get('/members/{id}', [MemberController::class, 'show']);
    Route::apiResource('/reservations', ReservationController::class);
});
