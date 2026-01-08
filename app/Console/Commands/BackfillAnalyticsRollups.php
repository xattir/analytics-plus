<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\AnalyticsDailyPath;
use App\Models\AnalyticsDailyDimension;

class BackfillAnalyticsRollups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:backfill-rollups 
                            {--site-id= : Specific site ID to backfill (optional)}
                            {--days=30 : Number of days to backfill from today}
                            {--chunk=10000 : Chunk size for processing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill analytics rollup tables from raw sessions data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $siteId = $this->option('site-id');
        $days = (int) $this->option('days');
        $chunkSize = (int) $this->option('chunk');
        
        $this->info("Starting rollup backfill for last {$days} days...");
        
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays($days - 1);
        
        // Get sites to process
        $sitesQuery = DB::table('analytics_sites');
        if ($siteId) {
            $sitesQuery->where('id', $siteId);
        }
        $sites = $sitesQuery->get();
        
        if ($sites->isEmpty()) {
            $this->error('No sites found to process.');
            return 1;
        }
        
        $this->info("Processing " . $sites->count() . " site(s)...");
        
        foreach ($sites as $site) {
            $this->info("\nProcessing site: {$site->domain} (ID: {$site->id})");
            
            // Backfill paths
            $this->backfillPaths($site->id, $startDate, $endDate, $chunkSize);
            
            // Backfill dimensions
            $this->backfillDimensions($site->id, $startDate, $endDate, $chunkSize);
        }
        
        $this->info("\n✅ Rollup backfill completed!");
        return 0;
    }
    
    /**
     * Backfill analytics_daily_paths from analytics_session_paths
     */
    private function backfillPaths($siteId, $startDate, $endDate, $chunkSize)
    {
        $this->info("  Backfilling paths...");
        
        $currentDate = $startDate->copy();
        $totalProcessed = 0;
        
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $dateStart = $currentDate->copy()->startOfDay();
            $dateEnd = $currentDate->copy()->endOfDay();
            
            // Aggregate paths for this date
            // Only count paths from non-bot sessions
            $paths = DB::table('analytics_session_paths')
                ->join('analytics_sessions', function($join) use ($siteId, $dateStart, $dateEnd) {
                    $join->on('analytics_sessions.session_id', '=', 'analytics_session_paths.session_id')
                         ->where('analytics_sessions.site_id', '=', $siteId)
                         ->whereBetween('analytics_sessions.first_seen', [$dateStart, $dateEnd])
                         ->where('analytics_sessions.is_bot', '=', 0);
                })
                ->where('analytics_session_paths.site_id', $siteId)
                ->select('analytics_session_paths.path', DB::raw('COUNT(*) as views'))
                ->groupBy('analytics_session_paths.path')
                ->get();
            
            // Upsert into rollup table
            foreach ($paths as $path) {
                AnalyticsDailyPath::incrementPath($siteId, $dateStr, $path->path, $path->views);
            }
            
            $totalProcessed += $paths->count();
            $currentDate->addDay();
        }
        
        $this->info("    Processed {$totalProcessed} path entries");
    }
    
    /**
     * Backfill analytics_daily_dimensions from analytics_sessions
     */
    private function backfillDimensions($siteId, $startDate, $endDate, $chunkSize)
    {
        $this->info("  Backfilling dimensions...");
        
        $dimensions = [
            'country' => 'country',
            'browser' => 'browser',
            'os' => 'os',
            'device_type' => 'device_type',
            'entry_path' => 'entry_path',
            'exit_path' => 'exit_path',
            'referrer_source' => 'referrer_source',
        ];
        
        $currentDate = $startDate->copy();
        $totalProcessed = 0;
        
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $dateStart = $currentDate->copy()->startOfDay();
            $dateEnd = $currentDate->copy()->endOfDay();
            
            foreach ($dimensions as $dimensionType => $column) {
                // For entry_path and exit_path, we need special handling
                if ($dimensionType === 'entry_path') {
                    // Entry path: only count from first_seen (new sessions)
                    $values = DB::table('analytics_sessions')
                        ->where('site_id', $siteId)
                        ->whereBetween('first_seen', [$dateStart, $dateEnd])
                        ->where('is_bot', false)
                        ->whereNotNull($column)
                        ->select($column, DB::raw('COUNT(*) as count'))
                        ->groupBy($column)
                        ->get();
                } elseif ($dimensionType === 'exit_path') {
                    // Exit path: count from last_seen
                    $values = DB::table('analytics_sessions')
                        ->where('site_id', $siteId)
                        ->whereBetween('last_seen', [$dateStart, $dateEnd])
                        ->where('is_bot', false)
                        ->whereNotNull($column)
                        ->select($column, DB::raw('COUNT(*) as count'))
                        ->groupBy($column)
                        ->get();
                } else {
                    // Other dimensions: count from first_seen (new sessions only)
                    $values = DB::table('analytics_sessions')
                        ->where('site_id', $siteId)
                        ->whereBetween('first_seen', [$dateStart, $dateEnd])
                        ->where('is_bot', false)
                        ->whereNotNull($column)
                        ->select($column, DB::raw('COUNT(*) as count'))
                        ->groupBy($column)
                        ->get();
                }
                
                // Upsert into rollup table
                foreach ($values as $value) {
                    AnalyticsDailyDimension::incrementDimension(
                        $siteId,
                        $dateStr,
                        $dimensionType,
                        $value->$column,
                        $value->count
                    );
                }
                
                $totalProcessed += $values->count();
            }
            
            $currentDate->addDay();
        }
        
        $this->info("    Processed {$totalProcessed} dimension entries");
    }
}

