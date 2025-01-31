<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTest extends TestCase
{
    #[Test]
    public function api_json_example(): void
    {
        $response = $this->getJson('/api/test');

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'foo' => 'test',
                'year' => 2024
            ]);
    }
}
