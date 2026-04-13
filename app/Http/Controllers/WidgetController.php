<?php

namespace App\Http\Controllers;

use App\Http\Requests\WidgetRequest;
use App\Models\Ticket;

class WidgetController extends Controller
{
    public function index(WidgetRequest $request)
    {
        $ticketId = $request->validated('ticket_id');
        $ticket = null;
        if ($ticketId) {
            $ticket = Ticket::with('customer', 'replies')->findOrFail($ticketId);
        }
        return view('widget', ['ticket' => $ticket]);
    }
}
