<?php

namespace App\Swagger\Schemas\Requests;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LoginRequest',
    required: ['email', 'password'],
    properties: [
        new OA\Property(
            property: 'email',
            type: 'string',
            format: 'email',
            maxLength: 255,
            example: 'ivan.ivanov@example.com'
        ),
        new OA\Property(
            property: 'password',
            type: 'string',
            format: 'password',
            minLength: 6,
            example: 'secret123'
        ),
    ],
    type: 'object'
)]
class LoginRequest {}
