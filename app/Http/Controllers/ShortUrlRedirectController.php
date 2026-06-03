<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;

class ShortUrlRedirectController extends Controller
{
    public function __invoke(string $code): RedirectResponse
    {
        $shortUrl = ShortUrl::query()->where('code', $code)->firstOrFail();

        return redirect()->away($shortUrl->original_url);
    }
}
