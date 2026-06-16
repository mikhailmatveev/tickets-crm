<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Неверные данные'], 401);
        }
        $request->session()->regenerate();
        return response()->json(['message' => 'OK']);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        return response()->json(['message' => 'Сеанс завершён']);
    }

    public function user(Request $request)
    {
        return $request->user();
    }
}
