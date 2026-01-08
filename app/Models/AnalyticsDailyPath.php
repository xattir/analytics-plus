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
     * Note: Path is truncated to 191 chars to match unique index prefix
     */
    public static function incrementPath($siteId, $date, $path, $increment = 1)
    {
        $dateStr = is_string($date) ? $date : $date->format('Y-m-d');
        
        // Truncate path to 191 chars to match unique index prefix (path(191))
        // This ensures the unique constraint works correctly
        $pathForRollup = mb_substr($path, 0, 191);
        
        try {
            \DB::statement("
                INSERT INTO analytics_daily_paths (site_id, date, path, views)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE views = views + ?
            ", [$siteId, $dateStr, $pathForRollup, $increment, $increment]);
        } catch (\Exception $e) {
            // Log error but don't throw - rollup updates are non-critical
            \Log::warning('Failed to increment path in rollup table', [
                'site_id' => $siteId,
                'date' => $dateStr,
                'path' => $pathForRollup,
                'error' => $e->getMessage(),
            ]);
        }
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

