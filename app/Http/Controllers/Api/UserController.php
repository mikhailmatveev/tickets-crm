<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Get(
        path: '/api/users',
        description: 'Возвращает список пользователей вместе ролями',
        tags: ['api'],
        responses: [
            new OA\Response(response: 200, description: 'Список пользователей'),
            new OA\Response(response: 401, description: 'Неавторизован')
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(User::all());
    }

    public function show(Request $request): UserResource
    {
        return new UserResource($request->id);
    }

    #[OA\Delete(
        path: '/api/user/{id}',
        description: 'Удаляет пользователя по id',
        summary: 'Удалить пользователя',
        tags: ['api'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID пользователя',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Пользователь удалён'),
            new OA\Response(response: 401, description: 'Неавторизован'),
            new OA\Response(response: 403, description: 'Доступ запрещён'),
            new OA\Response(response: 404, description: 'Пользователь не найден'),
            new OA\Response(response: 422, description: 'Ошибка валидации')
        ]
    )]
    public function destroy(UserDeleteRequest $request): UserResource
    {
        $user = User::findOrFail($request->id);
        $user->delete();

        return new UserResource($user);
    }
}
