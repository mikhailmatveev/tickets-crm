<?php

namespace Tests\Feature;

use Tests\TestCase;

class MainPageTest extends TestCase
{
    /**
     * Базовый тест проверки доступности главной страницы
     * @return void
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // 200-й ответ
        $response->assertOk();
    }
}
