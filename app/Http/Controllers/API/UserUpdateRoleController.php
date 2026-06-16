<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRoleRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use OpenApi\Attributes as OA;

class UserUpdateRoleController extends Controller
{
    #[OA\Put(
        path: '/api/user/{id}/role',
        description: 'Обновляет роль пользователя. У пользователя остаётся только одна роль',
        summary: 'Обновить роль пользователя',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserUpdateRoleRequest')
        ),

        tags: ['api'],

        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID пользователя',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'integer',
                    minimum: 1,
                    example: 1
                )
            )
        ],

        responses: [
            new OA\Response(
                response: 200,
                description: 'Роль пользователя успешно обновлена',
                content: new OA\JsonContent(ref: '#/components/schemas/User')
            ),

            new OA\Response(
                response: 422,
                description: 'Ошибка валидации',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Данные не прошли валидацию'
                        ),
                        new OA\Property(
                            property: 'errors',
                            properties: [
                                new OA\Property(
                                    property: 'role_id',
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                    example: [
                                        'Поле role_id является обязательным',
                                        'Поле role_id является целым числом',
                                        'Поле role_id должно быть больше 0',
                                        'Поля role_id с таким значением не существует'
                                    ]
                                ),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),

            new OA\Response(
                response: 404,
                description: 'Пользователь не найден'
            ),

            new OA\Response(
                response: 500,
                description: 'Ошибка сервера'
            ),
        ]
    )]
    public function update(UserUpdateRoleRequest $request, int $id): UserResource
    {
        $validated = $request->validated();
        $user = User::findOrFail($id);
        $user->roles()->sync([$validated['role_id']]);
        return new UserResource($user);
    }
}
