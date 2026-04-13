<?php

namespace App\Http\Controllers\Api;

use App\Enums\User\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\RolesResource;
use OpenApi\Attributes as OA;

class RolesController extends Controller
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
    public function index(): RolesResource
    {
        return new RolesResource(Role::collection());
    }
}
