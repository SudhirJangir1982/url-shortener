<?php

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createCompanyAdminForTeam(): User
{
    $company = Company::create(['name' => 'Acme', 'email' => 'acme@example.com']);

    return User::factory()->create([
        'role' => UserRole::Admin,
        'company_id' => $company->id,
    ]);
}

test('company admin can view team page', function () {
    $admin = createCompanyAdminForTeam();

    $this->actingAs($admin)
        ->get(route('admin.team.index'))
        ->assertOk()
        ->assertSee('Team')
        ->assertSee('Invite admin or member')
        ->assertSee('Team members');
});

test('company admin can invite admin or member', function () {
    $admin = createCompanyAdminForTeam();

    $this->actingAs($admin)
        ->post(route('admin.team.invitations.store'), [
            'name' => 'New Member',
            'email' => 'newmember@acme.test',
            'role' => 'member',
        ])
        ->assertRedirect(route('admin.team.index'))
        ->assertSessionHas('invitation_link');

    $this->assertDatabaseHas('invitations', [
        'company_id' => $admin->company_id,
        'email' => 'newmember@acme.test',
        'role' => UserRole::Member->value,
    ]);
});

test('company admin can fetch team datatables server side', function () {
    $admin = createCompanyAdminForTeam();
    User::factory()->create([
        'role' => UserRole::Member,
        'company_id' => $admin->company_id,
        'name' => 'Existing Member',
        'email' => 'member@acme.test',
    ]);

    Invitation::create([
        'company_id' => $admin->company_id,
        'invited_by' => $admin->id,
        'name' => 'Pending Admin',
        'email' => 'pending@acme.test',
        'role' => UserRole::Admin,
        'token' => Invitation::generateToken(),
        'expires_at' => now()->addDays(7),
    ]);

    $members = $this->actingAs($admin)
        ->getJson(route('admin.team.members.data', ['draw' => 1, 'start' => 0, 'length' => 10]))
        ->assertOk();

    expect(collect($members->json('data'))->pluck('email'))->toContain('member@acme.test');

    $invitations = $this->actingAs($admin)
        ->getJson(route('admin.team.invitations.data', ['draw' => 1, 'start' => 0, 'length' => 10]))
        ->assertOk();

    expect(collect($invitations->json('data'))->pluck('email'))->toContain('pending@acme.test');
    expect($invitations->json('data.0.actions'))->toContain('Copy link');
});

test('member cannot access admin team', function () {
    $company = Company::create(['name' => 'Co', 'email' => 'co@example.com']);
    $member = User::factory()->create([
        'role' => UserRole::Member,
        'company_id' => $company->id,
    ]);

    $this->actingAs($member)
        ->get(route('admin.team.index'))
        ->assertForbidden();
});
