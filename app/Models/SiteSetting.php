<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'applications_open',
        'uploads_enabled',
        'votes_enabled',
        'live_url',
    ];

    protected $casts = [
        'applications_open' => 'boolean',
        'uploads_enabled' => 'boolean',
        'votes_enabled' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('site_settings');
        });
    }
}
