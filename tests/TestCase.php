<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $user;

    protected function authorize() {
        $password = '12345678';
        $this->createUser($password);
        $token = $this->getToken($this->user->email, $password);
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);
        return $this;
    }

    protected function createUser($password) {
        $this->user = User::factory()->create([
            'password' => $password,
        ]);
    }

    protected function getToken($email, $password) {
        $response = $this->postJson('/api/auth/login', [
            'email'=> $email,
            'password'=> $password,
        ]);
        return $response->json('token');
    }

}
