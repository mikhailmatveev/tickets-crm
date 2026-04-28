<?php

namespace Tests\Feature;

use App\Models\User;

class UserCreateValidationTest extends UserCreateTest
{
    /**
     * Валидация пустого поля name
     * @return void
     */
    public function test_name_is_required(): void
    {
        $this->performTypicalValidationTest(['name' => '']);
    }

    /**
     * Валидация поля name при попытке передачи не строки
     * @return void
     */
    public function test_name_must_be_string(): void
    {
        $this->performTypicalValidationTest(['name' => ['not-string']]);
    }

    /**
     * Валидация поля name при превышении максимальной длины 255 символов
     * @return void
     */
    public function test_name_must_not_exceed_255_chars(): void
    {
        $this->performTypicalValidationTest(['name' => str_repeat('a', 256)]);
    }

    /**
     * Валидация пустого поля email
     * @return void
     */
    public function test_email_is_required(): void
    {
        $this->performTypicalValidationTest(['email' => '']);
    }

    /**
     * Валидация поля email при попытке передачи не строки
     * @return void
     */
    public function test_email_must_be_string(): void
    {
        $this->performTypicalValidationTest(['email' => ['not-string']]);
    }

    /**
     * Валидация поля email при попытке передать некорректный адрес электронной почты
     * @return void
     */
    public function test_email_must_be_valid(): void
    {
        $this->performTypicalValidationTest(['email' => ['invalid-email']]);
    }

    /**
     * Проверка уникальности email (пользователей с одинаковым email не должно существовать)
     * @return void
     */
    public function test_email_must_be_unique(): void
    {
        $this->actingAsAdmin();
        // Тестовый пользователь
        User::factory()->create(['email' => 'duplicate@example.com']);
        // POST /api/user
        $response = $this->postJson(
            '/api/user',
            $this->validPayload(['email' => 'duplicate@example.com'])
        );
        // 422-й ответ
        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email'])
        ;
    }

    /**
     * Валидация пустого поля password
     * @return void
     */
    public function test_password_is_required(): void
    {
        $this->performTypicalValidationTest(['password' => '']);
    }

    /**
     * Валидация поля password при попытке передачи не строки
     * @return void
     */
    public function test_password_must_be_string(): void
    {
        $this->performTypicalValidationTest(['password' => ['not-string']]);
    }

    /**
     * Валидация поля password при попытке передать меньше 6 символов
     * @return void
     */
    public function test_password_must_have_min_6_chars(): void
    {
        $this->performTypicalValidationTest(['password' => '12345']);
    }

    /**
     * Валидация пустого поля role
     * @return void
     */
    public function test_role_is_required(): void
    {
        $this->performTypicalValidationTest(['role' => '']);
    }

    /**
     * Валидация поля role при передаче не строки
     * @return void
     */
    public function test_role_must_be_string(): void
    {
        $this->performTypicalValidationTest(['role' => ['manager']]);
    }

    /**
     * Валидация допустимых значений поля role
     * @return void
     */
    public function test_role_must_be_in_allowed_values(): void
    {
        $this->performTypicalValidationTest(['role' => 'super-admin']);
    }

    /**
     * Валидация при передаче некорректных полей разом
     * @return void
     */
    public function test_validation_reports_multiple_fields_at_once(): void
    {
        $this->performTypicalValidationTest([
            'name' => '',
            'email' => 'wrong-email',
            'password' => '123',
            'role' => 'invalid-role',
        ]);
    }

    /**
     * Тест на успешную валидацию полей и создание нового пользователя
     * @return void
     */
    public function test_admin_can_create_user_with_valid_payload(): void
    {
        $this->actingAsAdmin();
        // Валидные данные
        $payload = $this->validPayload();
        // POST /api/user
        $response = $this->postJson('/api/user', $payload);
        // 422-й ответ
        $response->assertCreated();
        // Проверим, что пользователь был записан в базу
        $this->assertDatabaseHas('users', [
            'name' => $payload['name'],
            'email' => $payload['email'],
        ]);
    }

    /**
     * Хелпер-метод для выполнения однотипных валидаций для уменьшения дублирования кода
     * @param array $payload Данные запроса
     * @return void
     */
    private function performTypicalValidationTest(array $payload = []): void
    {
        $this->actingAsAdmin();
        // POST /api/user
        $response = $this->postJson(
            '/api/user',
            $this->validPayload($payload)
        );
        // 422-й ответ
        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(array_keys($payload))
        ;
    }
}
