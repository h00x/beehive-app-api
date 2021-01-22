<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_login()
    {
        $password = 'secret';
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $this->postJson('/api/login', [
            'email' => $user['email'],
            'password' => $password,
        ])
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Successfully logged in.',
                'code' => 200,
            ]);
    }

    public function test_a_user_using_the_wrong_password_gets_an_error()
    {
        $user = User::factory()->create();

        $this->postJson('/api/login', [
            'email' => $user['email'],
            'password' => 'WrongPassword',
        ])
            ->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Provided credentials are incorrect.',
                'code' => 401,
            ]);
    }

    public function test_a_not_registered_user_gets_an_error()
    {
        $this->postJson('/api/login', [
            'email' => 'dave@test.com',
            'password' => 'super-secret',
        ])
            ->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Provided credentials are incorrect.',
                'code' => 401,
            ]);
    }
}
