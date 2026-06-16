<?php

namespace App\Http\Controllers\Api;

use App\DTO\CreateTicketData;
use App\Http\Controllers\Controller;
use App\Http\Requests\TicketFilterRequest;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Requests\TicketUpdateRequest;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketCreateResource;
use App\Http\Resources\TicketUpdateResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class TicketController extends Controller
{
    public function __construct(
        protected TicketService $ticketService
    ) {}

    #[OA\Get(
        path: '/api/tickets',
        description: 'Возвращает список тикетов в связке с клиентом',
        requestBody: new OA\RequestBody(
            description: 'Фильтр',
            content: new OA\JsonContent(
                ref: '#/components/schemas/TicketFilterRequest'
            )
        ),
        tags: ['api'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список тикетов',
                content: new OA\JsonContent(
                    items: new OA\Items(ref: '#/components/schemas/Model.TicketSchema')
                )
            ),
            new OA\Response(response: 401, description: 'Неавторизован')
        ]
    )]
    public function index(TicketFilterRequest $request): AnonymousResourceCollection
    {
        return TicketResource::collection(
            Ticket::query()
                ->with('customer')
                ->when($request->filled('email'), fn($q) => $q->byEmail($request->email))
                ->when($request->filled('phone'), fn($q) => $q->byPhone($request->phone))
                ->when($request->filled('date'), fn($q) => $q->byDate($request->date))
                ->when($request->filled('status'), fn($q) => $q->byStatus($request->status))
                ->get()
        );
    }

    #[OA\Get(
        path: '/api/ticket/{id}',
        description: 'Возвращает подробные данные о тикете в связке с клиентами и ответами на этот тикет',
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
                description: 'Данные по тикету',
                content: new OA\JsonContent(ref: '#/components/schemas/Model.TicketSchema')
            ),
            new OA\Response(response: 401, description: 'Неавторизован'),
            new OA\Response(response: 422, description: 'Ошибка валидации')
        ]
    )]
    public function show(int $id): TicketResource
    {
        return new TicketResource(
            Ticket::with(['customer', 'replies', 'media'])
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
                items: new OA\Items(ref: '#/components/schemas/TicketStoreRequest')
            )
        ),

        tags: ['api'],

        responses: [
            new OA\Response(
                response: 201,
                description: 'Тикет успешно создан',
                content: new OA\JsonContent(ref: '#/components/schemas/Model.TicketSchema')
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
                                        'Телефон должен быть в формате E.164 (только + и цифры)'
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
                                        'Описание обязательно',
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
            )
        ]
    )]
    public function create(TicketStoreRequest $request)
    {
        // Передача в сервис данных, полученных из DTO
        $ticket = $this->ticketService->create(
            CreateTicketData::from($request)
        );

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
            content: new OA\JsonContent(ref: '#/components/schemas/TicketUpdateRequest')
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
                content: new OA\JsonContent(ref: '#/components/schemas/Model.TicketSchema')
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
