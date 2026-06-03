<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Concerns\ProvidesCompanyTeamDataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyInvitationRequest;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeamController extends Controller
{
    use ProvidesCompanyTeamDataTables;

    public function index(): View
    {
        $company = auth()->user()->company;

        abort_if($company === null, 403);

        return view('admin.team.index', [
            'company' => $company,
            'invitationsDataUrl' => route('admin.team.invitations.data'),
            'membersDataUrl' => route('admin.team.members.data'),
        ]);
    }

    public function invitationsData(): JsonResponse
    {
        $companyId = auth()->user()->company_id;

        abort_if($companyId === null, 403);

        return $this->invitationsDataTable($companyId);
    }

    public function membersData(): JsonResponse
    {
        $companyId = auth()->user()->company_id;

        abort_if($companyId === null, 403);

        return $this->membersDataTable($companyId);
    }

    public function storeInvitation(StoreCompanyInvitationRequest $request): RedirectResponse
    {
        $company = $request->user()->company;

        abort_if($company === null, 403);

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
            ->route('admin.team.index')
            ->with('status', __('Invitation created. Share the link below with :email.', ['email' => $invitation->email]))
            ->with('invitation_link', route('invitation.accept', $invitation->token));
    }
}
