<?php

use App\Http\Controllers\Admin\TeamController as AdminTeamController;
use App\Http\Controllers\Admin\ShortUrlController as AdminShortUrlController;
use App\Http\Controllers\Member\ShortUrlController as MemberShortUrlController;
use App\Http\Controllers\Auth\AcceptInvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShortUrlRedirectController;
use App\Http\Controllers\SuperAdmin\CompanyController;
use App\Http\Controllers\SuperAdmin\CompanyInvitationController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\ShortUrlController as SuperAdminShortUrlController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return view('welcome');
    }

    return redirect(auth()->user()->homeRoute());
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/s/{code}', ShortUrlRedirectController::class)->name('short-url.redirect');

/* Guest Routes */
Route::middleware('guest')->group(function () {
    Route::get('invitation/{token}', [AcceptInvitationController::class, 'create'])->name('invitation.accept');
    Route::post('invitation/{token}', [AcceptInvitationController::class, 'store'])->name('invitation.accept.store');
});

/* Authenticated Routes */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    /* Company Admin Routes */
    Route::middleware(['verified', 'company_admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/short-urls', [AdminShortUrlController::class, 'index'])->name('short-urls.index');
            Route::get('/short-urls/create', [AdminShortUrlController::class, 'create'])->name('short-urls.create');
            Route::post('/short-urls', [AdminShortUrlController::class, 'store'])->name('short-urls.store');
            Route::delete('/short-urls/{shortUrl}', [AdminShortUrlController::class, 'destroy'])->name('short-urls.destroy');
            Route::get('/team', [AdminTeamController::class, 'index'])->name('team.index');
            Route::get('/team/invitations/data', [AdminTeamController::class, 'invitationsData'])->name('team.invitations.data');
            Route::get('/team/members/data', [AdminTeamController::class, 'membersData'])->name('team.members.data');
            Route::post('/team/invitations', [AdminTeamController::class, 'storeInvitation'])->name('team.invitations.store');
        });



    /* Company Member Routes */
    Route::middleware(['verified', 'company_member'])
        ->prefix('member')
        ->name('member.')
        ->group(function () {
            Route::get('/short-urls', [MemberShortUrlController::class, 'index'])->name('short-urls.index');
            Route::get('/short-urls/create', [MemberShortUrlController::class, 'create'])->name('short-urls.create');
            Route::post('/short-urls', [MemberShortUrlController::class, 'store'])->name('short-urls.store');
            Route::delete('/short-urls/{shortUrl}', [MemberShortUrlController::class, 'destroy'])->name('short-urls.destroy');
        });

        /* Super Admin Routes */
    Route::middleware('super_admin')
        ->prefix('super-admin')
        ->name('super-admin.')
        ->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
            Route::get('/companies/data', [CompanyController::class, 'data'])->name('companies.data');
            Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
            Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
            Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
            Route::get('/companies/{company}/invitations/data', [CompanyController::class, 'invitationsData'])->name('companies.invitations.data');
            Route::get('/companies/{company}/members/data', [CompanyController::class, 'membersData'])->name('companies.members.data');
            Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
            Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
            Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
            Route::post('/companies/{company}/invitations', [CompanyInvitationController::class, 'store'])->name('companies.invitations.store');
            Route::get('/admins', [UserManagementController::class, 'admins'])->name('admins.index');
            Route::get('/admins/data', [UserManagementController::class, 'adminsData'])->name('admins.data');
            Route::get('/members', [UserManagementController::class, 'members'])->name('members.index');
            Route::get('/members/data', [UserManagementController::class, 'membersData'])->name('members.data');
            Route::get('/short-urls', [SuperAdminShortUrlController::class, 'index'])->name('short-urls.index');
            Route::get('/short-urls/data', [SuperAdminShortUrlController::class, 'data'])->name('short-urls.data');
        });
});

require __DIR__.'/auth.php';
