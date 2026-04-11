<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::query()
            ->each(function (Customer $customer) {
                // Тикет со статусом NEW
                Ticket::factory()
                    ->for($customer)
                    ->asNew()
                    ->create();
                // Тикет со статусом WORKING
                Ticket::factory()
                    ->for($customer)
                    ->asWorking()
                    ->create();
                // Тикет со статусом DONE
                Ticket::factory()
                    ->for($customer)
                    ->asDone()
                    ->create();
            });
    }
}
