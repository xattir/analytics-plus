<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsSession extends Model
{
    protected $table = 'analytics_sessions';
    
    public $timestamps = false;
    
    protected $fillable = [
        'site_id',
        'session_id',
        'first_seen',
        'last_seen',
        'duration_ms',
        'entry_path',
        'exit_path',
        'pages_count',
        'max_scroll_percent',
        'active_time_ms',
        'idle_time_ms',
        'user_agent',
        'device_fingerprint',
        'device_type',
        'os',
        'os_version',
        'browser',
        'browser_version',
        'browser_engine',
        'screen_width',
        'screen_height',
        'viewport_width',
        'viewport_height',
        'device_pixel_ratio',
        'network_type',
        'rtt_ms',
        'downlink_mbps',
        'country',
        'city',
        'isp',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'referrer',
        'referrer_source',
        'referrer_domain',
        'is_returning',
        'is_bounce',
        'is_bot',
        'is_high_quality',
        'is_low_quality',
        'ip',
    ];
    
    protected $casts = [
        'is_returning' => 'boolean',
        'is_bounce' => 'boolean',
        'is_bot' => 'boolean',
        'is_high_quality' => 'boolean',
        'is_low_quality' => 'boolean',
        'device_pixel_ratio' => 'decimal:2',
        'downlink_mbps' => 'decimal:2',
        'first_seen' => 'datetime',
        'last_seen' => 'datetime',
        'first_seen_date' => 'date',
        'last_seen_date' => 'date',
    ];
    
    public function site()
    {
        return $this->belongsTo(AnalyticsSite::class, 'site_id');
    }
    
    public function paths()
    {
        return $this->hasMany(AnalyticsSessionPath::class, 'session_id', 'session_id');
    }
}
