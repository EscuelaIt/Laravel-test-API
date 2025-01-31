<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_register_a_user_and_returns_a_token(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Miguel Angel Alvarez',
            'email'=> 'miguel@example.com',
            'password' => '12345678',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(["message" => "El usuario se ha creado"])
            ->assertJsonStructure([
                'message',
                'token'
            ]);
        $this->assertNotEmpty($response->json('token'));
        $this->assertDatabaseHas('users', [
            'name' => 'Miguel Angel Alvarez',
            'email'=> 'miguel@example.com',
        ]);
    }

    #[Test]
    public function it_returns_validation_errors_when_email_is_missing()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Miguel',
            'password' => '12345678',
        ]);

        $response->assertStatus(422);
        $response->assertInvalid(['email']);
        $response->assertValid(['name']);
    }

}
