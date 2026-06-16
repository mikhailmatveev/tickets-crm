<?php

namespace App\Swagger\Schemas\Enums\Ticket;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Enum.PeriodSchema',
    type: 'string',
    enum: ['day', 'month', 'week']
)]
class PeriodSchema {}
