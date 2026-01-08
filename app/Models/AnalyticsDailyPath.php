<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsDailyPath extends Model
{
    protected $table = 'analytics_daily_paths';
    
    public $timestamps = false;
    
    protected $fillable = [
        'site_id',
        'date',
        'path',
        'views',
    ];
    
    protected $casts = [
        'date' => 'date',
        'views' => 'integer',
    ];
    
    /**
     * Increment views for a path on a specific date
     * Uses INSERT ... ON DUPLICATE KEY UPDATE for atomic upsert
     */
    public static function incrementPath($siteId, $date, $path, $increment = 1)
    {
        $dateStr = is_string($date) ? $date : $date->format('Y-m-d');
        
        \DB::statement("
            INSERT INTO analytics_daily_paths (site_id, date, path, views)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE views = views + ?
        ", [$siteId, $dateStr, $path, $increment, $increment]);
    }
    
    /**
     * Get top paths for a date range
     * This replaces the expensive JOIN + GROUP BY query
     */
    public static function getTopPaths($siteId, $dateFrom, $dateTo, $limit = 30)
    {
        $dateFromStr = is_string($dateFrom) ? $dateFrom : $dateFrom->format('Y-m-d');
        $dateToStr = is_string($dateTo) ? $dateTo : $dateTo->format('Y-m-d');
        
        return static::where('site_id', $siteId)
            ->whereBetween('date', [$dateFromStr, $dateToStr])
            ->select('path', \DB::raw('SUM(views) as views'))
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit($limit)
            ->get();
    }
}

