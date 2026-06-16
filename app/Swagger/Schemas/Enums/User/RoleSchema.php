<?php

namespace App\Swagger\Schemas\Enums\User;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Enum.RoleSchema',
    type: 'string',
    enum: ['admin', 'manager']
)]
class RoleSchema {}
