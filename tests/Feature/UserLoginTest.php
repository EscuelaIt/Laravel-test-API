<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function user_can_login_and_recive_token(): void
    {
        $password = $this->faker->password();
        $user = User::factory()->create([
            'password' => $password,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => $password
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(["message" => "Login correcto"])
            ->assertJsonStructure([
                'message',
                'token'
            ]);
        $this->assertNotEmpty($response->json('token'));
    }

    #[Test]
    public function login_attempt_with_invalid_data_do_not_receives_correct_token(): void {
        $password = $this->faker->password();
        $user = User::factory()->create([
            'password' => $password,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'un password que no es válido'
        ]);

        $response
            ->assertStatus(401)
            ->assertJsonMissingPath('token');
    }

    #[Test]
    public function login_attempt_with_incomplete_data_send_validation_errors(): void {
        $response = $this->postJson('api/auth/login', []);

        $response->assertStatus(422);
        $response->assertInvalid(['email', 'password']);
        $response->assertJsonMissingPath('token');
    }
}
