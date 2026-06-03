<?php

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(SuperAdminSeeder::class);
    $this->superAdmin = User::where('email', 'superadmin@sembark.test')->first();
});

test('super admin can view admins list page', function () {
    $this->actingAs($this->superAdmin)
        ->get(route('super-admin.admins.index'))
        ->assertOk()
        ->assertSee('Admin Management')
        ->assertSee('admins-datatable', false);
});

test('super admin can fetch admins data server side', function () {
    $company = Company::create(['name' => 'Acme', 'email' => 'acme@test.com']);
    User::factory()->create([
        'name' => 'Jane Admin',
        'email' => 'jane@acme.test',
        'role' => UserRole::Admin,
        'company_id' => $company->id,
    ]);

    $response = $this->actingAs($this->superAdmin)
        ->getJson(route('super-admin.admins.data', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => 'Jane'],
        ]));

    $response->assertOk()
        ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data'])
        ->assertJsonPath('recordsFiltered', 1);

    expect($response->json('data.0.name'))->toBe('Jane Admin');
});

test('super admin can fetch members data server side', function () {
    $company = Company::create(['name' => 'Acme', 'email' => 'acme@test.com']);
    User::factory()->create([
        'name' => 'Bob Member',
        'email' => 'bob@acme.test',
        'role' => UserRole::Member,
        'company_id' => $company->id,
    ]);

    $response = $this->actingAs($this->superAdmin)
        ->getJson(route('super-admin.members.data', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
        ]));

    $response->assertOk();
    expect(collect($response->json('data'))->pluck('name'))->toContain('Bob Member');
});

test('admin cannot access super admin user management data', function () {
    $user = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($user)
        ->get(route('super-admin.admins.index'))
        ->assertForbidden();

    $this->actingAs($user)
        ->getJson(route('super-admin.admins.data'))
        ->assertForbidden();
});
