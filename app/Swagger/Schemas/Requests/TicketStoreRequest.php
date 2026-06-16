<?php

namespace App\Swagger\Schemas\Requests;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TicketStoreRequest',
    required: ['name', 'email', 'phone', 'subject', 'text'],
    properties: [
        new OA\Property(
            property: 'name',
            type: 'string',
            maxLength: 255,
            example: 'Иван Иванов'
        ),
        new OA\Property(
            property: 'email',
            type: 'string',
            format: 'email',
            maxLength: 255,
            example: 'test@example.com'
        ),
        new OA\Property(
            property: 'phone',
            type: 'string',
            maxLength: 20,
            example: '+79991234567'
        ),
        new OA\Property(
            property: 'subject',
            type: 'string',
            maxLength: 255,
            example: 'Проблема с заказом'
        ),
        new OA\Property(
            property: 'text',
            type: 'string',
            maxLength: 2000,
            example: 'Добрый день! Не прошёл заказ. Что делать?'
        )
    ],
    type: 'object'
)]
class TicketStoreRequest {}
