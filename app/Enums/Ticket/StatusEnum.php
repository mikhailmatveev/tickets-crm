<?php

namespace App\Enums\Ticket;

use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StatusEnum',
    type: 'string',
    enum: ['done', 'new', 'working']
)]
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
