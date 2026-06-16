<?php

namespace App\Swagger\Schemas\Requests;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TicketUpdateRequest',
    required: ['status'],
    properties: [
        new OA\Property(
            property: 'status',
            description: 'Статус тикета',
            type: 'string',
            enum: ['new', 'working', 'done'],
            example: 'done'
        ),
        new OA\Property(
            property: 'reply_text',
            description: "Текст ответа. Обязателен только при status === 'done'. Запрещён для других статусов",
            type: 'string',
            maxLength: 2000,
            example: 'Добрый день! Вы оставляли заявку на сайте о том, что у вас не прошёл заказ. Попробуйте сделать заказ повторно.',
            nullable: true
        ),
    ]
)]
class TicketUpdateRequest {}
