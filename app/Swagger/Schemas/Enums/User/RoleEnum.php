<?php

namespace App\Swagger\Schemas\Enums\User;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RoleEnum',
    type: 'string',
    enum: ['admin', 'manager']
)]
class RoleEnum {}
