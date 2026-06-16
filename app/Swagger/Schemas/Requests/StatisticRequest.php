<?php

namespace App\Swagger\Schemas\Requests;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StatisticRequest',
    properties: [
        new OA\Property(
            property: 'period',
            ref: '#/components/schemas/PeriodEnum',
            type: 'string',
            nullable: true
        )
    ],
    type: 'object'
)]
class StatisticRequest {}
