<?php

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createCompanyAdmin(): User
{
    $company = Company::create(['name' => 'Acme', 'email' => 'acme@example.com']);

    return User::factory()->create([
        'role' => UserRole::Admin,
        'company_id' => $company->id,
    ]);
}

test('company admin can view short urls index', function () {
    $admin = createCompanyAdmin();

    $this->actingAs($admin)
        ->get(route('admin.short-urls.index'))
        ->assertOk()
        ->assertSee('Short URLs')
        ->assertSee('Add short URL');
});

test('destination url gets https prefix when missing', function () {
    $admin = createCompanyAdmin();

    $this->actingAs($admin)
        ->post(route('admin.short-urls.store'), [
            'original_url' => 'example.com/page',
        ])
        ->assertRedirect(route('admin.short-urls.index'));

    $shortUrl = ShortUrl::first();
    expect($shortUrl)->not->toBeNull();
    expect($shortUrl->original_url)->toBe('https://example.com/page');
    expect($shortUrl->code)->not->toBeEmpty();
});

test('company admin can create short url for their company', function () {
    $admin = createCompanyAdmin();

    $this->actingAs($admin)
        ->post(route('admin.short-urls.store'), [
            'original_url' => 'https://example.com/docs',
            'title' => 'Docs',
        ])
        ->assertRedirect(route('admin.short-urls.index'));

    $this->assertDatabaseHas('short_urls', [
        'company_id' => $admin->company_id,
        'user_id' => $admin->id,
        'original_url' => 'https://example.com/docs',
        'title' => 'Docs',
    ]);
});

test('company admin sees all company short urls not only their own', function () {
    $admin = createCompanyAdmin();
    $member = User::factory()->create([
        'role' => UserRole::Member,
        'company_id' => $admin->company_id,
    ]);

    ShortUrl::create([
        'company_id' => $admin->company_id,
        'user_id' => $member->id,
        'original_url' => 'https://member.example.com',
        'code' => 'memberlink',
    ]);

    ShortUrl::create([
        'company_id' => $admin->company_id,
        'user_id' => $admin->id,
        'original_url' => 'https://admin.example.com',
        'code' => 'adminlink',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.short-urls.index'))
        ->assertOk()
        ->assertSee('memberlink')
        ->assertSee('adminlink');
});

test('member cannot access admin short urls', function () {
    $company = Company::create(['name' => 'Co', 'email' => 'co@example.com']);
    $member = User::factory()->create([
        'role' => UserRole::Member,
        'company_id' => $company->id,
    ]);

    $this->actingAs($member)
        ->get(route('admin.short-urls.index'))
        ->assertForbidden();
});

test('member can only see their own short urls', function () {
    $company = Company::create(['name' => 'Co', 'email' => 'co@example.com']);
    $member = User::factory()->create([
        'role' => UserRole::Member,
        'company_id' => $company->id,
    ]);
    $otherMember = User::factory()->create([
        'role' => UserRole::Member,
        'company_id' => $company->id,
    ]);

    ShortUrl::create([
        'company_id' => $company->id,
        'user_id' => $member->id,
        'original_url' => 'https://mine.test',
        'code' => 'mine',
    ]);

    ShortUrl::create([
        'company_id' => $company->id,
        'user_id' => $otherMember->id,
        'original_url' => 'https://other.test',
        'code' => 'other',
    ]);

    $this->actingAs($member)
        ->get(route('member.short-urls.index'))
        ->assertOk()
        ->assertSee('mine')
        ->assertDontSee('other.test');
});

test('admin cannot access member short urls route', function () {
    $admin = createCompanyAdmin();

    $this->actingAs($admin)
        ->get(route('member.short-urls.index'))
        ->assertForbidden();
});

test('member can create and delete their own short url', function () {
    $company = Company::create(['name' => 'Co', 'email' => 'co@example.com']);
    $member = User::factory()->create([
        'role' => UserRole::Member,
        'company_id' => $company->id,
    ]);

    $this->actingAs($member)
        ->post(route('member.short-urls.store'), [
            'original_url' => 'https://member.example.com/page',
            'title' => 'My page',
        ])
        ->assertRedirect(route('member.short-urls.index'));

    $shortUrl = ShortUrl::where('original_url', 'https://member.example.com/page')->first();
    expect($shortUrl)->not->toBeNull();
    expect($shortUrl->user_id)->toBe($member->id);

    $this->actingAs($member)
        ->delete(route('member.short-urls.destroy', $shortUrl))
        ->assertRedirect(route('member.short-urls.index'));

    $this->assertDatabaseMissing('short_urls', ['id' => $shortUrl->id]);
});

test('member cannot delete another members short url', function () {
    $company = Company::create(['name' => 'Co', 'email' => 'co@example.com']);
    $member = User::factory()->create([
        'role' => UserRole::Member,
        'company_id' => $company->id,
    ]);
    $otherMember = User::factory()->create([
        'role' => UserRole::Member,
        'company_id' => $company->id,
    ]);

    $foreign = ShortUrl::create([
        'company_id' => $company->id,
        'user_id' => $otherMember->id,
        'original_url' => 'https://other.test',
        'code' => 'foreign',
    ]);

    $this->actingAs($member)
        ->delete(route('member.short-urls.destroy', $foreign))
        ->assertForbidden();
});

test('short url redirect works', function () {
    $admin = createCompanyAdmin();

    ShortUrl::create([
        'company_id' => $admin->company_id,
        'user_id' => $admin->id,
        'original_url' => 'https://destination.test/page',
        'code' => 'go',
    ]);

    $this->get(route('short-url.redirect', 'go'))
        ->assertRedirect('https://destination.test/page');
});

test('admin cannot delete short url from another company', function () {
    $admin = createCompanyAdmin();
    $otherCompany = Company::create(['name' => 'Other', 'email' => 'other@example.com']);
    $otherAdmin = User::factory()->create([
        'role' => UserRole::Admin,
        'company_id' => $otherCompany->id,
    ]);

    $foreign = ShortUrl::create([
        'company_id' => $otherCompany->id,
        'user_id' => $otherAdmin->id,
        'original_url' => 'https://other.test',
        'code' => 'foreign',
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.short-urls.destroy', $foreign))
        ->assertForbidden();
});
