<?php

namespace App\Swagger\Schemas\Models;

use Carbon\Carbon;
use OpenApi\Attributes as OA;
use Spatie\Permission\Models\Role;

#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Иван'),
        new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'ivan.ivanov@example.com'),
        new OA\Property(property: 'email_verified_at', type: Carbon::class, format: 'date-time', example: '2026-04-17T10:00:00Z', nullable: true),
        new OA\Property(property: 'password', type: 'string', format: 'password'),
        new OA\Property(property: 'remember_token', type: 'string', maxLength: 100, nullable: true),
        new OA\Property(property: 'created_at', type: Carbon::class, format: 'date-time', example: '2026-04-17T10:00:00Z', nullable: true),
        new OA\Property(property: 'updated_at', type: Carbon::class, format: 'date-time', example: '2026-04-17T10:00:00Z', nullable: true),
        new OA\Property(property: 'deleted_at', type: Carbon::class, format: 'date-time', example: '2026-04-17T10:00:00Z', nullable: true),
        new OA\Property(property: 'role', type: Role::class)
    ],
    type: 'object'
)]
class User {}
