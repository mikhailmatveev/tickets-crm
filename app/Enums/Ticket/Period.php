<?php

namespace App\Enums\Ticket;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Period',
    type: 'string',
    enum: ['day', 'month', 'week']
)]
enum Period: string
{
    case DAY = 'day';
    case MONTH = 'month';
    case WEEK = 'week';
}
