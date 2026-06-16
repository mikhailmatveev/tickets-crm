<?php

namespace App\Swagger\Schemas;

use Carbon\Carbon;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CustomerSchema',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Иван'),
        new OA\Property(property: 'phone', type: 'string', maxLength: 16, example: '+799912345678'),
        new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'ivan.ivanov@example.com'),
        new OA\Property(property: 'created_at', type: Carbon::class, format: 'date-time', example: '2026-04-17T10:00:00Z', nullable: true),
        new OA\Property(property: 'updated_at', type: Carbon::class, format: 'date-time', example: '2026-04-17T10:00:00Z', nullable: true),
        new OA\Property(property: 'tickets', items: new OA\Items(ref: '#/components/schemas/TicketSchema'))
    ],
    type: 'object'
)]
class CustomerSchema {}
