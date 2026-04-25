<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * Тест удаления пользователя из-под админа (ожидаем 200-й ответ)
     * @return void
     */
    public function test_admin_can_delete_user(): void
    {
        $admin = User::factory()
            ->admin()
            ->create()
        ;

        $targetUser = User::factory()
            ->manager()
            ->create()
        ;

        $this->be($admin, 'sanctum');

        $response = $this->deleteJson("/api/user/{$targetUser->id}");

        // 200-й ответ
        $response->assertOk();

        $this->assertSoftDeleted('users', [
            'id' => $targetUser->id
        ]);
    }

    /**
     * Тест удаления пользователя из-под менеджера (ожидаем 403-й ответ)
     * @return void
     */
    public function test_manager_cannot_delete_user(): void
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

        $response = $this->deleteJson("/api/user/{$targetUser->id}");

        // 403-й ответ
        $response->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'deleted_at' => null
        ]);
    }
}
