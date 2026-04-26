<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * Хелпер-метод для авторизации под админом
     * @return void
     */
    protected function actingAsAdmin(): void
    {
        $admin = User::factory()
            ->admin()
            ->create()
        ;
        $this->be($admin, 'sanctum');
    }

    /**
     * Хелпер-метод для авторизации под менеджером
     * @return void
     */
    protected function actingAsManager(): void
    {
        $manager = User::factory()
            ->manager()
            ->create()
        ;
        $this->be($manager, 'sanctum');
    }
}
