<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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
            new OA\Response(
                response: 200,
                description: 'Список пользователей',
                content: new OA\JsonContent(
                    items: new OA\Items(ref: '#/components/schemas/User')
                )
            ),
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

    #[OA\Post(
        path: '/api/user',
        description: 'Создаёт нового пользователя и назначает ему роль',
        summary: 'Создать пользователя',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserCreateRequest')
        ),
        tags: ['api'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Пользователь успешно создан',
                content: new OA\JsonContent(ref: '#/components/schemas/User')
            ),
            new OA\Response(response: 401, description: 'Неавторизован'),
            new OA\Response(response: 403, description: 'Доступ запрещён'),
            new OA\Response(
                response: 422,
                description: 'Ошибка валидации',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Ошибка валидации'),
                        new OA\Property(
                            property: 'errors',
                            properties: [
                                new OA\Property(
                                    property: 'name',
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                    example: [
                                        'Поле "Имя" обязательно для заполнения',
                                        'Поле "Имя" должно быть строкой',
                                        'Поле "Имя" не должно превышать 255 символов'
                                    ]
                                ),
                                new OA\Property(
                                    property: 'email',
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                    example: [
                                        'Поле "E-mail" обязательно для заполнения',
                                        'Поле "E-mail" должно быть строкой',
                                        'Укажите корректный E-mail',
                                        'Поле "E-mail" не должно превышать 255 символов',
                                        'Пользователь с таким E-mail уже существует'
                                    ]
                                ),
                                new OA\Property(
                                    property: 'password',
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                    example: [
                                        'Поле "Пароль" обязательно для заполнения',
                                        'Поле "Пароль" должно быть строкой',
                                        'Пароль должен содержать не менее 6 символов'
                                    ]
                                ),
                                new OA\Property(
                                    property: 'role',
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                    example: [
                                        'Поле "Роль" обязательно для заполнения',
                                        'Поле "Роль" должно быть строкой',
                                        'Выбрана недопустимая роль'
                                    ]
                                )
                            ],
                            type: 'object'
                        )
                    ]
                )
            )
        ]
    )]
    public function create(UserCreateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'email_verified_at' => Carbon::now()
        ]);
        $user->assignRole($validated['role']);
        return new UserResource($user)
            ->response()
            ->setStatusCode(201)
        ;
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
            new OA\Response(
                response: 200,
                description: 'Пользователь удалён',
                content: new OA\JsonContent(ref: '#/components/schemas/User')
            ),
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
