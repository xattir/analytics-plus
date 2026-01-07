<?php

namespace App\Jobs;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\AnalyticsSite;

class UpdateAnalyticsSummaryTables
{
    protected $date;
    protected $siteId;

    /**
     * Create a new job instance.
     */
    public function __construct($date = null, $siteId = null)
    {
        $this->date = $date ? Carbon::parse($date) : Carbon::yesterday();
        $this->siteId = $siteId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $sites = $this->siteId 
            ? [AnalyticsSite::find($this->siteId)] 
            : AnalyticsSite::all();

        foreach ($sites as $site) {
            $this->updateDailySummary($site->id, $this->date);
            $this->updateTopPages($site->id, $this->date);
            $this->updateEntryExit($site->id, $this->date);
            $this->updateDimensions($site->id, $this->date);
        }
    }

    /**
     * Update daily summary statistics
     */
    private function updateDailySummary($siteId, $date)
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        // Get all statistics in optimized queries
        $stats = DB::table('analytics_sessions')
            ->where('site_id', $siteId)
            ->whereBetween('first_seen', [$startOfDay, $endOfDay])
            ->selectRaw('
                COUNT(*) as total_sessions,
                SUM(pages_count) as total_pageviews,
                AVG(duration_ms) as avg_duration,
                AVG(pages_count) as avg_pages_per_session,
                SUM(CASE WHEN is_bounce = 1 THEN 1 ELSE 0 END) as bounce_count,
                SUM(CASE WHEN is_returning = 0 THEN 1 ELSE 0 END) as new_visitors,
                SUM(CASE WHEN is_returning = 1 THEN 1 ELSE 0 END) as returning_visitors,
                SUM(CASE WHEN is_bot = 1 THEN 1 ELSE 0 END) as bot_sessions,
                SUM(CASE WHEN is_bot = 0 THEN 1 ELSE 0 END) as real_sessions,
                SUM(CASE WHEN is_bot = 0 AND pages_count > 1 AND duration_ms > 30000 AND max_scroll_percent > 50 THEN 1 ELSE 0 END) as high_quality,
                SUM(CASE WHEN is_bot = 0 AND (pages_count = 1 OR duration_ms < 5000 OR max_scroll_percent < 10) THEN 1 ELSE 0 END) as low_quality
            ')
            ->first();

        // Get unique visitors separately
        $uniqueVisitors = DB::table('analytics_sessions')
            ->where('site_id', $siteId)
            ->whereBetween('first_seen', [$startOfDay, $endOfDay])
            ->distinct('device_fingerprint')
            ->count('device_fingerprint');

        // Insert or update summary
        DB::table('analytics_daily_summary')->updateOrInsert(
            ['site_id' => $siteId, 'date' => $date->format('Y-m-d')],
            [
                'total_sessions' => $stats->total_sessions ?? 0,
                'unique_visitors' => $uniqueVisitors,
                'total_pageviews' => $stats->total_pageviews ?? 0,
                'avg_duration' => round($stats->avg_duration ?? 0, 2),
                'avg_pages_per_session' => round($stats->avg_pages_per_session ?? 0, 2),
                'bounce_count' => $stats->bounce_count ?? 0,
                'new_visitors' => $stats->new_visitors ?? 0,
                'returning_visitors' => $stats->returning_visitors ?? 0,
                'bot_sessions' => $stats->bot_sessions ?? 0,
                'real_sessions' => $stats->real_sessions ?? 0,
                'high_quality' => $stats->high_quality ?? 0,
                'low_quality' => $stats->low_quality ?? 0,
            ]
        );
    }

    /**
     * Update top pages summary
     */
    private function updateTopPages($siteId, $date)
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        // Get top pages for the day
        $topPages = DB::table('analytics_session_paths')
            ->join('analytics_sessions', function($join) use ($siteId, $startOfDay, $endOfDay) {
                $join->on('analytics_session_paths.session_id', '=', 'analytics_sessions.session_id')
                     ->where('analytics_sessions.site_id', '=', $siteId)
                     ->whereBetween('analytics_sessions.first_seen', [$startOfDay, $endOfDay])
                     ->where('analytics_sessions.is_bot', '=', false);
            })
            ->where('analytics_session_paths.site_id', $siteId)
            ->select('analytics_session_paths.path', DB::raw('COUNT(*) as views'))
            ->groupBy('analytics_session_paths.path')
            ->get();

        // Delete existing records for this date
        DB::table('analytics_top_pages_daily')
            ->where('site_id', $siteId)
            ->where('date', $date->format('Y-m-d'))
            ->delete();

