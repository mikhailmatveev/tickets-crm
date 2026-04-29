<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;

class TicketTest extends FeatureTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }
}
