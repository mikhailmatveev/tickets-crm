<?php

namespace App\Swagger\Schemas\Requests;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserUpdateRoleRequest',
    required: ['role_id'],
    properties: [
        new OA\Property(
            property: 'role_id',
            description: 'ID роли из таблицы roles',
            type: 'integer',
            minimum: 1,
            example: 2
        )
    ]
)]
class UserUpdateRoleRequest {}
