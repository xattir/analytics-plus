<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsDailyDimension extends Model
{
    protected $table = 'analytics_daily_dimensions';
    
    public $timestamps = false;
    
    protected $fillable = [
        'site_id',
        'date',
        'dimension_type',
        'dimension_value',
        'count',
    ];
    
    protected $casts = [
        'date' => 'date',
        'count' => 'integer',
    ];
    
    /**
     * Increment count for a dimension on a specific date
     * Uses INSERT ... ON DUPLICATE KEY UPDATE for atomic upsert
     */
    public static function incrementDimension($siteId, $date, $dimensionType, $dimensionValue, $increment = 1)
    {
        $dateStr = is_string($date) ? $date : $date->format('Y-m-d');
        
        // Truncate dimension_value to 255 chars (max length in schema)
        $dimensionValueForRollup = mb_substr($dimensionValue, 0, 255);
        
        try {
            \DB::statement("
                INSERT INTO analytics_daily_dimensions (site_id, date, dimension_type, dimension_value, count)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE count = count + ?
            ", [$siteId, $dateStr, $dimensionType, $dimensionValueForRollup, $increment, $increment]);
        } catch (\Exception $e) {
            // Log error but don't throw - rollup updates are non-critical
            \Log::warning('Failed to increment dimension in rollup table', [
                'site_id' => $siteId,
                'date' => $dateStr,
                'dimension_type' => $dimensionType,
                'dimension_value' => $dimensionValueForRollup,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Get top values for a dimension type in a date range
     * This replaces expensive GROUP BY queries on raw sessions
     */
    public static function getTopValues($siteId, $dateFrom, $dateTo, $dimensionType, $limit = 10)
    {
        $dateFromStr = is_string($dateFrom) ? $dateFrom : $dateFrom->format('Y-m-d');
        $dateToStr = is_string($dateTo) ? $dateTo : $dateTo->format('Y-m-d');
        
        return static::where('site_id', $siteId)
            ->where('dimension_type', $dimensionType)
            ->whereBetween('date', [$dateFromStr, $dateToStr])
            ->select('dimension_value', \DB::raw('SUM(count) as count'))
            ->groupBy('dimension_value')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }
}

