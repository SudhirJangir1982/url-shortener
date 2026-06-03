<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(string $role): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended(Auth::user()->homeRoute());
        }

        return view('auth.login', [
            'loginRole' => UserRole::fromSlug($role),
        ]);
    }

    public function store(LoginRequest $request, string $role): RedirectResponse
    {
        $expectedRole = UserRole::fromSlug($role);

        $request->authenticate($expectedRole);

        $request->session()->regenerate();

        return redirect()->intended($request->user()->homeRoute());
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
