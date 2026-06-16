<?php

namespace Tests\Unit;

use App\Enums\Ticket\StatusEnum;
use App\Enums\User\Role;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class SeederTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Проверяет, что существует хотя бы один админ
     * @return void
     */
    public function test_that_at_least_one_admin_exists(): void
    {
        $this->assertNotEquals(
            0,
            User::query()
                ->role(Role::ADMIN)
                ->count()
        );
    }

    /**
     * Проверка, что у всех клиентов есть хотя бы один тикет
     * Или по-другому: нет таких клиентов, у которых нет тикетов
     * @return void
     */
    public function test_that_all_customers_has_tickets(): void
    {
        $this->assertEquals(
            0,
            Customer::query()
                ->doesntHave('tickets')
                ->count()
        );
    }

    /**
     * Проверка, что у DONE-тикетов есть хотя бы один ответ
     * Или по другому: нет таких тикетов со статусом DONE, которые не имели бы ответов
     * @return void
     */
    public function test_that_all_done_tickets_has_at_least_one_reply(): void
    {
        $this->assertEquals(
            0,
            Ticket::query()
                ->where(
                    'status',
                    StatusEnum::DONE
                )
                ->doesntHave('replies')
                ->count()
        );
    }

    /**
     * Проверка, что ответы привязаны именно к менеджерам
     * Или по другому: нет таких ответов, на которые отвечал админ
     * @return void
     */
    public function test_that_all_ticket_replies_linked_to_manager_users(): void
    {
        $this->assertEquals(
            0,
            TicketReply::query()
                ->whereHas(
                    'user',
                    fn ($query) => $query->role(Role::ADMIN)
                )
                ->count()
        );
    }

    /**
     * Проверить, что у менеджеров вообще есть ответы
     * @return void
     */
    public function test_that_ticket_replies_has_at_least_one_reply(): void
    {
        $this->assertNotEquals(
            0,
            TicketReply::query()
                ->whereHas(
                    'user',
                    fn ($query) => $query->role(Role::MANAGER)
                )
                ->count()
        );
    }
}
