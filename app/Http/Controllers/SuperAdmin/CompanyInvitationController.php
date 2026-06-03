<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyInvitationRequest;
use App\Models\Company;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;

class CompanyInvitationController extends Controller
{
    public function store(StoreCompanyInvitationRequest $request, Company $company): RedirectResponse
    {
        $invitation = Invitation::create([
            'company_id' => $company->id,
            'invited_by' => $request->user()->id,
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'role' => UserRole::from($request->validated('role')),
            'token' => Invitation::generateToken(),
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()
            ->route('super-admin.companies.show', $company)
            ->with('status', __('Invitation created. Share the link below with :email.', ['email' => $invitation->email]))
            ->with('invitation_link', route('invitation.accept', $invitation->token));
    }
}
