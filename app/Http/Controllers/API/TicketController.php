<?php

namespace App\Http\Controllers\API;

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

class TicketController extends Controller
{
    public function __construct(
        protected TicketService $ticketService
    ) {}

    public function index(TicketFilterRequest $request): AnonymousResourceCollection
    {
        return TicketResource::collection(
            Ticket::query()
                ->with('customer')
                ->when($request->filled('email'), fn($q) => $q->byEmail($request->email))
                ->when($request->filled('phone'), fn($q) => $q->byPhone($request->phone))
                ->when($request->filled('date'), fn($q) => $q->byDate($request->date))
                ->when($request->status(), fn($q, $status) => $q->byStatus($status))
                ->get()
        );
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

    public function update(TicketUpdateRequest $request, int $id): TicketUpdateResource
    {
        $ticket = $this->ticketService->update(
            $id,
            $request->validated()
        );

        return new TicketUpdateResource($ticket);
    }
}
