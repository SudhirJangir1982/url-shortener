<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Concerns\ProvidesCompanyTeamDataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreCompanyRequest;
use App\Http\Requests\SuperAdmin\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{
    use ProvidesCompanyTeamDataTables;

    public function index(): View
    {
        return view('super-admin.companies.index', [
            'dataUrl' => route('super-admin.companies.data'),
        ]);
    }

    public function data(): JsonResponse
    {
        $query = Company::query()
            ->select([
                'companies.id',
                'companies.name',
                'companies.email',
                'companies.created_at',
            ])
            ->withCount(['users', 'shortUrls']);

        return DataTables::eloquent($query)
            ->editColumn('name', function (Company $company) {
                $url = route('super-admin.companies.show', $company);

                return '<a href="'.e($url).'" class="font-medium text-indigo-600 hover:text-indigo-800">'.e($company->name).'</a>';
            })
            ->editColumn('users_count', fn (Company $company) => (int) ($company->users_count ?? 0))
            ->editColumn('short_urls_count', fn (Company $company) => (int) ($company->short_urls_count ?? 0))
            ->editColumn('created_at', fn (Company $company) => $company->created_at->format('M j, Y'))
            ->addColumn('actions', function (Company $company) {
                $show = route('super-admin.companies.show', $company);
                $edit = route('super-admin.companies.edit', $company);
                $delete = route('super-admin.companies.destroy', $company);

                return '<div class="flex flex-wrap gap-2">'
                    .'<a href="'.e($show).'" class="text-sm text-indigo-600 hover:text-indigo-800">'.e(__('View')).'</a>'
                    .'<a href="'.e($edit).'" class="text-sm text-gray-600 hover:text-gray-900">'.e(__('Edit')).'</a>'
                    .'<form method="POST" action="'.e($delete).'" class="inline" onsubmit="return confirm(\''.e(__('Delete this company?')).'\')">'
                    .csrf_field()
                    .method_field('DELETE')
                    .'<button type="submit" class="text-sm text-red-600 hover:text-red-800">'.e(__('Delete')).'</button>'
                    .'</form>'
                    .'</div>';
            })
            ->rawColumns(['name', 'actions'])
            ->orderColumn('users_count', 'users_count $1')
            ->orderColumn('short_urls_count', 'short_urls_count $1')
            ->toJson();
    }

    public function create(): View
    {
        return view('super-admin.companies.create');
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $company = Company::create($request->validated());

        return redirect()
            ->route('super-admin.companies.show', $company)
            ->with('status', __('Company created. Invite an admin or member below.'));
    }

    public function show(Company $company): View
    {
        return view('super-admin.companies.show', [
            'company' => $company,
            'invitationsDataUrl' => route('super-admin.companies.invitations.data', $company),
            'membersDataUrl' => route('super-admin.companies.members.data', $company),
        ]);
    }

    public function invitationsData(Company $company): JsonResponse
    {
        return $this->invitationsDataTable($company->id);
    }

    public function membersData(Company $company): JsonResponse
    {
        return $this->membersDataTable($company->id);
    }

    public function edit(Company $company): View
    {
        return view('super-admin.companies.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        $company->update($request->validated());

        return redirect()
            ->route('super-admin.companies.show', $company)
            ->with('status', __('Company updated successfully.'));
    }

    public function destroy(Company $company): RedirectResponse
    {
        if ($company->users()->exists()) {
            return redirect()
                ->route('super-admin.companies.show', $company)
                ->with('error', __('Cannot delete a company that still has users. Remove users first.'));
        }

        $company->delete();

        return redirect()
            ->route('super-admin.companies.index')
            ->with('status', __('Company deleted successfully.'));
    }
}
