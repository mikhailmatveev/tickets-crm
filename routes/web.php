<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->name('login');
Route::post('/auth/logout', [AuthController::class, 'logout']);

Route::get('/{any}', function () {
    return view('index');
})->where('any', '^(?!api).*$');
