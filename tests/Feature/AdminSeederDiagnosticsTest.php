<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminSeederDiagnosticsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_missing_configuration_explains_why_admin_was_not_created(): void
    {
        config(['seeding.admin_email' => ' ']);
        $this->artisan('db:seed', ['--class' => AdminSeeder::class, '--force' => true])
            ->expectsOutputToContain('Admin tidak dibuat: isi SEED_ADMIN_EMAIL')
            ->assertSuccessful();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_existing_mixed_case_user_is_not_duplicated_or_promoted(): void
    {
        $user = User::factory()->create(['email' => 'Existing@Example.test', 'is_admin' => false]);
        config(['seeding.admin_email' => ' existing@example.test ']);
        $this->artisan('db:seed', ['--class' => AdminSeeder::class, '--force' => true])
            ->expectsOutputToContain('Email sudah digunakan user non-admin')
            ->assertSuccessful();
        $this->assertDatabaseCount('users', 1);
        $this->assertFalse($user->fresh()->is_admin);
    }
}
