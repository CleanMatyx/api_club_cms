<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourtController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\SportController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\UserController;


Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:api');

Route::apiResource('/courts', CourtController::class)->middleware('auth:api');
Route::post('/courts/search', [CourtController::class, 'search'])->middleware('auth:api');

Route::apiResource('/members', MemberController::class)->middleware('auth:api');
Route::apiResource('/reservations', ReservationController::class)->middleware('auth:api');
Route::apiResource('/sports', SportController::class)->middleware('auth:api');
Route::apiResource('/users', UserController::class)->middleware('auth:api');



