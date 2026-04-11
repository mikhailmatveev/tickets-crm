<?php

namespace App\Enums\Ticket;

use Illuminate\Support\Collection;

enum Status: string
{
    case DONE = 'done';
    case NEW = 'new';
    case WORKING = 'working';

    public static function collection(): Collection
    {
        return collect(self::cases())->map(fn (Status $status) => $status->value);
    }
}
