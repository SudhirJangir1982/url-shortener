<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(string $role): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended(Auth::user()->homeRoute());
        }

        $registerRole = UserRole::fromRegisterSlug($role);

        return view('auth.register', [
            'registerRole' => $registerRole,
            'companies' => $registerRole === UserRole::Member
                ? Company::query()->orderBy('name')->get(['id', 'name'])
                : collect(),
        ]);
    }

    public function store(Request $request, string $role): RedirectResponse
    {
        $registerRole = UserRole::fromRegisterSlug($role);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($registerRole === UserRole::Admin) {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['company_email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:companies,email'];
        }

        if ($registerRole === UserRole::Member) {
            $rules['company_id'] = ['required', 'exists:companies,id'];
        }

        $validated = $request->validate($rules);

        $user = DB::transaction(function () use ($validated, $registerRole) {
            $companyId = null;

            if ($registerRole === UserRole::Admin) {
                $company = Company::create([
                    'name' => $validated['company_name'],
                    'email' => $validated['company_email'],
                ]);
                $companyId = $company->id;
            }

            if ($registerRole === UserRole::Member) {
                $companyId = $validated['company_id'];
            }

            return User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => $registerRole,
                'company_id' => $companyId,
                'email_verified_at' => now(),
            ]);
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended($user->homeRoute());
    }
}
