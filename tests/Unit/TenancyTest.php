<?php

use Tests\TestCase;

class TenancyTest extends TestCase
{
    public function test_a_user_can_create_a_tenant()
    {
        $user = [
            'name' => 'Dave',
            'email' => 'dave@test.com',
            'password' => 'secretPasssword',
            'password_confirmation' => 'secretPasssword',
        ];

        $response = $this->postJson('/api/register', $user);

        dump($response);
    }
}
