<?php

namespace App\Swagger\Schemas\Requests;

use Carbon\Carbon;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TicketFilterRequest',
    properties: [
        new OA\Property(
            property: 'email',
            description: 'Поиск по email клиента (минимум 3 символа)',
            type: 'string',
            maxLength: 255,
            minLength: 3,
            example: 'ivan'
        ),
        new OA\Property(
            property: 'phone',
            description: 'Поиск по телефону клиента (минимум 3 символа)',
            type: 'string',
            maxLength: 20,
            minLength: 3,
            example: '123'
        ),
        new OA\Property(
            property: 'date',
            description: 'Фильтр по дате ответа менеджера',
            type: Carbon::class,
            format: 'date',
            example: '2026-04-20'
        ),
        new OA\Property(
            property: 'status',
            ref: '#/components/schemas/StatusEnum', description: 'Статус тикета',
            type: 'string',
            example: 'working'
        )
    ]
)]
class TicketFilterRequest {}
