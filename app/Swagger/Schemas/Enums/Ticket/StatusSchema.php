<?php

namespace App\Swagger\Schemas\Enums\Ticket;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Enum.StatusSchema',
    type: 'string',
    enum: ['done', 'new', 'working']
)]
class StatusSchema {}
