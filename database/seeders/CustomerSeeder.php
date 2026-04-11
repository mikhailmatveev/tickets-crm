<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Кол-во тестовых клиентов
     */
    public const int CUSTOMERS_COUNT = 10;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::factory()
            ->count(self::CUSTOMERS_COUNT)
            ->create();
    }
}
