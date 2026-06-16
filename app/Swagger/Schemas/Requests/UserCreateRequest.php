<?php

namespace App\Swagger\Schemas\Requests;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserCreateRequest',
    required: ['name', 'email', 'password', 'role'],
    properties: [
        new OA\Property(
            property: 'name',
            description: 'Имя пользователя',
            type: 'string',
            maxLength: 255,
            example: 'Иван Иванов'
        ),
        new OA\Property(
            property: 'email',
            description: 'Email пользователя (уникальный)',
            type: 'string',
            format: 'email',
            maxLength: 255,
            example: 'ivan@example.com'
        ),
        new OA\Property(
            property: 'password',
            description: 'Пароль пользователя',
            type: 'string',
            minLength: 6,
            example: 'secret123'
        ),
        new OA\Property(
            property: 'role',
            ref: '#/components/schemas/RoleEnum',
            description: 'Роль пользователя'
        )
    ]
)]
class UserCreateRequest {}
