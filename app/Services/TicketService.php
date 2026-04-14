<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Throwable;

class TicketService
{
    /**
     * Сервисный метод создания новой заявки с использованием транзакции для TicketController::create()
     * @param array $data Validated данные из реквеста TicketController
     * @return Ticket Новый тикет
     * @throws Throwable
     */
    public function create(array $data): Ticket
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::firstOrCreate(
                [
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                ],
                [
                    'name' => $data['name'],
                ]
            );

            return Ticket::create([
                'customer_id' => $customer->id,
                'subject' => $data['subject'],
                'text' => $data['text'] ?? null,
                'status' => 'new',
            ]);
        });
    }
}
