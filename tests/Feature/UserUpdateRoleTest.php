<?php

namespace Tests\Feature;

use App\Enums\User\Role as RoleEnum;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserUpdateRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * Тест смены роли пользователя из-под админа (ожидаем 200-й ответ)
     * @return void
     */
    public function test_admin_can_update_role(): void
    {
        $admin = User::factory()
            ->admin()
            ->create()
        ;

        $targetUser = User::factory()
            ->admin()
            ->create()
        ;

        $this->be($admin, 'sanctum');

        $payload = [
            'role_id' => Role::where('name', RoleEnum::MANAGER)
                ->first()
                ->id
        ];

        $response = $this->putJson("/api/user/{$targetUser->id}/role", $payload);

        // 200-й ответ
        $response->assertOk();

        // Загрузить повторно пользователя из базы в модель
        $targetUser->refresh();

        // Убедимся, что после выполнения запроса, у пользователя сменилась роль с админа на менеджера
        $this->assertTrue($targetUser->hasRole(RoleEnum::MANAGER));

        // Дополнительная проверка, что у пользователя уже нет роли админа
        $this->assertFalse($targetUser->hasRole(RoleEnum::ADMIN));
    }

    /**
     * Тест смены роли пользователя из-под менеджера (ожидаем 403-й ответ)
     * @return void
     */
    public function test_manager_cannot_update_role(): void
    {
        $manager = User::factory()
            ->manager()
            ->create()
        ;

        $targetUser = User::factory()
            ->admin()
            ->create()
        ;

        $this->be($manager, 'sanctum');

        $payload = [
            'role_id' => Role::where('name', RoleEnum::MANAGER)
                ->first()
                ->id
        ];

        $response = $this->putJson("/api/user/{$targetUser->id}/role", $payload);

        // 403-й ответ
        $response->assertForbidden();

        // Загрузить повторно пользователя из базы в модель
        $targetUser->refresh();

        // Убедимся, что после выполнения запроса, у пользователя не поменялась роль с админа на менеджера
        $this->assertTrue($targetUser->hasRole(RoleEnum::ADMIN));
    }
}
