<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShortUrlRequest;
use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShortUrlController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $shortUrls = ShortUrl::query()
            ->where('company_id', $user->company_id)
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('short-urls.index', [
            'shortUrls' => $shortUrls,
            'company' => $user->company,
            'canCreate' => true,
            'canDelete' => true,
            'showCreatedBy' => false,
            'createRoute' => route('member.short-urls.create'),
            'destroyRouteName' => 'member.short-urls.destroy',
            'listDescription' => __('Your short URLs.'),
        ]);
    }

    public function create(): View
    {
        return view('short-urls.create', [
            'company' => auth()->user()->company,
            'storeRoute' => route('member.short-urls.store'),
            'indexRoute' => route('member.short-urls.index'),
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
            ->route('member.short-urls.index')
            ->with('status', __('Short URL created successfully.'));
    }

    public function destroy(ShortUrl $shortUrl): RedirectResponse
    {
        $user = auth()->user();

        if ($shortUrl->company_id !== $user->company_id || $shortUrl->user_id !== $user->id) {
            abort(403);
        }

        $shortUrl->delete();

        return redirect()
            ->route('member.short-urls.index')
            ->with('status', __('Short URL deleted.'));
    }
}
