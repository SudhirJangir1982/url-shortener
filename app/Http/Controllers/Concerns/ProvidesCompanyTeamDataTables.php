<?php

namespace App\Http\Controllers\Concerns;

use App\Enums\UserRole;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

trait ProvidesCompanyTeamDataTables
{
    protected function invitationsDataTable(int $companyId): JsonResponse
    {
        $query = Invitation::query()
            ->where('invitations.company_id', $companyId)
            ->whereNull('invitations.accepted_at')
            ->where('invitations.expires_at', '>', now())
            ->select([
                'invitations.id',
                'invitations.token',
                'invitations.name',
                'invitations.email',
                'invitations.role',
                'invitations.expires_at',
            ]);

        return DataTables::eloquent($query)
            ->editColumn('role', fn (Invitation $invitation) => $invitation->role->label())
            ->editColumn('expires_at', fn (Invitation $invitation) => $invitation->expires_at->format('M j, Y'))
            ->addColumn('actions', function (Invitation $invitation) {
                $url = route('invitation.accept', $invitation->token);

                return '<button type="button" class="copy-invitation-link inline-flex items-center justify-center rounded-md border border-indigo-300 bg-white px-3 py-1.5 text-xs font-medium text-indigo-800 hover:bg-indigo-50" data-copy-url="'.e($url).'">'
                    .e(__('Copy link'))
                    .'</button>';
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    protected function membersDataTable(int $companyId): JsonResponse
    {
        $query = User::query()
            ->where('users.company_id', $companyId)
            ->whereIn('users.role', [UserRole::Admin, UserRole::Member])
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.role',
                'users.created_at',
            ]);

        return DataTables::eloquent($query)
            ->editColumn('role', fn (User $user) => $user->role->label())
            ->editColumn('created_at', fn (User $user) => $user->created_at->format('M j, Y'))
            ->toJson();
    }
}
