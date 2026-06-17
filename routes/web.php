<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\WidgetController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->name('login');
Route::post('/auth/logout', [AuthController::class, 'logout']);

Route::get('/widget', [WidgetController::class, 'index'])->name('widget');

Route::get('/widget/error', function () {
    // Редирект на основную страницу виджета, если сессия с ошибками истекла
    if (!session('errors') || !session('errors')->any()) {
        return redirect()->route('widget');
    }
    // Редирект на страницу ошибки
    return view('widget', ['params' => session('errors')]);
})->name('widget.error');

Route::get('/{any}', function () {
    return view('index');
})->where('any', '^(?!api).*$');