        // Insert new records
        foreach ($topPages as $page) {
            DB::table('analytics_top_pages_daily')->insert([
                'site_id' => $siteId,
                'date' => $date->format('Y-m-d'),
                'path' => substr($page->path, 0, 500), // Limit to 500 chars
                'views' => $page->views,
            ]);
        }
    }

    /**
     * Update entry/exit pages summary
     */
    private function updateEntryExit($siteId, $date)
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        // Get top entry pages
        $entryPages = DB::table('analytics_sessions')
            ->where('site_id', $siteId)
            ->whereBetween('first_seen', [$startOfDay, $endOfDay])
            ->select('entry_path', DB::raw('COUNT(*) as entries'))
            ->groupBy('entry_path')
            ->get();

        // Get top exit pages
        $exitPages = DB::table('analytics_sessions')
            ->where('site_id', $siteId)
            ->whereBetween('last_seen', [$startOfDay, $endOfDay])
            ->select('exit_path', DB::raw('COUNT(*) as exits'))
            ->groupBy('exit_path')
            ->get();

        // Delete existing records
        DB::table('analytics_entry_exit_daily')
            ->where('site_id', $siteId)
            ->where('date', $date->format('Y-m-d'))
            ->delete();

        // Insert entry pages
        foreach ($entryPages as $page) {
            DB::table('analytics_entry_exit_daily')->insert([
                'site_id' => $siteId,
                'date' => $date->format('Y-m-d'),
                'entry_path' => $page->entry_path ? substr($page->entry_path, 0, 500) : null,
                'entry_count' => $page->entries,
            ]);
        }

        // Insert exit pages
        foreach ($exitPages as $page) {
            DB::table('analytics_entry_exit_daily')->insert([
                'site_id' => $siteId,
                'date' => $date->format('Y-m-d'),
                'exit_path' => $page->exit_path ? substr($page->exit_path, 0, 500) : null,
                'exit_count' => $page->exits,
            ]);
        }
    }

    /**
     * Update dimensions summary (browser, device, os, country)
     */
    private function updateDimensions($siteId, $date)
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        // Delete existing records
        DB::table('analytics_dimensions_daily')
            ->where('site_id', $siteId)
            ->where('date', $date->format('Y-m-d'))
            ->delete();

        // Browsers
        $browsers = DB::table('analytics_sessions')
            ->where('site_id', $siteId)
            ->whereBetween('first_seen', [$startOfDay, $endOfDay])
            ->where('is_bot', false)
            ->whereNotNull('browser')
            ->select('browser', DB::raw('COUNT(*) as count'))
            ->groupBy('browser')
            ->get();

        foreach ($browsers as $browser) {
            DB::table('analytics_dimensions_daily')->insert([
                'site_id' => $siteId,
                'date' => $date->format('Y-m-d'),
                'dimension_type' => 'browser',
                'dimension_value' => $browser->browser,
                'count' => $browser->count,
            ]);
        }

        // Device types
        $devices = DB::table('analytics_sessions')
            ->where('site_id', $siteId)
            ->whereBetween('first_seen', [$startOfDay, $endOfDay])
            ->whereNotNull('device_type')
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->get();

        foreach ($devices as $device) {
            DB::table('analytics_dimensions_daily')->insert([
                'site_id' => $siteId,
                'date' => $date->format('Y-m-d'),
                'dimension_type' => 'device_type',
                'dimension_value' => $device->device_type,
                'count' => $device->count,
            ]);
        }

        // OS
        $os = DB::table('analytics_sessions')
            ->where('site_id', $siteId)
            ->whereBetween('first_seen', [$startOfDay, $endOfDay])
            ->whereNotNull('os')
            ->select('os', DB::raw('COUNT(*) as count'))
            ->groupBy('os')
            ->get();

        foreach ($os as $osItem) {
            DB::table('analytics_dimensions_daily')->insert([
                'site_id' => $siteId,
                'date' => $date->format('Y-m-d'),
                'dimension_type' => 'os',
                'dimension_value' => $osItem->os,
                'count' => $osItem->count,
            ]);
        }

        // Countries
        $countries = DB::table('analytics_sessions')
            ->where('site_id', $siteId)
            ->whereBetween('first_seen', [$startOfDay, $endOfDay])
            ->where('is_bot', false)
            ->whereNotNull('country')
            ->select('country', DB::raw('COUNT(*) as count'))
            ->groupBy('country')
            ->get();

        foreach ($countries as $country) {
            DB::table('analytics_dimensions_daily')->insert([
                'site_id' => $siteId,
                'date' => $date->format('Y-m-d'),
                'dimension_type' => 'country',
                'dimension_value' => $country->country,
                'count' => $country->count,
            ]);
        }
    }
}
