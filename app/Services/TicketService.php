<?php

namespace App\Services;

use App\DTO\CreateTicketData;
use App\Enums\Ticket\StatusEnum;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Throwable;

class TicketService
{
    /**
     * Сервисный метод создания новой заявки с использованием транзакции для TicketController::create()
     * @param CreateTicketData $data DTO из реквеста TicketController
     * @return Ticket Новый тикет
     * @throws Throwable
     */
    public function create(CreateTicketData $data): Ticket
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::firstOrCreate(
                [
                    'email' => $data->email,
                    'phone' => $data->phone
                ],
                [
                    'name' => $data->name
                ]
            );

            $ticket = Ticket::create([
                'customer_id' => $customer->id,
                'subject' => $data->subject,
                'text' => $data->text,
                'status' => 'new'
            ]);

            // Прикрепляем файлы к тикету, если они есть
            foreach ($data->attachments as $file) {
                $ticket
                    ->addMedia($file)
                    ->toMediaCollection('attachments')
                ;
            }

            return $ticket;
        });
    }

    /**
     * Сервисный метод обновления статуса и ответа менеджера с использованием транзакции для TicketController::update()
     * @param int $id ID тикета
     * @param array $data Данные тикета (статус и ответ менеджера)
     * @return Ticket Модель тикета
     * @throws Throwable
     */
    public function update(int $id, array $data): Ticket
    {
        return DB::transaction(function () use ($id, $data) {

            $ticket = Ticket::findOrFail($id);

            $status = StatusEnum::from($data['status']);
            $ticket->changeStatus($status);
            $ticket->save();

            if ($status === StatusEnum::DONE) {
                $ticket->addReply($data['reply_text'], auth()->id());
            }

            return $ticket->load(['customer', 'replies', 'media']);
        });
    }
}
