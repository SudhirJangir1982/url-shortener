<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(SuperAdminSeeder::class);
});

test('super admin can log in and access dashboard', function () {
    $response = $this->post('/login/super-admin', [
        'email' => 'superadmin@sembark.test',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('super-admin.dashboard'));
    $this->get(route('super-admin.dashboard'))->assertOk()->assertSee('Super Admin Dashboard');
});

test('guest cannot access super admin dashboard', function () {
    $this->get(route('super-admin.dashboard'))->assertRedirect('/');
});

test('super admin cannot log in via member login page', function () {
    $response = $this->post('/login/member', [
        'email' => 'superadmin@sembark.test',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('non super admin cannot access super admin dashboard', function () {
    $user = User::factory()->create([
        'role' => UserRole::Member,
    ]);

    $this->actingAs($user)
        ->get(route('super-admin.dashboard'))
        ->assertForbidden();
});

test('super admin can log out', function () {
    $user = User::where('email', 'superadmin@sembark.test')->first();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect('/');

    $this->assertGuest();
});

test('super admin seeder creates user via database', function () {
    $this->assertDatabaseHas('users', [
        'email' => 'superadmin@sembark.test',
        'role' => UserRole::SuperAdmin->value,
        'company_id' => null,
    ]);
});
