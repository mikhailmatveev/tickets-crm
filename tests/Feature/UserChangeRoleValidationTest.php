<?php

namespace Feature;

use App\Enums\User\Role as RoleEnum;
use Spatie\Permission\Models\Role;

class UserChangeRoleValidationTest extends UserChangeRoleTest
{
    /**
     * Валидация пустого поля role_id
     * @return void
     */
    public function test_role_id_is_required(): void
    {
        $this->performTypicalValidationTest(['role_id' => null]);
    }

    /**
     * Валидация поля role_id на соответствие типу integer
     * @return void
     */
    public function test_role_id_must_be_integer(): void
    {
        $this->performTypicalValidationTest(['role_id' => 'not-integer']);
    }

    /**
     * Валидация поля role_id на условие id > 0
     * @return void
     */
    public function test_role_id_must_be_greater_than_zero(): void
    {
        $this->performTypicalValidationTest(['role_id' => 0]);
        $this->performTypicalValidationTest(['role_id' => -5]);
    }

    /**
     * Проверка на существование role_id в базе
     * @return void
     */
    public function test_role_id_must_exist_in_roles_table(): void
    {
        $missingId = (int) Role::query()->max('id') + 1000;
        $this->performTypicalValidationTest(['role_id' => $missingId]);
    }

    /**
     * Хелпер-метод для выполнения однотипных валидаций для уменьшения дублирования кода
     * @param array $payload Данные запроса
     * @return void
     */
    private function performTypicalValidationTest(array $payload = []): void
    {
        $this->actingAsAdmin();
        // Тестовый пользователь
        $user = $this->createUser(RoleEnum::ADMIN);
        // PUT /api/user/{id}/role
        $response = $this->putJson(
            "/api/user/{$user->id}/role",
            $this->validPayload($payload)
        );
        // 422-й ответ
        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(array_keys($payload))
        ;
    }

    /**
     * По-умолчанию возвращает массив валидных полей, которые через $overrides можно переопределить для тестов на валидацию
     * @param array $overrides Массив для переопределения валидных полей
     * @return array
     */
    protected function validPayload(array $overrides = []): array
    {
        return array_merge([
            'role_id' => $this->getValidRoleId(RoleEnum::MANAGER)
        ], $overrides);
    }
}
