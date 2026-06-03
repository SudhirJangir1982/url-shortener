<?php

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin registration screen can be rendered', function () {
    $this->get(route('register.role', 'admin'))
        ->assertOk()
        ->assertSee('Admin registration')
        ->assertSee('Company name');
});

test('member registration screen can be rendered', function () {
    $company = Company::create(['name' => 'Acme Corp', 'email' => 'acme@example.com']);

    $this->get(route('register.role', 'member'))
        ->assertOk()
        ->assertSee('Member registration')
        ->assertSee('Acme Corp');
});

test('super admin cannot register', function () {
    $this->get('/register/super-admin')->assertNotFound();
});

test('new admin can register with company', function () {
    $response = $this->post('/register/admin', [
        'name' => 'Company Admin',
        'email' => 'admin@acme.test',
        'password' => 'password',
        'password_confirmation' => 'password',
        'company_name' => 'Acme Corp',
        'company_email' => 'contact@acme.test',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    $user = User::where('email', 'admin@acme.test')->first();
    expect($user->role)->toBe(UserRole::Admin);
    expect($user->company)->not->toBeNull();
    expect($user->company->name)->toBe('Acme Corp');

    $this->assertDatabaseHas('companies', [
        'name' => 'Acme Corp',
        'email' => 'contact@acme.test',
    ]);
});

test('new member can register for existing company', function () {
    $company = Company::create(['name' => 'Acme Corp', 'email' => 'contact@acme.test']);

    $response = $this->post('/register/member', [
        'name' => 'Team Member',
        'email' => 'member@acme.test',
        'password' => 'password',
        'password_confirmation' => 'password',
        'company_id' => $company->id,
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    $user = User::where('email', 'member@acme.test')->first();
    expect($user->role)->toBe(UserRole::Member);
    expect($user->company_id)->toBe($company->id);
});

test('member registration requires company', function () {
    $this->post('/register/member', [
        'name' => 'Team Member',
        'email' => 'member@acme.test',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('company_id');

    $this->assertGuest();
});
