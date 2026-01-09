<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementClick extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'advertisement_id',
        'site_id',
        'session_id',
        'device_type',
        'country_code',
        'url_pattern_id',
        'selector',
        'ip',
        'user_agent',
        'url',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }

    public function site()
    {
        return $this->belongsTo(AnalyticsSite::class, 'site_id');
    }

    public function urlPattern()
    {
        return $this->belongsTo(AnalyticsUrlPattern::class, 'url_pattern_id');
    }
}

