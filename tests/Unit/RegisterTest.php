<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_new_user_can_register()
    {
        $user = [
            'name' => 'Dave',
            'email' => 'dave@test.com',
            'password' => 'secretPasssword',
            'password_confirmation' => 'secretPasssword',
        ];

        $response = $this->postJson('/api/register', $user);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Successfully registered',
                'code' => 200,
            ]);

        $this->assertDatabaseHas('users', Arr::except($user, ['password', 'password_confirmation']));
    }

    public function test_a_new_user_cannot_use_wrong_credentials()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Dave',
            'email' => 'malformed#email,com',
            'password' => 'short',
            'password_confirmation' => 'wrongConfirm',
        ]);

        $response
            ->assertStatus(422)
            ->assertExactJson([
                'message' => 'The given data was invalid.',
                'status' => 'error',
                'code' => 422,
                'errors' => [
                    'email' => [
                        'The email must be a valid email address.'
                    ],
                    'password' => [
                        'The password must be at least 8 characters.',
                        'The password confirmation does not match.'
                    ]
                ]
            ]);
    }
}
