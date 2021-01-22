<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_new_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Dave',
            'email' => 'dave@test.com',
            'password' => 'secretPasssword',
            'password_confirmation' => 'secretPasssword',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Successfully registered',
                'code' => 200,
            ]);
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
