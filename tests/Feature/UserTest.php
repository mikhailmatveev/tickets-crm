<?php

namespace Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;

class UserTest extends FeatureTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }
}
