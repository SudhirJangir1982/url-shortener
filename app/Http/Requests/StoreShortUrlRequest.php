<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreShortUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && $user->company_id !== null
            && ($user->isAdmin() || $user->isMember());
    }

    protected function prepareForValidation(): void
    {
        $originalUrl = trim((string) $this->input('original_url'));
        if ($originalUrl !== '' && ! preg_match('/^https?:\/\//i', $originalUrl)) {
            $originalUrl = 'https://'.$originalUrl;
        }

        $this->merge(['original_url' => $originalUrl]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'original_url' => ['required', 'string', 'max:2048', 'regex:/^https?:\/\/.+/i'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'original_url.regex' => __('Enter a full destination URL, e.g. https://example.com/page'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $url = $this->input('original_url');
            if (is_string($url) && $url !== '' && filter_var($url, FILTER_VALIDATE_URL) === false) {
                $validator->errors()->add(
                    'original_url',
                    __('Enter a valid destination URL, e.g. https://example.com/page')
                );
            }
        });
    }
}
