<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use OpenApi\Attributes as OA;

class TicketController extends Controller
{
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
}
