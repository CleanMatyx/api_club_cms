<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourtController;


Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:api');

Route::apiResource('/courts', CourtController::class)->middleware('auth:api');
Route::post('/courts/search', [CourtController::class, 'search'])->middleware('auth:api');


