<?php

namespace App\Enums\Ticket;

enum PeriodEnum: string
{
    case DAY = 'day';
    case MONTH = 'month';
    case WEEK = 'week';
}
