<?php

namespace App\DTO;

use App\Enums\Ticket\StatusEnum;
use Spatie\LaravelData\Data;

class TicketFilterData extends Data
{
    public function __construct(
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $date = null,
        public readonly ?StatusEnum $status = null
    ) {}
}
