<?php

namespace App\Enums\Ticket;

enum Period: string
{
    case DAY = 'day';
    case MONTH = 'month';
    case WEEK = 'week';
}
