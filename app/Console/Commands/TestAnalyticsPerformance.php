<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AnalyticsSession;
use App\Models\AnalyticsSessionPath;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestAnalyticsPerformance extends Command
{
    protected $signature = 'analytics:test-performance {site_id?}';
    protected $description = 'Test analytics queries performance on server data';

    public function handle()
    {
        $siteId = $this->argument('site_id') ?? 1;
        
        $this->info("Testing Analytics Performance for Site ID: {$siteId}");
        $this->info("================================================");
        
        // Test 1: Count sessions
        $this->info("\n1. Testing AnalyticsSession::count()...");
        $start = microtime(true);
        $sessionCount = AnalyticsSession::where('site_id', $siteId)->count();
        $time = (microtime(true) - $start) * 1000;
        $this->info("   Result: {$sessionCount} sessions in " . number_format($time, 2) . "ms");
        
        // Test 2: Count paths
        $this->info("\n2. Testing AnalyticsSessionPath::count()...");
        $start = microtime(true);
        $pathCount = AnalyticsSessionPath::where('site_id', $siteId)->count();
        $time = (microtime(true) - $start) * 1000;
        $this->info("   Result: {$pathCount} paths in " . number_format($time, 2) . "ms");
        
        // Test 3: Recent sessions (last 30 minutes)
        $this->info("\n3. Testing recent sessions (last 30 minutes)...");
        $startTime = Carbon::now()->subMinutes(30);
        $start = microtime(true);
        $recentSessions = AnalyticsSession::where('site_id', $siteId)
            ->where('last_seen', '>=', $startTime)
            ->where('is_bot', false)
            ->count();
        $time = (microtime(true) - $start) * 1000;
        $this->info("   Result: {$recentSessions} active sessions in " . number_format($time, 2) . "ms");
        
        // Test 4: Top pages last 30 minutes
        $this->info("\n4. Testing top pages (last 30 minutes)...");
        $start = microtime(true);
        $topPages = DB::table('analytics_session_paths')
            ->join('analytics_sessions', function($join) use ($siteId, $startTime) {
                $join->on('analytics_sessions.session_id', '=', 'analytics_session_paths.session_id')
                     ->where('analytics_sessions.site_id', '=', $siteId)
                     ->where('analytics_sessions.last_seen', '>=', $startTime->toDateTimeString())
                     ->where('analytics_sessions.is_bot', '=', 0);
            })
            ->where('analytics_session_paths.site_id', $siteId)
            ->select('analytics_session_paths.path', DB::raw('COUNT(*) as views'))
            ->groupBy('analytics_session_paths.path')
            ->orderByDesc('views')
            ->limit(10)
            ->get();
        $time = (microtime(true) - $start) * 1000;
        $this->info("   Result: " . $topPages->count() . " pages in " . number_format($time, 2) . "ms");
        foreach ($topPages->take(5) as $page) {
            $this->info("   - {$page->path}: {$page->views} views");
        }
        
        // Test 5: Check recent data
        $this->info("\n5. Checking recent data (last hour)...");
        $hourAgo = Carbon::now()->subHour();
        $recentSessionsCount = AnalyticsSession::where('site_id', $siteId)
            ->where('created_at', '>=', $hourAgo)
            ->count();
        $recentPathsCount = AnalyticsSessionPath::where('site_id', $siteId)
            ->where('created_at', '>=', $hourAgo)
            ->count();
        $this->info("   Sessions created in last hour: {$recentSessionsCount}");
        $this->info("   Paths created in last hour: {$recentPathsCount}");
        
        // Test 6: Check database directly
        $this->info("\n6. Checking database directly...");
        $start = microtime(true);
        $dbSessionCount = DB::table('analytics_sessions')
            ->where('site_id', $siteId)
            ->count();
        $dbPathCount = DB::table('analytics_session_paths')
            ->where('site_id', $siteId)
            ->count();
        $time = (microtime(true) - $start) * 1000;
        $this->info("   DB Sessions: {$dbSessionCount}, DB Paths: {$dbPathCount} in " . number_format($time, 2) . "ms");
        
        // Test 7: Check last insert
        $this->info("\n7. Checking last inserts...");
        $lastSession = DB::table('analytics_sessions')
            ->where('site_id', $siteId)
            ->orderBy('created_at', 'desc')
            ->first();
        $lastPath = DB::table('analytics_session_paths')
            ->where('site_id', $siteId)
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($lastSession) {
            $this->info("   Last session: " . $lastSession->created_at . " (session_id: " . substr($lastSession->session_id, 0, 8) . "...)");
        } else {
            $this->warn("   No sessions found!");
        }
        
        if ($lastPath) {
            $this->info("   Last path: " . $lastPath->created_at . " (path: " . substr($lastPath->path, 0, 50) . "...)");
        } else {
            $this->warn("   No paths found!");
        }
        
        $this->info("\n================================================");
        $this->info("Performance test completed!");
    }
}

