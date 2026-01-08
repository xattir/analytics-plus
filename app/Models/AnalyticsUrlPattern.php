<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsUrlPattern extends Model
{
    protected $table = 'analytics_url_patterns';
    
    protected $fillable = [
        'site_id',
        'domain',
        'pattern',
        'generated_at',
    ];
    
    protected $casts = [
        'generated_at' => 'datetime',
    ];
    
    /**
     * Get the site this pattern belongs to
     */
    public function site()
    {
        return $this->belongsTo(AnalyticsSite::class, 'site_id');
    }
}
