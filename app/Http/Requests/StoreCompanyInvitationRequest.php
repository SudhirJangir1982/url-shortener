<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return $this->route('company') instanceof Company;
        }

        return $user->isAdmin() && $user->company_id !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $companyId = $this->companyId();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('invitations', 'email')->where(
                    fn ($query) => $query->where('company_id', $companyId)->whereNull('accepted_at')
                ),
            ],
            'role' => ['required', Rule::in([UserRole::Admin->value, UserRole::Member->value])],
        ];
    }

    protected function companyId(): int
    {
        if ($this->user()?->isSuperAdmin()) {
            return $this->route('company')->id;
        }

        return (int) $this->user()->company_id;
    }
}
