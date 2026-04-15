<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Requests\TicketUpdateRequest;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketCreateResource;
use App\Http\Resources\TicketUpdateResource;
use App\Models\Ticket;
use App\Services\TicketService;
use OpenApi\Attributes as OA;
use RateLimiter;

class TicketController extends Controller
{
    public function __construct(
        protected TicketService $ticketService
    ) {}

    #[OA\Get(
        path: '/api/tickets',
        description: 'Возвращает список тикетов в связке с клиентом',
        tags: ['api'],
        responses: [
            new OA\Response(response: 200, description: 'Список тикетов'),
            new OA\Response(response: 401, description: 'Неавторизован')
        ]
    )]
    public function index(): TicketResource
    {
        return new TicketResource(
            Ticket::with('customer')
                ->get()
        );
    }

    #[OA\Get(
        path: '/api/ticket/{id}',
        description: 'Возвращает подробные данные о тикете в связке с клиентами и ответами на этот тикет',
        tags: ['api'],
        responses: [
            new OA\Response(response: 200, description: 'Данные по тикету'),
            new OA\Response(response: 401, description: 'Неавторизован')
        ]
    )]
    public function show(int $id): TicketResource
    {
        return new TicketResource(
            Ticket::with('customer', 'replies')
                ->findOrFail($id)
        );
    }

    #[OA\Post(
        path: '/api/ticket/create',
        description: 'Создание нового тикета клиентом',
        summary: 'Создать тикет',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'phone', 'subject'],
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
                        example: 'Описание проблемы',
                        nullable: true
                    ),
                ]
            )
        ),

        tags: ['api'],

        responses: [
            new OA\Response(
                response: 201,
                description: 'Тикет успешно создан',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'customer_id', type: 'integer', example: 1),
                        new OA\Property(property: 'subject', type: 'string', example: 'Проблема с заказом'),
                        new OA\Property(property: 'text', type: 'string', example: 'Описание проблемы'),
                        new OA\Property(property: 'status', type: 'string', example: 'new'),
                        new OA\Property(property: 'manager_replied_at', type: 'string', format: 'date-time', nullable: true),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),

                        new OA\Property(
                            property: 'customer',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Иван Иванов'),
                                new OA\Property(property: 'email', type: 'string', example: 'test@example.com'),
                                new OA\Property(property: 'phone', type: 'string', example: '+79991234567'),
                            ],
                            type: 'object'
                        ),

                        new OA\Property(
                            property: 'replies',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 16),
                                    new OA\Property(property: 'ticket_id', type: 'integer', example: 7),
                                    new OA\Property(property: 'user_id', type: 'integer', example: 1),
                                    new OA\Property(property: 'text', type: 'string', example: 'Починили')
                                ],
                                type: 'object'
                            )
                        ),
                    ]
                )
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
                                    property: 'name',
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                    example: [
                                        'Имя обязательно',
                                        'Имя не должно превышать 255 символов'
                                    ]
                                ),
                                new OA\Property(
                                    property: 'email',
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                    example: [
                                        'E-mail обязателен',
                                        'Некорректный формат E-mail',
                                        'E-mail не должен превышать 255 символов'
                                    ]
                                ),
                                new OA\Property(
                                    property: 'phone',
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                    example: [
                                        'Телефон обязателен',
                                        'Телефон не должен превышать 20 символов'
                                    ]
                                ),
                                new OA\Property(
                                    property: 'subject',
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                    example: [
                                        'Тема обращения обязательна',
                                        'Тема не должна превышать 255 символов'
                                    ]
                                ),
                                new OA\Property(
                                    property: 'text',
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                    example: [
                                        'Описание не должно превышать 2000 символов'
                                    ]
                                ),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),

            new OA\Response(
                response: 429,
                description: 'Слишком много запросов',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Вы уже создавали заявку. Попробуйте через 24 часа'
                        )
                    ]
                )
            ),

            new OA\Response(
                response: 500,
                description: 'Ошибка сервера'
            ),
        ]
    )]
    public function create(TicketStoreRequest $request)
    {
        $validated = $request->validated();

        // Ключ для RateLimiter
        $keyEmail = 'ticket:create:' . md5(
            $validated['email']
        );
        // Ключ для RateLimiter
        $keyPhone = 'ticket:create:' . md5(
            $validated['phone']
        );

        // Проверяем лимит
        if (
            RateLimiter::tooManyAttempts($keyEmail, 1) ||
            RateLimiter::tooManyAttempts($keyPhone, 1)
        ) {
            return response()->json([
                'message' => 'Вы уже создавали заявку. Попробуйте через 24 часа.'
            ], 429);
        }

        // TicketService
        $ticket = $this->ticketService->create($validated);

        // Установка лимита на 24 часа
        $decay = 60 * 60 * 24;
        // Фиксируем попытку только после успеха (ограничение на сутки)
        RateLimiter::hit($keyEmail, $decay);
        RateLimiter::hit($keyPhone, $decay);

        return new TicketCreateResource(
            $ticket->load([
                'customer',
                'replies'
            ])
        )
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Put(
        path: '/api/ticket/{id}',
        description: 'Обновляет статус тикета и создаёт ответ (reply). Поле reply_text обязательно только при статусе done и запрещено для остальных статусов',
        summary: 'Обновить тикет и добавить ответ',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['status'],
                properties: [
                    new OA\Property(
                        property: 'status',
                        description: 'Статус тикета',
                        type: 'string',
                        enum: ['new', 'working', 'done'],
                        example: 'working'
                    ),
                    new OA\Property(
                        property: 'reply_text',
                        description: 'Текст ответа. Обязателен только при status = done. Запрещён для других статусов',
                        type: 'string',
                        maxLength: 2000,
                        example: 'Ответ менеджера клиенту',
                        nullable: true
                    ),
                ]
            )
        ),

        tags: ['api'],

        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID тикета',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],

        responses: [
            new OA\Response(
                response: 200,
                description: 'Тикет успешно обновлён',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'customer_id', type: 'integer', example: 1),
                        new OA\Property(property: 'subject', type: 'string', example: 'Проблема с заказом'),
                        new OA\Property(property: 'text', type: 'string', example: 'Описание проблемы'),
                        new OA\Property(property: 'status', type: 'string', example: 'working'),
                        new OA\Property(property: 'manager_replied_at', type: 'string', format: 'date-time', nullable: true),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),

                        new OA\Property(
                            property: 'customer',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Иван Иванов'),
                                new OA\Property(property: 'email', type: 'string', example: 'test@example.com'),
                                new OA\Property(property: 'phone', type: 'string', example: '+79991234567'),
                            ],
                            type: 'object'
                        ),

                        new OA\Property(
                            property: 'replies',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'text', type: 'string', example: 'Ответ менеджера'),
                                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                ]
                            )
                        ),
                    ]
                )
            ),

            new OA\Response(
                response: 404,
                description: 'Тикет не найден'
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
                                    property: 'status',
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                    example: [
                                        'Поле status является обязательным',
                                        'Поле status должно быть строкой',
                                        'Поле status может принимать только одно из значений new, working или done'
                                    ]
                                ),
                                new OA\Property(
                                    property: 'reply_text',
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                    example: [
                                        'Поле ответа обязательно при завершении тикета',
                                        'Поле reply_text должно быть строкой',
                                        'Поле reply_text не должно превышать 2000 символов'
                                    ]
                                ),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),

            new OA\Response(
                response: 500,
                description: 'Ошибка сервера'
            ),
        ]
    )]
    public function update(TicketUpdateRequest $request, int $id): TicketUpdateResource
    {
        $ticket = $this->ticketService->update(
            $id,
            $request->validated()
        );

        return new TicketUpdateResource($ticket);
    }
}
