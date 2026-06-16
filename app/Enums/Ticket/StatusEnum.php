<?php

namespace App\Enums\Ticket;

use Illuminate\Support\Collection;

enum StatusEnum: string
{
    case DONE = 'done';
    case NEW = 'new';
    case WORKING = 'working';

    public static function collection(): Collection
    {
        return collect(self::cases())->map(fn (StatusEnum $status) => $status->value);
    }
}
