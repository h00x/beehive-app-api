<?php

namespace Tests\Unit;

use App\Models\Hive;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_create_a_hive()
    {
        $this->signIn();

        $hive = [
            'name' => 'First hive',
            'empty' => true,
            'archived' => true,
        ];

        $response = $this->postJson('/api/hives', $hive);

        $response
            ->assertStatus(201)
            ->assertJson([
                'hive' => $hive,
                'status' => 'success',
                'message' => 'Successfully created the hive.',
                'code' => 201,
            ]);

        $this->assertDatabaseHas('hives', $hive);
    }

    public function test_a_hive_is_validated()
    {
        $this->signIn();

        $hive = [
            'empty' => 'true',
            'archived' => 33
        ];

        $response = $this->postJson('/api/hives', $hive);

        $response
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'The given data was invalid.',
                'code' => 422,
                'errors' => [
                    'name' => [
                        'The name field is required.'
                    ],
                    'empty' => [
                        'The empty field must be true or false.'
                    ],
                    'archived' => [
                        'The archived field must be true or false.'
                    ],
                ],
            ]);
    }

    public function test_a_user_can_get_multiple_hives()
    {
        $user = User::factory()
            ->has(Hive::factory()->count(4))
            ->create();

        $this->signIn($user);

        $response = $this->getJson('/api/hives');

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'hives' => $user->hives()->get()->toArray(),
                'meta' => [
                    'current_page' => 1,
                    'from' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'to' => 4,
                    'total' => 4,
                ],
                'status' => 'success',
                'message' => 'Successfully found the hives.',
                'code' => 200,
            ]);
    }

    public function test_a_user_cannot_get_the_hives_of_an_other_user()
    {
        $user1 = User::factory()
            ->has(Hive::factory()->count(4))
            ->create();

        User::factory()
            ->has(Hive::factory()->count(4))
            ->create();

        $this->signIn($user1);

        $response = $this->getJson('/api/hives');

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'hives' => $user1->hives()->get()->toArray(),
                'meta' => [
                    'current_page' => 1,
                    'from' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'to' => 4,
                    'total' => 4,
                ],
                'status' => 'success',
                'message' => 'Successfully found the hives.',
                'code' => 200,
            ]);
    }

    public function test_a_user_can_have_zero_hives()
    {
        $this->signIn();

        $response = $this->getJson('/api/hives');

        $response
            ->assertStatus(404)
            ->assertExactJson([
                'hives' => [],
                'meta' => [
                    'current_page' => 1,
                    'from' => null,
                    'last_page' => 1,
                    'per_page' => 10,
                    'to' => null,
                    'total' => 0,
                ],
                'status' => 'error',
                'message' => 'No hives found.',
                'code' => 404,
            ]);
    }

    public function test_you_can_paginate_the_get_all_hives_response()
    {
        $user = User::factory()
            ->has(Hive::factory()->count(40))
            ->create();

        $this->signIn($user);

        $response = $this->getJson('/api/hives?page=2');

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'hives' => $user->hives()->skip(10)->take(10)->get()->toArray(),
                'meta' => [
                    'current_page' => 2,
                    'from' => 11,
                    'last_page' => 4,
                    'per_page' => 10,
                    'to' => 20,
                    'total' => 40,
                ],
                'status' => 'success',
                'message' => 'Successfully found the hives.',
                'code' => 200,
            ]);
    }
}
