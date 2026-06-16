<?php

namespace App\Swagger\Paths;

use OpenApi\Attributes as OA;

class Roles
{
    #[OA\Get(
        path: '/api/roles',
        description: 'Возвращает список ролей',
        tags: ['api'],
        responses: [
            new OA\Response(response: 200, description: 'Список ролей'),
            new OA\Response(response: 401, description: 'Неавторизован')
        ]
    )]
    public function getRoles(): void {}
}
