<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Наполняем базу данными:
     * RoleAndPermissionSeeder назначает права группе пользователей
     * AdminSeeder создаёт главного администратора
     * UserSeeder создаёт фейковых администраторов и менеджеров
     * CustomerSeeder создаёт новых клиентов
     * TicketSeeder создаёт новые тикеты и связывает их с клиентами
     * TicketReplySeeder создаёт ответы на тикеты с привязкой к менеджерам
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            CustomerSeeder::class,
            TicketSeeder::class,
            TicketReplySeeder::class
        ]);
    }
}
