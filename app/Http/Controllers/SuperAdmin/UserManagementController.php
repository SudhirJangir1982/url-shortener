<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class UserManagementController extends Controller
{
    public function admins(): View
    {
        return $this->indexView(
            title: __('Admin Management'),
            roleLabel: UserRole::Admin->label(),
            tableId: 'admins-datatable',
            dataUrl: route('super-admin.admins.data'),
        );
    }

    public function adminsData(): JsonResponse
    {
        return $this->dataTableResponse(UserRole::Admin);
    }

    public function members(): View
    {
        return $this->indexView(
            title: __('Member Management'),
            roleLabel: UserRole::Member->label(),
            tableId: 'members-datatable',
            dataUrl: route('super-admin.members.data'),
        );
    }

    public function membersData(): JsonResponse
    {
        return $this->dataTableResponse(UserRole::Member);
    }

    protected function indexView(string $title, string $roleLabel, string $tableId, string $dataUrl): View
    {
        return view('super-admin.users.index', compact('title', 'roleLabel', 'tableId', 'dataUrl'));
    }

    protected function dataTableResponse(UserRole $role): JsonResponse
    {
        $query = User::query()
            ->where('users.role', $role)
            ->leftJoin('companies', 'users.company_id', '=', 'companies.id')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.role',
                'users.created_at',
                'companies.name as company_name',
            ]);

        return DataTables::eloquent($query)
            ->editColumn('company_name', fn (User $user) => $user->company_name ?? '—')
            ->editColumn('role', fn (User $user) => $user->role->label())
            ->editColumn('created_at', fn (User $user) => $user->created_at->format('M j, Y'))
            ->filterColumn('company_name', function ($query, $keyword) {
                $query->where('companies.name', 'like', '%'.addcslashes($keyword, '%_\\').'%');
            })
            ->orderColumn('company_name', 'companies.name $1')
            ->toJson();
    }
}
