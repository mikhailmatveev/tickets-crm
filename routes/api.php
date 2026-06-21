<?php

use App\Http\Controllers\API\RolesController;
use App\Http\Controllers\API\StatisticController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserUpdatePasswordController;
use App\Http\Controllers\API\UserUpdateRoleController;
use App\Http\Middleware\TicketRateLimit;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/user', [UserController::class, 'create']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);
    Route::put('/user/{id}/role', [UserUpdateRoleController::class, 'update']);
    Route::put('/user/{id}/password', [UserUpdatePasswordController::class, 'update']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/roles', [RolesController::class, 'index']);
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/ticket/{id}', [TicketController::class, 'show']);
    Route::put('/ticket/{id}', [TicketController::class, 'update']);
    Route::get('/tickets/statistics', [StatisticController::class, 'index']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Проверка аутентификации пользователя
    Route::get('/user', fn (Request $request) => new UserResource($request->user()));
    // Повторная отправка письма
    Route::post('/email/resend', function (Request $request, UserService $userService) {
        // Отправляем письмо со ссылкой на верификацию
        $userService->sendEmailVerificationNotification($request->user());
        return response()->json(['message' => 'Письмо отправлено']);
    })
        ->middleware('throttle:6,1')
        ->name('verification.resend');
    ;
});

// Верификация по ссылке из письма
Route::get('/email/verify/{id}/{hash}', function (Request $request, int $id, string $hash) {
    $user = User::findOrFail($id);
    // Проверяем hash
    if (!hash_equals($hash, sha1($user->email))) {
        return redirect('/')->with('error', 'Неверная ссылка верификации');
    }
    // Уже верифицирован
    if ($user->hasVerifiedEmail()) {
        return redirect('/')->with('info', 'Email уже подтверждён');
    }
    $user->markEmailAsVerified();
    // Если верифицирован - отправляем на главную страницу
    return redirect('/');
})
    ->middleware('signed')
    ->name('verification.verify')
;

// API-метод для виджета
Route::post('/ticket/create', [TicketController::class, 'create'])
    ->middleware(TicketRateLimit::class);
