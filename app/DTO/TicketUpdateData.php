<?php

namespace App\DTO;

use App\Enums\Ticket\StatusEnum;
use Spatie\LaravelData\Data;

class TicketUpdateData extends Data
{
    public function __construct(
        public readonly StatusEnum $status,
        public readonly string $replyText
    ) {}
}
