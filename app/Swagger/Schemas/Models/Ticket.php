<?php

namespace App\Swagger\Schemas\Models;

use Carbon\Carbon;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Ticket',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'customer_id', type: 'integer', example: 1),
        new OA\Property(property: 'subject', type: 'string', maxLength: 255, example: 'Проблема с заказом'),
        new OA\Property(property: 'text', type: 'string', example: 'Добрый день! Не прошёл заказ. Что делать?'),
        new OA\Property(property: 'status', ref: '#/components/schemas/StatusEnum', default: 'new'),
        new OA\Property(property: 'manager_replied_at', type: Carbon::class, format: 'date-time', example: '2026-04-17T10:00:00Z', nullable: true),
        new OA\Property(property: 'created_at', type: Carbon::class, format: 'date-time', example: '2026-04-17T10:00:00Z', nullable: true),
        new OA\Property(property: 'updated_at', type: Carbon::class, format: 'date-time', example: '2026-04-17T10:00:00Z', nullable: true),
        new OA\Property(property: 'customer', ref: '#/components/schemas/Customer'),
        new OA\Property(property: 'replies', items: new OA\Items(ref: '#/components/schemas/TicketReply')),
        new OA\Property(property: 'attachments', type: 'array', items: new OA\Items(ref: '#/components/schemas/MediaResource'))
    ],
    type: 'object'
)]
class Ticket {}
