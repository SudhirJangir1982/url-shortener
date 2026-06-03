<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ShortUrl extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'title',
        'original_url',
        'code',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shortLink(): string
    {
        return url('/s/'.$this->code);
    }

    public static function generateUniqueCode(int $length = 6): string
    {
        do {
            $code = Str::lower(Str::random($length));
        } while (static::query()->where('code', $code)->exists());

        return $code;
    }
}
