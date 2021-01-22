<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_create_a_hive()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $hive = [
            'name' => 'First hive'
        ];

        $response = $this->postJson('/api/hives', $hive);

        $response
            ->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Successfully created the hive.',
                'code' => 201,
                'hive' => [
                    'name' => 'First hive'
                ],
            ]);

        $this->assertDatabaseHas('hives', $hive);
    }
}
