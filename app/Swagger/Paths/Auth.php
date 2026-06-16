<?php

namespace App\Swagger\Paths;

use OpenApi\Attributes as OA;

class Auth
{
    #[OA\Post(
        path: '/login',
        description: 'Логин с использованием email и пароля',
        requestBody: new OA\RequestBody(
            description: 'Данные авторизации (email и пароль)',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest')
        ),
        tags: ['web'],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'Неверные данные'),
            new OA\Response(response: 422, description: 'Ошибка валидации')
        ]
    )]
    public function postLogin(): void {}

    #[OA\Post(
        path: '/logout',
        description: 'Завершение сеанса пользователя',
        tags: ['web'],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'Неавторизован')
        ]
    )]
    public function postLogout(): void {}

    #[OA\Get(
        path: '/api/user',
        description: 'Возвращает данные авторизованного пользователя',
        tags: ['api'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Данные пользователя',
                content: new OA\JsonContent(ref: '#/components/schemas/User')
            ),
            new OA\Response(response: 401, description: 'Неавторизован')
        ]
    )]
    public function getUser(): void {}
}
