<?php

use App\Enums\UserRole;

test('welcome page shows role selection buttons', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Who are you?');

    foreach (UserRole::loginOptions() as $role) {
        $response->assertSee($role->label());
        $response->assertSee(route('login.role', $role->slug()), false);
    }
});

test('role login pages render', function () {
    $this->get(route('login.role', 'super-admin'))
        ->assertOk()
        ->assertSee('Super Admin login');

    $this->get(route('login.role', 'admin'))
        ->assertOk()
        ->assertSee('Admin login');

    $this->get(route('login.role', 'member'))
        ->assertOk()
        ->assertSee('Member login');
});

test('generic login route redirects to welcome', function () {
    $this->get(route('login'))->assertRedirect('/');
});
