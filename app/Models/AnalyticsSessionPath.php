<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsSessionPath extends Model
{
    protected $table = 'analytics_session_paths';
    
    protected $fillable = [
        'site_id',
        'session_id',
        'path',
        'position',
        'scroll_percent',
        'time_spent_ms',
    ];
    
    public function site()
    {
        return $this->belongsTo(AnalyticsSite::class, 'site_id');
    }
    
    public function session()
    {
        return $this->belongsTo(AnalyticsSession::class, 'session_id', 'session_id');
    }
}
