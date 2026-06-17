<?php

namespace App\Http\Controllers\API;

use App\DTO\TicketCreateData;
use App\DTO\TicketFilterData;
use App\Http\Controllers\Controller;
use App\Http\Requests\TicketFilterRequest;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Requests\TicketUpdateRequest;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketCreateResource;
use App\Http\Resources\TicketResourceCollection;
use App\Http\Resources\TicketUpdateResource;
use App\Models\Ticket;
use App\Services\TicketService;

class TicketController extends Controller
{
    public function __construct(
        protected TicketService $ticketService
    ) {}

    public function index(TicketFilterRequest $request): TicketResourceCollection
    {
        $tickets = $this->ticketService->getFilteredTickets(
            TicketFilterData::from($request)
        );
        return new TicketResourceCollection($tickets);
    }

    public function show(int $id): TicketResource
    {
        return new TicketResource(
            Ticket::with(['customer', 'replies', 'media'])
                ->findOrFail($id)
        );
    }

    public function create(TicketStoreRequest $request)
    {
        // Передача в сервис данных, полученных из DTO
        $ticket = $this->ticketService->create(
            TicketCreateData::from($request)
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

    public function update(TicketUpdateRequest $request, int $id): TicketUpdateResource
    {
        $ticket = $this->ticketService->update(
            $id,
            $request->validated()
        );

        return new TicketUpdateResource($ticket);
    }
}
