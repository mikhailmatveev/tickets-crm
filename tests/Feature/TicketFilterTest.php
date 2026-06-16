<?php

namespace Tests\Feature;

use App\Enums\Ticket\StatusEnum;
use App\Enums\User\Role;
use Carbon\Carbon;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;

class TicketFilterTest extends TicketTest
{
    #[DataProvider('validationDataProvider')]
    public function test_ticket_filter_validation(
        Role $actingAsRole,
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

    public static function validationDataProvider(): array
    {
        return [
            'email must be string' => self::defaultProviderData([
                'overrides' => ['filters' => ['email' => ['not-string']]],
                'expectedValidationErrors' => ['email']
            ]),
            'email must have min 3 chars' => self::defaultProviderData([
                'overrides' => ['filters' => ['email' => 'ab']],
                'expectedValidationErrors' => ['email']
            ]),
            'email must have max 255 chars' => self::defaultProviderData([
                'overrides' => ['filters' => ['email' => str_repeat('a', 256)]],
                'expectedValidationErrors' => ['email']
            ]),
            'phone must be string' => self::defaultProviderData([
                'overrides' => ['filters' => ['phone' => ['not-string']]],
                'expectedValidationErrors' => ['phone']
            ]),
            'phone must have min 3 chars' => self::defaultProviderData([
                'overrides' => ['filters' => ['phone' => '79']],
                'expectedValidationErrors' => ['phone']
            ]),
            'phone must have max 20 chars' => self::defaultProviderData([
                'overrides' => ['filters' => ['phone' => str_repeat('a', 21)]],
                'expectedValidationErrors' => ['phone']
            ]),
            'date must be valid' => self::defaultProviderData([
                'overrides' => ['filters' => ['date' => 'invalid-date']],
                'expectedValidationErrors' => ['date']
            ]),
            'status must be string' => self::defaultProviderData([
                'overrides' => ['filters' => ['status' => ['not-string']]],
                'expectedValidationErrors' => ['status']
            ]),
            'status must be in allowed values' => self::defaultProviderData([
                'overrides' => ['filters' => ['status' => 'Hello world!']],
                'expectedValidationErrors' => ['status']
            ]),
            'validation reports multiple fields at once' => self::defaultProviderData([
                'overrides' => [
                    'filters' => [
                        'email' => ['not-string'],
                        'phone' => '79',
                        'date' => 'invalid-date',
                        'status' => 'Hello world!'
                    ]
                ],
                'expectedValidationErrors' => ['email', 'phone', 'date', 'status']
            ]),
            'validation successful' => self::defaultProviderData([
                'overrides' => [
                    'filters' => [
                        'email' => 'user@example.com',
                        'phone' => '+799912345678',
                        'date' => Carbon::now()->toDateString(),
                        'status' => StatusEnum::DONE
                    ]
                ],
                'expectedStatus' => 200
            ])
        ];
    }

    /**
     * Возвращает массив с изначально дефолтными значениями, переопределяемыми в $overrides, для провайдера данных
     * @param array $overrides Массив для переопределения дефолтных значений
     * @return array Изменённый массив
     */
    protected static function defaultProviderData(array $overrides = []): array
    {
        return array_merge([
            'actingAsRole' => Role::ADMIN,
            'overrides' => [],
            'expectedStatus' => 422,
            'expectedValidationErrors' => []
        ], $overrides);
    }

    /**
     * Хелпер-матод для выполнения запроса под заданной ролью
     * @param Role $actingAsRole Под какой ролью пользователя авторизуемся
     * @param array $payload Массив полей для передачи в запрос
     * @return TestResponse
     */
    protected function doActingAsRoleRequest(Role $actingAsRole, array $payload): TestResponse
    {
        // Логинимся под требуемой ролью
        $this->actingAsRole($actingAsRole);
        // GET /api/tickets
        return $this->json('GET', '/api/tickets', $payload);
    }

    /**
     * По-умолчанию возвращает массив валидных полей, которые через $overrides можно переопределить для тестов на валидацию
     * @param array $overrides Массив для переопределения валидных полей
     * @return array
     */
    protected function validPayload(array $overrides = []): array
    {
        return array_merge([
            'filters' => [
                'email' => 'user@example.com',
                'phone' => '+799912345678',
                'date' => Carbon::now()->toDateString(),
                'status' => StatusEnum::DONE
            ]
        ], $overrides);
    }
}
