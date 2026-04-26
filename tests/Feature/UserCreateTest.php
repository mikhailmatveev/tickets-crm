<?php

namespace Tests\Feature;

use App\Enums\User\Role;

class UserCreateTest extends UserTest
{
    /**
     * По-умолчанию возвращает массив валидных полей, которые через $overrides можно переопределить для тестов на валидацию
     * @param array $overrides Массив для переопределения валидных полей
     * @return array
     */
    protected function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test Manager',
            'email' => 'test.manager@example.com',
            'password' => 'secret123',
            'role' => Role::MANAGER
        ], $overrides);
    }

    /**
     * Тест создания нового пользователя из-под админа (ожидаем 201-й ответ)
     * @return void
     */
    public function test_admin_can_create_user(): void
    {
        // Логинимся как админ
        $this->actingAsAdmin();

        $payload = $this->validPayload();

        // POST /api/user
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
        // Логинимся как менеджер
        $this->actingAsManager();

        $payload = $this->validPayload();

        // POST /api/user
        $response = $this->postJson('/api/user', $payload);

        // 403-й ответ
        $response->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'email' => $payload['email']
        ]);
    }
}
