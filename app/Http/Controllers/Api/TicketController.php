<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function index(): TicketResource
    {
        return new TicketResource(
            Ticket::with('customer')
                ->get()
        );
    }

    public function show(int $id): TicketResource
    {
        return new TicketResource(
            Ticket::with('customer', 'replies')
                ->findOrFail($id)
        );
    }
}
