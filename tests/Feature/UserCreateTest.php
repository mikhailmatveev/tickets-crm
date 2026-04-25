<?php

namespace Tests\Feature;

use App\Enums\User\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * Тест создания нового пользователя из-под админа (ожидаем 201-й ответ)
     * @return void
     */
    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()
            ->admin()
            ->create()
        ;

        $this->be($admin, 'sanctum');

        $payload = [
            'name' => 'Test Manager',
            'email' => 'test.manager@example.com',
            'password' => 'secret123',
            'role' => Role::MANAGER
        ];

        $response = $this->postJson('/api/user', $payload);

        // 201-й ответ
        $response->assertCreated();

        $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
            'name' => $payload['name']
        ]);
    }

    /**
     * Тест создания нового пользователя из-под менеджера (ожидаем 403-й ответ)
     * @return void
     */
    public function test_manager_cannot_create_user(): void
    {
        $manager = User::factory()
            ->manager()
            ->create()
        ;

        $this->be($manager, 'sanctum');

        $payload = [
            'name' => 'Blocked User',
            'email' => 'blocked.user@example.com',
            'password' => 'secret123',
            'role' => Role::MANAGER
        ];

        $response = $this->postJson('/api/user', $payload);

        // 403-й ответ
        $response->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'email' => $payload['email']
        ]);
    }
}
