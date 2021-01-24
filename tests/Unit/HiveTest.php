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

    public function test_a_get_on_hives_only_returns_the_hives_the_user_is_authenticated_for()
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

    public function test_an_unauthenticated_user_cannot_get_hives()
    {
        User::factory()
            ->has(Hive::factory()->count(4))
            ->create();

        $response = $this->getJson('/api/hives');

        $response
            ->assertStatus(401)
            ->assertExactJson([
                'status' => 'error',
                'message' => 'Unauthenticated.',
                'code' => 401,
            ]);
    }

    public function test_you_can_update_a_hive()
    {
        $user = User::factory()
            ->has(Hive::factory()->count(4))
            ->create();

        $this->signIn($user);

        $updatedHive = [
            'name' => 'changed title',
        ];

        $response = $this->patchJson('/api/hives/' . $user->hives()->first()->id, $updatedHive);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'hive' => $user->hives()->first()->toArray(),
                'status' => 'success',
                'message' => 'Successfully updated Hive',
                'code' => 200,
            ]);

        $this->assertDatabaseHas('hives', $updatedHive);
    }

    public function test_a_unauthenticated_user_cannot_update_the_hive_of_an_other_user()
    {
        $user1 = User::factory()
            ->has(Hive::factory()->count(4))
            ->create();

        $user2 = User::factory()->create();

        $this->signIn($user2);

        $updatedHive = [
            'name' => 'changed title',
        ];

        $response = $this->patchJson('/api/hives/' . $user1->hives()->first()->id, $updatedHive);

        $response
            ->assertStatus(403)
            ->assertExactJson([
                'status' => 'error',
                'message' => 'You do not own this post.',
                'code' => 403,
            ]);

        $this->assertDatabaseMissing('hives', $updatedHive);
    }

    public function test_updating_a_hive_with_invalid_data_returns_an_error()
    {
        $user = User::factory()
            ->has(Hive::factory()->count(4))
            ->create();

        $this->signIn($user);

        $updatedHive = [
            'empty' => 'true',
            'archived' => 33
        ];

        $response = $this->patchJson('/api/hives/' . $user->hives()->first()->id, $updatedHive);

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

    public function test_updating_a_hive_with_a_not_existing_id_returns_an_error()
    {
        $user = User::factory()
            ->has(Hive::factory())
            ->create();

        $this->signIn($user);

        $updatedHive = [
            'name' => 'changed title',
        ];

        $response = $this->patchJson('/api/hives/mangled', $updatedHive);

        $response
            ->assertStatus(404)
            ->assertExactJson([
                'status' => 'error',
                'message' => 'Model not found.',
                'code' => 404,
            ]);

        $this->assertDatabaseMissing('hives', $updatedHive);
    }

    public function test_you_can_get_a_single_hive()
    {
        $user = User::factory()
            ->has(Hive::factory()->count(4))
            ->create();

        $this->signIn($user);

        $response = $this->getJson('/api/hives/' . $user->hives()->latest()->first()->id);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'hive' => $user->hives()->latest()->first()->toArray(),
                'status' => 'success',
                'message' => 'Successfully found the hive.',
                'code' => 200,
            ]);
    }

    public function test_getting_a_single_hive_with_a_not_existing_id_returns_an_error()
    {
        $user = User::factory()
            ->has(Hive::factory()->count(4))
            ->create();

        $this->signIn($user);

        $response = $this->getJson('/api/hives/934298');

        $response
            ->assertStatus(404)
            ->assertExactJson([
                'status' => 'error',
                'message' => 'Model not found.',
                'code' => 404,
            ]);
    }

    public function test_you_can_delete_a_hive()
    {
        $user = User::factory()
            ->has(Hive::factory()->count(4))
            ->create();

        $this->signIn($user);

        $hive = $user->hives()->latest()->first();

        $response = $this->deleteJson('/api/hives/' . $hive->id);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'status' => 'success',
                'message' => 'Successfully deleted the Hive.',
                'code' => 200,
            ]);

        $this->assertDatabaseMissing('hives', $hive->toArray());
    }

    public function test_deleting_a_hive_with_a_not_existing_id_returns_an_error()
    {
        $user = User::factory()
            ->has(Hive::factory()->count(4))
            ->create();

        $this->signIn($user);

        $response = $this->deleteJson('/api/hives/932498');

        $response
            ->assertStatus(404)
            ->assertExactJson([
                'status' => 'error',
                'message' => 'Model not found.',
                'code' => 404,
            ]);
    }
}
