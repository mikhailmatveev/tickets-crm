<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RolesResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;
use Spatie\Permission\Models\Role;

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
    public function index(): AnonymousResourceCollection
    {
        return RolesResource::collection(Role::all());
    }
}
