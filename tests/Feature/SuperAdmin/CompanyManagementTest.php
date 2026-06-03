<?php

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(SuperAdminSeeder::class);
    $this->superAdmin = User::where('email', 'superadmin@sembark.test')->first();
});

test('super admin can view companies list page', function () {
    $this->actingAs($this->superAdmin)
        ->get(route('super-admin.companies.index'))
        ->assertOk()
        ->assertSee('Companies')
        ->assertSee('Add company');
});

test('super admin can fetch companies data server side', function () {
    $company = Company::create(['name' => 'Globex', 'email' => 'globex@example.com']);
    User::factory()->count(2)->create([
        'role' => \App\Enums\UserRole::Member,
        'company_id' => $company->id,
    ]);

    $response = $this->actingAs($this->superAdmin)
        ->getJson(route('super-admin.companies.data', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => 'Globex'],
        ]));

    $response->assertOk()
        ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);

    expect(strip_tags($response->json('data.0.name')))->toBe('Globex');
    expect($response->json('data.0.users_count'))->toBe(2);
});

test('super admin can create a company', function () {
    $this->actingAs($this->superAdmin)
        ->post(route('super-admin.companies.store'), [
            'name' => 'Initech',
            'email' => 'initech@example.com',
        ])
        ->assertRedirect()
        ->assertSessionHas('status');

    $company = Company::where('email', 'initech@example.com')->first();

    expect($company)->not->toBeNull();
    expect($company->name)->toBe('Initech');
});

test('super admin can view edit and update a company', function () {
    $company = Company::create(['name' => 'Acme', 'email' => 'acme@example.com']);

    $this->actingAs($this->superAdmin)
        ->get(route('super-admin.companies.show', $company))
        ->assertOk()
        ->assertSee('Acme')
        ->assertSee('Invite admin or member');

    $this->actingAs($this->superAdmin)
        ->put(route('super-admin.companies.update', $company), [
            'name' => 'Acme Corp',
            'email' => 'acme-corp@example.com',
        ])
        ->assertRedirect(route('super-admin.companies.show', $company));

    expect($company->fresh()->name)->toBe('Acme Corp');
});

test('super admin can invite admin or member to a company', function () {
    $company = Company::create(['name' => 'Umbrella', 'email' => 'umbrella@example.com']);

    $this->actingAs($this->superAdmin)
        ->post(route('super-admin.companies.invitations.store', $company), [
            'name' => 'Alice Admin',
            'email' => 'alice@umbrella.test',
            'role' => 'admin',
        ])
        ->assertRedirect(route('super-admin.companies.show', $company))
        ->assertSessionHas('invitation_link');

    $this->assertDatabaseHas('invitations', [
        'company_id' => $company->id,
        'email' => 'alice@umbrella.test',
        'role' => UserRole::Admin->value,
    ]);
});

test('guest can accept invitation and join company', function () {
    $company = Company::create(['name' => 'Wayne', 'email' => 'wayne@example.com']);
    $invitation = Invitation::create([
        'company_id' => $company->id,
        'invited_by' => $this->superAdmin->id,
        'name' => 'Bruce Wayne',
        'email' => 'bruce@wayne.test',
        'role' => UserRole::Member,
        'token' => Invitation::generateToken(),
        'expires_at' => now()->addDays(7),
    ]);

    $this->get(route('invitation.accept', $invitation->token))
        ->assertOk()
        ->assertSee('Accept invitation');

    $this->post(route('invitation.accept.store', $invitation->token), [
        'name' => 'Bruce Wayne',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertRedirect(route('dashboard'));

    $user = User::where('email', 'bruce@wayne.test')->first();
    expect($user)->not->toBeNull();
    expect($user->company_id)->toBe($company->id);
    expect($user->role)->toBe(UserRole::Member);
    expect($invitation->fresh()->accepted_at)->not->toBeNull();
});

test('super admin cannot delete company with users', function () {
    $company = Company::create(['name' => 'Busy Co', 'email' => 'busy@example.com']);
    User::factory()->create(['company_id' => $company->id, 'role' => UserRole::Admin]);

    $this->actingAs($this->superAdmin)
        ->delete(route('super-admin.companies.destroy', $company))
        ->assertRedirect(route('super-admin.companies.show', $company))
        ->assertSessionHas('error');

    expect($company->fresh())->not->toBeNull();
});

test('super admin can delete empty company', function () {
    $company = Company::create(['name' => 'Empty Co', 'email' => 'empty@example.com']);

    $this->actingAs($this->superAdmin)
        ->delete(route('super-admin.companies.destroy', $company))
        ->assertRedirect(route('super-admin.companies.index'));

    $this->assertDatabaseMissing('companies', ['id' => $company->id]);
});

test('non super admin cannot manage companies', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('super-admin.companies.index'))
        ->assertForbidden();
});
