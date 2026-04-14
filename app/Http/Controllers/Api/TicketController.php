<?php

namespace App\Http\Controllers\Api;

use App\Enums\Ticket\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketCreateResource;
use App\Models\Customer;
use App\Models\Ticket;
use App\Services\TicketService;
use DB;
use OpenApi\Attributes as OA;
use RateLimiter;
use Throwable;

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

    /**
     * @OA\Post(
     *     path="/api/ticket/create",
     *     summary="Создать тикет",
     *     description="Создание нового тикета клиентом",
     *     tags={"api"},

     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","phone","subject"},
     *
     *             @OA\Property(property="name", type="string", maxLength=255, example="Иван Иванов"),
     *             @OA\Property(property="email", type="string", format="email", maxLength=255, example="test@example.com"),
     *             @OA\Property(property="phone", type="string", maxLength=20, example="+79991234567"),
     *             @OA\Property(property="subject", type="string", maxLength=255, example="Проблема с заказом"),
     *             @OA\Property(property="text", type="string", maxLength=2000, nullable=true, example="Описание проблемы")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Тикет успешно создан",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="customer_id", type="integer", example=1),
     *             @OA\Property(property="subject", type="string", example="Проблема с заказом"),
     *             @OA\Property(property="text", type="string", example="Описание проблемы"),
     *             @OA\Property(property="status", type="string", example="new"),
     *             @OA\Property(property="manager_replied_at", type="string", format="date-time", nullable=true),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *
     *             @OA\Property(
     *                 property="customer",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                 @OA\Property(property="email", type="string", example="test@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+79991234567")
     *             ),
     *
     *             @OA\Property(
     *                 property="replies",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *       response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Данные не прошли валидацию."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"Имя обязательно", "Имя не должно превышать 255 символов"}
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"E-mail обязателен", "Некорректный формат E-mail", "E-mail не должен превышать 255 символов"}
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"Телефон обязателен", "Телефон не должен превышать 20 символов"}
     *                 ),
     *                 @OA\Property(
     *                     property="subject",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"Тема обращения обязательна", "Тема не должна превышать 255 символов"}
     *                 ),
     *                 @OA\Property(
     *                     property="text",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"Описание не должно превышать 2000 символов"}
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=429,
     *         description="Слишком много запросов",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Вы уже создавали заявку. Попробуйте через 24 часа.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Ошибка сервера"
     *     )
     * )
     */
    public function create(TicketStoreRequest $request)
    {
        $validated = $request->validated();
        // Ключ для RateLimiter
        $key = 'ticket:create:' . md5(
                $validated['email'] . '|' . $validated['phone']
            );
        // Проверяем лимит
        if (RateLimiter::tooManyAttempts($key, 1)) {
            return response()->json([
                'message' => 'Вы уже создавали заявку. Попробуйте через 24 часа.'
            ], 429);
        }

        // TicketService
        $ticket = $this->ticketService->create($validated);

        // Фиксируем попытку только после успеха (ограничение на сутки)
        RateLimiter::hit($key, 60 * 60 * 24);

        return new TicketCreateResource(
            $ticket->load([
                'customer',
                'replies'
            ])
        )
            ->response()
            ->setStatusCode(201);
    }
}
