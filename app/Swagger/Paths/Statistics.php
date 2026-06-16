<?php

namespace App\Swagger\Paths;

use OpenApi\Attributes as OA;

class Statistics
{
    #[OA\Get(
        path: '/api/tickets/statistics',
        description: 'Возвращает статистику по тикетам по периодам (день, неделя, месяц)',
        requestBody: new OA\RequestBody(
            description: 'Период',
            content: new OA\JsonContent(
                ref: '#/components/schemas/PeriodEnum'
            )
        ),
        tags: ['api'],
        responses: [
            new OA\Response(response: 200, description: 'Данные статистики'),
            new OA\Response(response: 401, description: 'Неавторизован')
        ]
    )]
    public function getStatistics(): void {}
}
