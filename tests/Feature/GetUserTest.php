<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function get_user_without_token_returns_http_error(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertJson(["message" => "Unauthenticated."]);
        $response->assertStatus(401);
    }

    #[Test]
    public function get_user_with_token_returns_user_data(): void
    {
        $response = $this->authorize()->getJson('/api/user');
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $this->user->id,
            'email' => $this->user->email,
            'name' => $this->user->name,
        ]);
    }
}
