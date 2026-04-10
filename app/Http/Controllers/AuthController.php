<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/login',
        description: 'Логин с использованием email и пароля',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string'),
                    new OA\Property(property: 'password', type: 'string')
                ]
            ),
        ),
        tags: ['web'],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'Неверные данные')
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Неверные данные'], 401);
        }
        $request->session()->regenerate();
        return response()->json(['message' => 'OK']);
    }

    #[OA\Post(
        path: '/logout',
        description: 'Завершение сеанса пользователя',
        tags: ['web'],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'Неавторизован')
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        return response()->json(['message' => 'Сеанс завершён']);
    }

    #[OA\Get(
        path: '/api/user',
        description: 'Возвращает данные авторизованного пользователя',
        tags: ['api'],
        responses: [
            new OA\Response(response: 200, description: 'Данные пользователя'),
            new OA\Response(response: 401, description: 'Неавторизован')
        ]
    )]
    public function user(Request $request)
    {
        return $request->user();
    }
}
