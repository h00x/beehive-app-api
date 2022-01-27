<?php

namespace Tests;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected bool $tenancy = false;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh');

        if ($this->tenancy) {
            $this->initializeTenancy();
        }
    }

    public function tearDown(): void
    {
        config([
            'tenancy.queue_database_deletion' => false,
            'tenancy.delete_database_after_tenant_deletion' => true,
        ]);

        tenancy()->query()->delete();

        parent::tearDown();
    }

    /**
     * @param null|User $user
     * @return User
     */
    protected function signIn($user = null): User
    {
        $user = $user ?: User::factory()->create();

        $this->actingAs($user);

        return $user;
    }

    public function initializeTenancy()
    {
        $tenant = Tenant::create();
        tenancy()->initialize($tenant);
    }
}
