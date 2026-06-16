<?php

namespace Database\Seeders;

use App\Enums\Ticket\StatusEnum;
use App\Enums\User\RoleEnum;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketReplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $managers = User::query()
            ->role(RoleEnum::MANAGER)
            ->get();

        if ($managers->isEmpty()) {
            $this->command->warn('Менеджеры не найдены, TicketReplySeeder пропущен.');
            return;
        }

        Ticket::query()
            ->where('status', StatusEnum::DONE)
            ->each(function (Ticket $ticket) use ($managers) {
                // Отвечать на тикет будет случайный менеджер
                $manager = $managers->random();
                TicketReply::factory()
                    ->forTicket($ticket)
                    ->fromUser($manager)
                    ->create();
            });
    }
}
