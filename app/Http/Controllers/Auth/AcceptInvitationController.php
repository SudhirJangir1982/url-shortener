<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AcceptInvitationController extends Controller
{
    public function create(string $token): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended(Auth::user()->homeRoute());
        }

        $invitation = $this->findPendingInvitation($token);

        if (! $invitation) {
            return redirect('/')->with('error', __('This invitation is invalid or has expired.'));
        }

        return view('auth.accept-invitation', [
            'invitation' => $invitation,
            'token' => $token,
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $invitation = $this->findPendingInvitation($token);

        if (! $invitation) {
            return redirect('/')->with('error', __('This invitation is invalid or has expired.'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($validated, $invitation) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $invitation->email,
                'password' => $validated['password'],
                'role' => $invitation->role,
                'company_id' => $invitation->company_id,
                'email_verified_at' => now(),
            ]);

            $invitation->update(['accepted_at' => now()]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended($user->homeRoute());
    }

    protected function findPendingInvitation(string $token): ?Invitation
    {
        $invitation = Invitation::query()
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();

        return $invitation;
    }
}
