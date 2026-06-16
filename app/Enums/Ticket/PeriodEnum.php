<?php

namespace App\Enums\Ticket;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PeriodEnum',
    type: 'string',
    enum: ['day', 'month', 'week']
)]
enum PeriodEnum: string
{
    case DAY = 'day';
    case MONTH = 'month';
    case WEEK = 'week';
}
