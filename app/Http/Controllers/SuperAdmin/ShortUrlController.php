<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ShortUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ShortUrlController extends Controller
{
    public function index(): View
    {
        return view('super-admin.short-urls.index', [
            'dataUrl' => route('super-admin.short-urls.data'),
        ]);
    }

    public function data(): JsonResponse
    {
        $query = ShortUrl::query()
            ->leftJoin('companies', 'short_urls.company_id', '=', 'companies.id')
            ->leftJoin('users', 'short_urls.user_id', '=', 'users.id')
            ->select([
                'short_urls.id',
                'short_urls.code',
                'short_urls.title',
                'short_urls.original_url',
                'short_urls.created_at',
                'companies.name as company_name',
                'users.name as user_name',
            ]);

        return DataTables::eloquent($query)
            ->addColumn('short_link', function (ShortUrl $shortUrl) {
                $link = url('/s/'.$shortUrl->code);

                return '<a href="'.e($link).'" target="_blank" rel="noopener" class="font-medium text-indigo-600 hover:text-indigo-800">'.e($link).'</a>';
            })
            ->editColumn('original_url', function (ShortUrl $shortUrl) {
                $url = $shortUrl->original_url;
                $label = strlen($url) > 50 ? substr($url, 0, 50).'…' : $url;

                return '<a href="'.e($url).'" target="_blank" rel="noopener" class="text-gray-700 hover:text-gray-900" title="'.e($url).'">'.e($label).'</a>';
            })
            ->editColumn('title', fn (ShortUrl $shortUrl) => e($shortUrl->title ?? '—'))
            ->editColumn('company_name', fn (ShortUrl $shortUrl) => e($shortUrl->company_name ?? '—'))
            ->editColumn('user_name', fn (ShortUrl $shortUrl) => e($shortUrl->user_name ?? '—'))
            ->editColumn('created_at', fn (ShortUrl $shortUrl) => $shortUrl->created_at->format('M j, Y'))
            ->filterColumn('company_name', function ($query, $keyword) {
                $query->where('companies.name', 'like', '%'.addcslashes($keyword, '%_\\').'%');
            })
            ->filterColumn('user_name', function ($query, $keyword) {
                $query->where('users.name', 'like', '%'.addcslashes($keyword, '%_\\').'%');
            })
            ->filterColumn('short_link', function ($query, $keyword) {
                $query->where('short_urls.code', 'like', '%'.addcslashes($keyword, '%_\\').'%');
            })
            ->orderColumn('company_name', 'companies.name $1')
            ->orderColumn('user_name', 'users.name $1')
            ->orderColumn('short_link', 'short_urls.code $1')
            ->rawColumns(['short_link', 'original_url'])
            ->toJson();
    }
}
