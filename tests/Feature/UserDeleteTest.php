<?php

namespace Tests\Feature;

use App\Enums\User\RoleEnum;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;

class UserDeleteTest extends UserTest
{
    /**
     * Тест проверки привилегий пользователей на удаление пользователя
     * @param RoleEnum $actingAsRole Под какой ролью пользователя авторизуемся
     * @param int $expectedStatus Какой статус ответа ожидаем
     * @return void
     */
    #[DataProvider('permissionsDataProvider')]
    public function test_delete_user_permissions(
        RoleEnum $actingAsRole,
        int $expectedStatus
    ): void
    {
        // Создадим тестового пользователя
        $user = $this->createUser(RoleEnum::MANAGER);
        // Выполняем запрос с переданной ролью
        $response = $this->doActingAsRoleRequest($actingAsRole, $user->id);
        // Ожидаем получить статус ответа тот же, что и в провайдере данных
        $response->assertStatus($expectedStatus);
        // Ожидаем, что пользователь будет удалён
        if ($actingAsRole === RoleEnum::ADMIN) {
            $this->assertSoftDeleted('users', [
                'id' => $user->id,
            ]);
        }
        // Ожидаем, что пользователь не будет удалён
        if ($actingAsRole === RoleEnum::MANAGER) {
            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'deleted_at' => null
            ]);
        }
    }

    /**
     * Тест на валидацию полей при удалении пользователя
     * @param RoleEnum $actingAsRole Под какой ролью пользователя авторизуемся
     * @param array $overrides Массив полей, которые хотим переопределить
     * @param int $expectedStatus Какой статус ответа ожидаем
     * @param array $expectedValidationErrors Список ошибок валидации, которые ожидаем получить
     * @return void
     */
    #[DataProvider('validationDataProvider')]
    public function test_delete_user_validation(
        RoleEnum $actingAsRole,
        array $overrides,
        int $expectedStatus,
        array $expectedValidationErrors
    ): void
    {
        // Выполняем запрос с переданной ролью
        $response = $this->doActingAsRoleRequest($actingAsRole, $overrides['id']);
        // Ожидаем получить статус ответа тот же, что и в провайдере данных
        $response->assertStatus($expectedStatus);
        // Ожидаем получить список ошибок валидации, что и в провайдере данных
        if (!empty($expectedValidationErrors)) {
            $response->assertJsonValidationErrors($expectedValidationErrors);
        }
    }

    /**
     * Проверка на существование id в базе
     * @return void
     */
    public function test_delete_user_validation_exists(): void
    {
        // Передаём id несуществующего пользователя
        $missingId = (int) User::query()->max('id') + 1000;
        // Выполняем запрос с переданной ролью
        $response = $this->doActingAsRoleRequest(RoleEnum::ADMIN, $missingId);
        // 422-й ответ
        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['id'])
        ;
    }

    public static function permissionsDataProvider(): array
    {
        return [
            'admin can delete user' => [RoleEnum::ADMIN, 200],
            'manager cannot delete user' => [RoleEnum::MANAGER, 403]
        ];
    }

    public static function validationDataProvider(): array
    {
        return [
            'id must be integer' => self::defaultValidationDataProvider([
                'overrides' => ['id' => 'not-integer']
            ]),
            'id must be greater than zero (0)' => self::defaultValidationDataProvider([
                'overrides' => ['id' => 0]
            ]),
            'id must be greater than zero (-5)' => self::defaultValidationDataProvider([
                'overrides' => ['id' => -5]
            ])
        ];
    }

    protected static function defaultValidationDataProvider(array $overrides = []): array
    {
        return array_merge([
            'actingAsRole' => RoleEnum::ADMIN,
            'expectedStatus' => 422,
            'expectedValidationErrors' => ['id']
        ], $overrides);
    }

    /**
     * Хелпер-матод для выполнения запроса под заданной ролью
     * @param RoleEnum $actingAsRole Под какой ролью пользователя авторизуемся
     * @param mixed $userId ID пользователя
     * @return TestResponse
     */
    protected function doActingAsRoleRequest(RoleEnum $actingAsRole, mixed $userId): TestResponse
    {
        // Логинимся под требуемой ролью
        $this->actingAsRole($actingAsRole);
        // DELETE /api/user/{id}
        return $this->deleteJson("/api/user/{$userId}");
    }
}
