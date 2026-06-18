<?php

namespace App\Services;

use App\DTO\TicketCreateData;
use App\DTO\TicketFilterData;
use App\DTO\TicketUpdateData;
use App\Enums\Ticket\StatusEnum;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class TicketService
{
    /**
     * Сервисный метод создания новой заявки с использованием транзакции для TicketController::create()
     * @param TicketCreateData $data DTO из реквеста TicketController
     * @return Ticket Новый тикет
     * @throws Throwable
     */
    public function create(TicketCreateData $data): Ticket
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
     * @param TicketUpdateData $data DTO с данными тикета (статус и ответ менеджера)
     * @return Ticket Модель тикета
     * @throws Throwable
     */
    public function update(int $id, TicketUpdateData $data): Ticket
    {
        return DB::transaction(function () use ($id, $data) {

            $ticket = Ticket::findOrFail($id);

            $ticket->changeStatus($data->status);
            $ticket->save();

            if ($data->status === StatusEnum::DONE) {
                $ticket->addReply($data->replyText, auth()->id());
            }

            return $ticket->load(['customer', 'replies', 'media']);
        });
    }

    /**
     * Возвращает список тикетов вместе с привязкой к клиенту по применённому фильтру
     * @param TicketFilterData $data DTO с фильтром по тикетам
     * @return Collection<int, Ticket>
     */
    public function getFilteredTickets(TicketFilterData $data): Collection
    {
        return Ticket::query()
            ->with('customer')
            ->when($data->email, fn($q, $email) => $q->byEmail($email))
            ->when($data->phone, fn($q, $phone) => $q->byPhone($phone))
            ->when($data->date, fn($q, $date) => $q->byDate($date))
            ->when($data->status, fn($q, $status) => $q->byStatus($status))
            ->get();
    }
}
