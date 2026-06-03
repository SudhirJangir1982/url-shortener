<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShortUrlRequest;
use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShortUrlController extends Controller
{
    public function index(): View
    {
        $companyId = auth()->user()->company_id;

        $shortUrls = ShortUrl::query()
            ->where('company_id', $companyId)
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->get();

        $company = auth()->user()->company;

        return view('short-urls.index', [
            'shortUrls' => $shortUrls,
            'company' => $company,
            'canCreate' => true,
            'canDelete' => true,
            'showCreatedBy' => true,
            'createRoute' => route('admin.short-urls.create'),
            'destroyRouteName' => 'admin.short-urls.destroy',
            'listDescription' => $company
                ? __('All short URLs for :company.', ['company' => $company->name])
                : __('All short URLs for your company.'),
        ]);
    }

    public function create(): View
    {
        return view('short-urls.create', [
            'company' => auth()->user()->company,
            'storeRoute' => route('admin.short-urls.store'),
            'indexRoute' => route('admin.short-urls.index'),
        ]);
    }

    public function store(StoreShortUrlRequest $request): RedirectResponse
    {
        $user = $request->user();

        ShortUrl::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'title' => $request->validated('title'),
            'original_url' => $request->validated('original_url'),
            'code' => ShortUrl::generateUniqueCode(),
        ]);

        return redirect()
            ->route('admin.short-urls.index')
            ->with('status', __('Short URL created successfully.'));
    }

    public function destroy(ShortUrl $shortUrl): RedirectResponse
    {
        if ($shortUrl->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $shortUrl->delete();

        return redirect()
            ->route('admin.short-urls.index')
            ->with('status', __('Short URL deleted.'));
    }
}
