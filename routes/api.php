<?php

use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\StatisticController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserUpdateRoleController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => new UserResource($request->user()));
    Route::delete('/user/{id}', [UserController::class, 'destroy']);
    Route::put('/user/{id}/role', [UserUpdateRoleController::class, 'update']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/roles', [RolesController::class, 'index']);
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/ticket/{id}', [TicketController::class, 'show']);
    Route::put('/ticket/{id}', [TicketController::class, 'update']);
    Route::get('/tickets/statistics', [StatisticController::class, 'index']);
});

// API-метод для виджета
Route::post('/ticket/create', [TicketController::class, 'create']);
