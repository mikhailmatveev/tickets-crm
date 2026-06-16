<?php

namespace Tests\Feature;

use App\Enums\User\RoleEnum as RoleEnum;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;

class UserUpdateRoleTest extends UserTest
{
    /**
     * Тест проверки привилегий пользователей на создание пользователя
     * @param RoleEnum $actingAsRole Под какой ролью пользователя авторизуемся
     * @param int $expectedStatus Какой статус ответа ожидаем
     * @return void
     */
    #[DataProvider('permissionsDataProvider')]
    public function test_user_update_role_permissions(
        RoleEnum $actingAsRole,
        int $expectedStatus
    ): void
    {
        // Создадим тестового пользователя
        $user = $this->createUser(RoleEnum::ADMIN);
        // Выполняем запрос с переданной ролью
        $response = $this->doActingAsRoleRequest($actingAsRole, $user->id, $this->validPayload());
        // Ожидаем получить статус ответа тот же, что и в провайдере данных
        $response->assertStatus($expectedStatus);
        // Найдём тестового пользователя в базе
        $user = User::where('email', $user->email)
            ->firstOrFail()
        ;
        // Убедимся, что после выполнения запроса, у пользователя сменилась роль с админа на менеджера
        if ($actingAsRole === RoleEnum::ADMIN) {
            $this->assertTrue($user->hasRole(RoleEnum::MANAGER));
        }
        // Проверяем, что роль у пользователя осталась прежней
        if ($actingAsRole === RoleEnum::MANAGER) {
            $this->assertTrue($user->hasRole(RoleEnum::ADMIN));
        }
    }

    /**
     * Тест на валидацию полей при создании пользователя
     * @param RoleEnum $actingAsRole Под какой ролью пользователя авторизуемся
     * @param array $overrides Массив полей, которые хотим переопределить
     * @param int $expectedStatus Какой статус ответа ожидаем
     * @param array $expectedValidationErrors Список ошибок валидации, которые ожидаем получить
     * @return void
     */
    #[DataProvider('validationDataProvider')]
    public function test_user_update_role_validation(
        RoleEnum $actingAsRole,
        array $overrides,
        int $expectedStatus,
        array $expectedValidationErrors
    ): void
    {
        // Тестовый пользователь
        $user = $this->createUser(RoleEnum::ADMIN);
        // Выполняем запрос с переданной ролью
        $response = $this->doActingAsRoleRequest($actingAsRole, $user->id, $this->validPayload($overrides));
        // Ожидаем получить статус ответа тот же, что и в провайдере данных
        $response->assertStatus($expectedStatus);
        // Ожидаем получить список ошибок валидации, что и в провайдере данных
        if (!empty($expectedValidationErrors)) {
            $response->assertJsonValidationErrors($expectedValidationErrors);
        }
    }

    public static function permissionsDataProvider(): array
    {
        return [
            'admin can update role' => [RoleEnum::ADMIN, 200],
            'manager cannot update role' => [RoleEnum::MANAGER, 403]
        ];
    }

    public static function validationDataProvider(): array
    {
        return [
            'role id is required' => self::defaultValidationDataProvider([
                'overrides' => ['role_id' => null],
                'expectedValidationErrors' => ['role_id']
            ]),
            'role id must be integer' => self::defaultValidationDataProvider([
                'overrides' => ['role_id' => 'not-integer'],
                'expectedValidationErrors' => ['role_id']
            ]),
            'role id must be greater than zero (0)' => self::defaultValidationDataProvider([
                'overrides' => ['role_id' => 0],
                'expectedValidationErrors' => ['role_id']
            ]),
            'role id must be greater than zero (-5)' => self::defaultValidationDataProvider([
                'overrides' => ['role_id' => -5],
                'expectedValidationErrors' => ['role_id']
            ]),
            'role id must exist in roles table' => self::defaultValidationDataProvider([
                'overrides' => ['role_id' => 1000],
                'expectedValidationErrors' => ['role_id']
            ])
        ];
    }

    /**
     * Возвращает массив с изначально дефолтными значениями, переопределяемыми в $overrides, для провайдера данных
     * @param array $overrides Массив для переопределения дефолтных значений
     * @return array Изменённый массив
     */
    protected static function defaultValidationDataProvider(array $overrides = []): array
    {
        return array_merge([
            'actingAsRole' => RoleEnum::ADMIN,
            'overrides' => [],
            'expectedStatus' => 422,
            'expectedValidationErrors' => []
        ], $overrides);
    }

    /**
     * Хелпер-метод получения ID роли по её имени
     * @param RoleEnum $role Роль
     * @return int ID роли в таблице roles
     */
    protected static function getValidRoleId(RoleEnum $role): int
    {
        return Role::where('name', $role)
            ->first()
            ->id
        ;
    }

    /**
     * Хелпер-матод для выполнения запроса под заданной ролью
     * @param RoleEnum $actingAsRole Под какой ролью пользователя авторизуемся
     * @param int $userId ID пользователя, которому меняем проль
     * @param array $payload Массив полей для передачи в запрос
     * @return TestResponse
     */
    protected function doActingAsRoleRequest(RoleEnum $actingAsRole, int $userId, array $payload): TestResponse
    {
        // Логинимся под требуемой ролью
        $this->actingAsRole($actingAsRole);
        // PUT /api/user/{id}/role
        return $this->putJson(
            "/api/user/{$userId}/role",
            $payload
        );
    }

    /**
     * Массив валидных полей по-умолчанию
     * @param array $overrides
     * @return array
     */
    protected function validPayload(array $overrides = []): array
    {
        return array_merge([
            'role_id' => $this->getValidRoleId(RoleEnum::MANAGER)
        ], $overrides);
    }
}
