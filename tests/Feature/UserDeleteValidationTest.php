<?php

namespace Tests\Feature;

use App\Models\User;

class UserDeleteValidationTest extends UserDeleteTest
{
    /**
     * Валидация параметра id на соответствие типу integer
     * @return void
     */
    public function test_id_must_be_integer(): void
    {
        $this->actingAsAdmin();
        // DELETE /api/user/{id}
        $response = $this->deleteJson('/api/user/not-integer');
        // 422-й ответ
        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['id'])
        ;
    }

    /**
     * Валидация параметра id на условие id > 0
     * @return void
     */
    public function test_id_must_be_greater_than_zero(): void
    {
        $this->actingAsAdmin();
        // DELETE /api/user/{id}
        $responseZero = $this->deleteJson('/api/user/0');
        // 422-й ответ
        $responseZero
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['id'])
        ;
        // DELETE /api/user/{id}
        $responseNegative = $this->deleteJson('/api/user/-5');
        // 422-й ответ
        $responseNegative
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['id'])
        ;
    }

    /**
     * Проверка на существование id в базе
     * @return void
     */
    public function test_id_must_exist_in_users_table(): void
    {
        $this->actingAsAdmin();
        // Несуществующий ID
        $missingId = (int) User::query()->max('id') + 1000;
        // DELETE /api/user/{id}
        $response = $this->deleteJson("/api/user/{$missingId}");
        // 422-й ответ
        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['id'])
        ;
    }
}
