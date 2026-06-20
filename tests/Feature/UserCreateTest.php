<?php

namespace Tests\Feature;

use App\Enums\User\RoleEnum;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use Exception;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;

class UserCreateTest extends UserTest
{
    /**
     * Тест проверки привилегий пользователей на создание пользователя
     * @param RoleEnum $actingAsRole Под какой ролью пользователя авторизуемся
     * @param int $expectedStatus Какой статус ответа ожидаем
     * @return void
     * @throws Exception
     */
    #[DataProvider('permissionsDataProvider')]
    public function test_create_user_permissions(
        RoleEnum $actingAsRole,
        int $expectedStatus
    ): void
    {
        // Подменяем реальный механизм отправки уведомлений фиктивным
        Notification::fake();

        $payload = $this->validPayload();
        // Выполняем запрос с переданной ролью
        $response = $this->doActingAsRoleRequest($actingAsRole, $payload);
        // Ожидаем получить статус ответа тот же, что и в провайдере данных
        $response->assertStatus($expectedStatus);

        // Ожидаем, что пользователь будет создан
        if ($actingAsRole === RoleEnum::ADMIN) {
            $this->assertDatabaseHas('users', [
                'email' => $payload['email'],
                'name' => $payload['name']
            ]);

            // Имитация получения письма
            Notification::assertSentTo(
                User::where('email', $payload['email'])->first(),
                UserCreatedNotification::class
            );
        }
        // Ожидаем, что пользователь не создастся
        if ($actingAsRole === RoleEnum::MANAGER) {
            $this->assertDatabaseMissing('users', [
                'email' => $payload['email']
            ]);
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
    public function test_create_user_validation(
        RoleEnum $actingAsRole,
        array $overrides,
        int $expectedStatus,
        array $expectedValidationErrors
    ): void
    {
        // Выполняем запрос с переданной ролью
        $response = $this->doActingAsRoleRequest($actingAsRole, $this->validPayload($overrides));
        // Ожидаем получить статус ответа тот же, что и в провайдере данных
        $response->assertStatus($expectedStatus);
        // Ожидаем получить список ошибок валидации, что и в провайдере данных
        if (!empty($expectedValidationErrors)) {
            $response->assertJsonValidationErrors($expectedValidationErrors);
        }
    }

    /**
     * Проверка уникальности email (пользователей с одинаковым email не должно существовать)
     * @return void
     */
    public function test_create_user_unique_email(): void
    {
        // Создаём тестового пользователя
        User::factory()->create(['email' => 'duplicate@example.com']);
        // Выполняем запрос с переданной ролью
        $response = $this->doActingAsRoleRequest(
            RoleEnum::ADMIN,
            $this->validPayload(['email' => 'duplicate@example.com'])
        );
        // 422-й ответ
        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email'])
        ;
    }

    public static function permissionsDataProvider(): array
    {
        return [
            'admin can create user' => [RoleEnum::ADMIN, 201],
            'manager cannot create user' => [RoleEnum::MANAGER, 403]
        ];
    }

    public static function validationDataProvider(): array
    {
        return [
            'name is required' => self::defaultValidationDataProvider([
                'overrides' => ['name' => ''],
                'expectedValidationErrors' => ['name']
            ]),
            'name must be string' => self::defaultValidationDataProvider([
                'overrides' => ['name' => ['not-string']],
                'expectedValidationErrors' => ['name']
            ]),
            'name must not exceed 255 chars' => self::defaultValidationDataProvider([
                'overrides' => ['name' => str_repeat('a', 256)],
                'expectedValidationErrors' => ['name']
            ]),
            'email is required' => self::defaultValidationDataProvider([
                'overrides' => ['email' => ''],
                'expectedValidationErrors' => ['email']
            ]),
            'email must be string' => self::defaultValidationDataProvider([
                'overrides' => ['email' => ['not-string']],
                'expectedValidationErrors' => ['email']
            ]),
            'email must be valid' => self::defaultValidationDataProvider([
                'overrides' => ['email' => 'invalid-email'],
                'expectedValidationErrors' => ['email']
            ]),
            'password is required' => self::defaultValidationDataProvider([
                'overrides' => ['password' => ''],
                'expectedValidationErrors' => ['password']
            ]),
            'password must be string' => self::defaultValidationDataProvider([
                'overrides' => ['password' => ''],
                'expectedValidationErrors' => ['password']
            ]),
            'password must have min 6 chars' => self::defaultValidationDataProvider([
                'overrides' => ['password' => '12345'],
                'expectedValidationErrors' => ['password']
            ]),
            'role is required' => self::defaultValidationDataProvider([
                'overrides' => ['role' => ''],
                'expectedValidationErrors' => ['role']
            ]),
            'role must be string' => self::defaultValidationDataProvider([
                'overrides' => ['role' => ['manager']],
                'expectedValidationErrors' => ['role']
            ]),
            'role must be in allowed values' => self::defaultValidationDataProvider([
                'overrides' => ['role' => 'super-admin'],
                'expectedValidationErrors' => ['role']
            ]),
            'validation reports multiple fields at once' => self::defaultValidationDataProvider([
                'overrides' => [
                    'name' => '',
                    'email' => 'wrong-email',
                    'password' => '123',
                    'role' => 'invalid-role',
                ],
                'expectedValidationErrors' => ['name', 'email', 'password', 'role']
            ])
        ];
    }

    protected static function defaultValidationDataProvider(array $overrides = []): array
    {
        return array_merge([
            'actingAsRole' => RoleEnum::ADMIN,
            'expectedStatus' => 422,
            'expectedValidationErrors' => []
        ], $overrides);
    }

    /**
     * Возвращает массив с изначально дефолтными значениями, переопределяемыми в $overrides, для провайдера данных
     * @param array $overrides Массив для переопределения дефолтных значений
     * @return array Изменённый массив
     */
    protected static function defaultProviderData(array $overrides = []): array
    {
        return array_merge([
            'actingAsRole' => RoleEnum::ADMIN,
            'overrides' => [],
            'expectedStatus' => 422,
            'expectedValidationErrors' => []
        ], $overrides);
    }

    /**
     * Хелпер-матод для выполнения запроса под заданной ролью
     * @param RoleEnum $actingAsRole Под какой ролью пользователя авторизуемся
     * @param array $payload Массив полей для передачи в запрос
     * @return TestResponse
     */
    protected function doActingAsRoleRequest(RoleEnum $actingAsRole, array $payload): TestResponse
    {
        // Логинимся под требуемой ролью
        $this->actingAsRole($actingAsRole);
        // POST /api/user
        return $this->postJson('/api/user', $payload);
    }

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
            'role' => RoleEnum::MANAGER
        ], $overrides);
    }
}
