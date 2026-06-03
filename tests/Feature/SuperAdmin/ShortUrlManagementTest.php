<?php

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(SuperAdminSeeder::class);
    $this->superAdmin = User::where('email', 'superadmin@sembark.test')->first();
});

test('super admin can view all short urls page', function () {
    $this->actingAs($this->superAdmin)
        ->get(route('super-admin.short-urls.index'))
        ->assertOk()
        ->assertSee('Short URLs')
        ->assertSee('All short URLs across every company');
});

test('super admin can fetch all short urls data server side', function () {
    $company = Company::create(['name' => 'Globex', 'email' => 'globex@example.com']);
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
        'company_id' => $company->id,
    ]);

    ShortUrl::create([
        'company_id' => $company->id,
        'user_id' => $admin->id,
        'original_url' => 'https://globex.test',
        'code' => 'globex1',
        'title' => 'Globex link',
    ]);

    $response = $this->actingAs($this->superAdmin)
        ->getJson(route('super-admin.short-urls.data', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => 'globex1'],
        ]));

    $response->assertOk()
        ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);

    expect($response->json('data.0.company_name'))->toBe('Globex');
    expect(strip_tags($response->json('data.0.short_link')))->toContain('globex1');
});

test('non super admin cannot view super admin short urls', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('super-admin.short-urls.index'))
        ->assertForbidden();
});
