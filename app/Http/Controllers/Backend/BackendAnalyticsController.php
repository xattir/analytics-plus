<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsSite;
use App\Models\AnalyticsSession;
use App\Models\AnalyticsSessionPath;
use App\Models\AnalyticsSiteInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BackendAnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Check if user is superadmin
     */
    private function isSuperAdmin()
    {
        return auth()->user()->hasRole('superadmin');
    }
    
    /**
     * Check if user is admin (admin or superadmin)
     */
    private function isAdmin()
    {
        return auth()->user()->hasRole('admin') || $this->isSuperAdmin();
    }

    /**
     * List all analytics sites
     * - Superadmin: sees all sites
     * - Admin: sees only their own sites and sites they're members of
     * - Regular users: sees only their own sites and sites they're members of
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $isSuperAdmin = $this->isSuperAdmin();
        
        if ($isSuperAdmin) {
            // Superadmin sees all sites
            $sitesQuery = AnalyticsSite::withCount('sessions')
                ->with('owner');
        } else {
            // Regular users and admins see only their sites
            // Get sites user owns
            $ownedSites = AnalyticsSite::where('user_id', $userId)
                ->withCount('sessions')
                ->get();
            
            // Get sites user is a member of
            $memberSites = AnalyticsSite::whereHas('users', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })->withCount('sessions')->get();
            
            // Merge
            $allSites = $ownedSites->merge($memberSites)->unique('id');
            $siteIds = $allSites->pluck('id')->toArray();
            
            $sitesQuery = AnalyticsSite::whereIn('id', $siteIds)
                ->withCount('sessions')
                ->with('owner');
        }
        
        // Order by user's order preference, then by created_at
        $sites = $sitesQuery->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get active users data for each site (last 30 minutes for chart)
        $activeUsersStart = Carbon::now()->subMinutes(30);
        // Check traffic in last 5 minutes for indicator
        $trafficLast5Minutes = Carbon::now()->subMinutes(5);
        foreach ($sites as $site) {
            $activeUsers = AnalyticsSession::where('site_id', $site->id)
                ->where('last_seen', '>=', $activeUsersStart)
                ->where('is_bot', false)
                ->distinct()
                ->count('session_id');
            
            // Check if has traffic in last 5 minutes for indicator
            $hasTrafficLast5Min = AnalyticsSession::where('site_id', $site->id)
                ->where('last_seen', '>=', $trafficLast5Minutes)
                ->where('is_bot', false)
                ->exists();
            
            $site->active_users = $activeUsers;
            $site->has_traffic_last_5min = $hasTrafficLast5Min;
            $site->active_users_chart_data = $this->getActiveUsersChartData($site->id, $activeUsersStart);
            // Get last 24 hours data for bars chart
            $site->last_24h_chart_data = $this->getLast24HoursChartData($site->id);
            
            // Get today's unique users count
            $todayStart = Carbon::today()->startOfDay();
            $todayEnd = Carbon::today()->endOfDay();
            $site->today_users_count = AnalyticsSession::where('site_id', $site->id)
                ->whereBetween('first_seen', [$todayStart->toDateTimeString(), $todayEnd->toDateTimeString()])
                ->where('is_bot', false)
                ->distinct()
                ->count('device_fingerprint');
            
            // Fetch title from website if not set
            if (empty($site->title)) {
                $this->fetchSiteTitle($site);
            }
        }
        
        // Get pending invitations for current user
        $pendingInvitations = AnalyticsSiteInvitation::where('email', auth()->user()->email)
            ->where('status', 'pending')
            ->with('site')
            ->get();
        
        return view('admin.analytics.index', compact('sites', 'pendingInvitations', 'isSuperAdmin'));
    }

    /**
     * Show analytics dashboard for a specific site
     */
    public function show(Request $request, AnalyticsSite $site)
    {
        $siteId = $site->id;
        
        // Superadmin can access any site, others need ownership or membership
        if (!$this->isSuperAdmin() && !$site->canAccess(auth()->id())) {
            abort(403, 'You do not have access to this site.');
        }
        
        // Get date range (default to last 7 days)
        $dateFrom = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $dateFromCarbon = Carbon::parse($dateFrom);
        $dateToCarbon = Carbon::parse($dateTo);
        
        // Today's date range
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();
        
        // Last 30 minutes for active users
        $activeUsersStart = Carbon::now()->subMinutes(30);
        
        // Build query for date range
        $query = AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$dateFromCarbon->startOfDay(), $dateToCarbon->endOfDay()]);
        
        // Build query for today
        $todayQuery = AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$todayStart, $todayEnd]);
        
        // Build query for active users (last 30 minutes)
        $activeUsersQuery = AnalyticsSession::where('site_id', $siteId)
            ->where('last_seen', '>=', $activeUsersStart)
            ->where('is_bot', false);
        
        // Get statistics
        $stats = [
            'total_sessions' => (clone $query)->count(),
            'unique_visitors' => (clone $query)->distinct('device_fingerprint')->count('device_fingerprint'),
            'total_pageviews' => (clone $query)->sum('pages_count'),
            'bounce_rate' => $this->calculateBounceRate($query),
            'avg_duration' => (clone $query)->avg('duration_ms'),
            'avg_pages_per_session' => (clone $query)->avg('pages_count'),
            'new_visitors' => (clone $query)->where('is_returning', false)->count(),
            'returning_visitors' => (clone $query)->where('is_returning', true)->count(),
        ];
        
        // Get time series data
        $timeSeries = $this->getTimeSeries($siteId, $dateFromCarbon, $dateToCarbon);
        
        // Get top pages - Last 7 days (for display in sections)
        $last7DaysStart = Carbon::now()->subDays(7)->startOfDay();
        $last7DaysEnd = Carbon::now()->endOfDay();
        $topPages = $this->getTopPages($siteId, $last7DaysStart, $last7DaysEnd);
        
        // Get top entry pages
        $topEntryPages = $this->getTopEntryPages($siteId, $dateFromCarbon, $dateToCarbon);
        
        // Get top exit pages
        $topExitPages = $this->getTopExitPages($siteId, $dateFromCarbon, $dateToCarbon);
        
        // Get browser statistics - Last 7 days
        $topBrowsers = $this->getTopBrowsers($siteId, $last7DaysStart, $last7DaysEnd);
        
        // Get device statistics
        $topDevices = $this->getTopDevices($siteId, $dateFromCarbon, $dateToCarbon);
        
        // Get OS statistics
        $topOs = $this->getTopOs($siteId, $dateFromCarbon, $dateToCarbon);
        
        // Get country statistics
        $topCountries = $this->getTopCountries($siteId, $dateFromCarbon, $dateToCarbon);
        
        // Get UTM campaign statistics
        $topCampaigns = $this->getTopCampaigns($siteId, $dateFromCarbon, $dateToCarbon);
        
        // Get real-time visitors
        $realtimeVisitors = $this->getRealtimeVisitors($siteId);
        
        // Get sessions with path counts
        $sessions = AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$dateFromCarbon->startOfDay(), $dateToCarbon->endOfDay()])
            ->withCount('paths')
            ->orderBy('first_seen', 'desc')
            ->paginate(20);
        
        // Traffic Quality Metrics
        $trafficQuality = [
            'total_sessions' => (clone $query)->count(),
            'bot_sessions' => (clone $query)->where('is_bot', true)->count(),
            'real_sessions' => (clone $query)->where('is_bot', false)->count(),
            'high_quality' => (clone $query)->where('is_bot', false)
                ->where('pages_count', '>', 1)
                ->where('duration_ms', '>', 30000)
                ->where('max_scroll_percent', '>', 50)
                ->count(),
            'low_quality' => (clone $query)->where('is_bot', false)
                ->where(function($q) {
                    $q->where('pages_count', 1)
                      ->orWhere('duration_ms', '<', 5000)
                      ->orWhere('max_scroll_percent', '<', 10);
                })
                ->count(),
        ];
        
        // Page Performance with detailed stats
        $pagePerformance = $this->getPagePerformance($siteId, $dateFromCarbon, $dateToCarbon);
        
        // User Flow (entry -> next -> exit)
        $userFlow = $this->getUserFlow($siteId, $dateFromCarbon, $dateToCarbon);
        
        // Source Quality
        $sourceQuality = $this->getSourceQuality($siteId, $dateFromCarbon, $dateToCarbon);
        
        // Calculate returning sessions percentage
        $stats['returning_sessions_pct'] = $stats['total_sessions'] > 0 
            ? round(($stats['returning_visitors'] / $stats['total_sessions']) * 100, 1)
            : 0;
        
        // TODAY'S METRICS (for hero section)
        $todayStats = [
            'visitors' => (clone $todayQuery)->distinct('device_fingerprint')->count('device_fingerprint'),
            'pageviews' => (clone $todayQuery)->sum('pages_count'),
        ];
        
        // TODAY'S USERS COUNT (for card matching index page)
        $todayUsersCount = (clone $todayQuery)->where('is_bot', false)->distinct('device_fingerprint')->count('device_fingerprint');
        
        // ACTIVE USERS (last 30 minutes) - Hero metric
        $activeUsersCount = (clone $activeUsersQuery)->distinct('session_id')->count('session_id');
        $activeUsersData = $this->getActiveUsersChartData($siteId, $activeUsersStart);
        
        // Check if site has traffic in last 5 minutes (for indicator)
        $trafficLast5Minutes = Carbon::now()->subMinutes(5);
        $hasTrafficLast5Min = AnalyticsSession::where('site_id', $siteId)
            ->where('last_seen', '>=', $trafficLast5Minutes)
            ->where('is_bot', false)
            ->exists();
        
        // Check if site has traffic in last 24 hours
        $hasTrafficLast24h = AnalyticsSession::where('site_id', $siteId)
            ->where('first_seen', '>=', Carbon::now()->subHours(24)->toDateTimeString())
            ->where('is_bot', false)
            ->exists();
        
        // Visits & Paths (for expandable paths section)
        $referrerFilter = $request->get('referrer_filter', 'external'); // Default: external only
        $visitsWithPaths = $this->getVisitsWithPaths($siteId, $dateFromCarbon, $dateToCarbon, $site, 20, $referrerFilter);
        
        // Last 7 days visitors chart
        $visitorsLast7Days = $this->getVisitorsLast7Days($siteId);
        
        // Top traffic sources - Last 7 days
        $last7DaysStart = Carbon::now()->subDays(7)->startOfDay();
        $last7DaysEnd = Carbon::now()->endOfDay();
        $topTrafficSources = $this->getTopTrafficSources($siteId, $last7DaysStart, $last7DaysEnd);
        
        // Top pages - Last 7 days
        $topPages = $this->getTopPages($siteId, $last7DaysStart, $last7DaysEnd);
        
        // Top browsers - Last 7 days
        $topBrowsers = $this->getTopBrowsers($siteId, $last7DaysStart, $last7DaysEnd);
        
        // Top countries - Last 30 minutes
        $last30MinStart = Carbon::now()->subMinutes(30);
        $topCountriesLast30Min = $this->getTopCountries($siteId, $last30MinStart, Carbon::now());
        
        // Top pages - Last 30 minutes
        $topPagesLast30Min = $this->getTopPagesLast30Minutes($siteId, $last30MinStart);
        
        // Top countries - Last 7 days
        $topCountriesLast7Days = $this->getTopCountries($siteId, $last7DaysStart, $last7DaysEnd);
        
        $isSuperAdmin = $this->isSuperAdmin();
        $isAdminRoute = request()->routeIs('admin.*');
        
        return view('admin.analytics.show', compact(
            'site',
            'stats',
            'todayStats',
            'todayUsersCount',
            'activeUsersCount',
            'activeUsersData',
            'hasTrafficLast5Min',
            'hasTrafficLast24h',
            'timeSeries',
            'topPages',
            'visitsWithPaths',
            'visitorsLast7Days',
            'topTrafficSources',
            'topBrowsers',
            'topCountriesLast30Min',
            'topPagesLast30Min',
            'topCountriesLast7Days',
            'topEntryPages',
            'topExitPages',
            'topDevices',
            'topOs',
            'topCountries',
            'topCampaigns',
            'realtimeVisitors',
            'trafficQuality',
            'pagePerformance',
            'userFlow',
            'sourceQuality',
            'dateFrom',
            'dateTo',
            'isSuperAdmin',
            'isAdminRoute',
            'referrerFilter'
        ));
    }

    /**
     * Create a new analytics site
     */
    public function create()
    {
        return view('admin.analytics.create');
    }

    /**
     * Store a new analytics site
     */
    public function store(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
        ]);
        
        // Get max order for user's sites
        $maxOrder = AnalyticsSite::where('user_id', auth()->id())->max('order') ?? 0;
        
        $site = AnalyticsSite::create([
            'user_id' => auth()->id(),
            'site_key' => $this->generateSiteKey(),
            'domain' => $request->domain,
            'title' => $request->title,
            'order' => $maxOrder + 1,
        ]);
        
        $redirectRoute = request()->routeIs('admin.*') 
            ? route('admin.analytics.tracking-code', $site->site_key)
            : route('user.analytics.tracking-code', $site->site_key);
        
        return redirect($redirectRoute)
            ->with('success', 'تم إنشاء موقع التحليلات بنجاح. استخدم كود التتبع أدناه.');
    }
    
    /**
     * Reorder sites
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'sites' => 'required|array',
            'sites.*.id' => 'required|exists:analytics_sites,id',
            'sites.*.order' => 'required|integer',
        ]);
        
        $userId = auth()->id();
        $isSuperAdmin = $this->isSuperAdmin();
        
        foreach ($request->sites as $siteData) {
            $site = AnalyticsSite::find($siteData['id']);
            
            // Check access
            if (!$isSuperAdmin && !$site->canAccess($userId)) {
                continue; // Skip sites user can't access
            }
            
            $site->order = $siteData['order'];
            $site->save();
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Generate a unique site key
     */
    private function generateSiteKey()
    {
        do {
            $key = \Illuminate\Support\Str::random(32);
        } while (AnalyticsSite::where('site_key', $key)->exists());
        
        return $key;
    }

    /**
     * Calculate bounce rate
     */
    private function calculateBounceRate($query)
    {
        $total = (clone $query)->count();
        if ($total == 0) return 0;
        
        $bounces = (clone $query)->where('is_bounce', true)->count();
        return round(($bounces / $total) * 100, 2);
    }

    /**
     * Get time series data
     */
    private function getTimeSeries($siteId, $dateFrom, $dateTo)
    {
        $days = $dateFrom->diffInDays($dateTo) + 1;
        
        if ($days <= 31) {
            // Daily data
            return AnalyticsSession::where('site_id', $siteId)
                ->whereBetween('first_seen', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
                ->select(
                    DB::raw('DATE(first_seen) as date'),
                    DB::raw('COUNT(*) as sessions'),
                    DB::raw('SUM(pages_count) as pageviews')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } else {
            // Weekly data
            return AnalyticsSession::where('site_id', $siteId)
                ->whereBetween('first_seen', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
                ->select(
                    DB::raw('YEARWEEK(first_seen) as week'),
                    DB::raw('COUNT(*) as sessions'),
                    DB::raw('SUM(pages_count) as pageviews')
                )
                ->groupBy('week')
                ->orderBy('week')
                ->get();
        }
    }

    /**
     * Get top pages
     */
    private function getTopPages($siteId, $dateFrom, $dateTo)
    {
        return AnalyticsSessionPath::where('analytics_session_paths.site_id', $siteId)
            ->join('analytics_sessions', 'analytics_session_paths.session_id', '=', 'analytics_sessions.session_id')
            ->whereBetween('analytics_sessions.first_seen', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->select('analytics_session_paths.path', DB::raw('COUNT(*) as views'))
            ->groupBy('analytics_session_paths.path')
            ->orderByDesc('views')
            ->limit(30)
            ->get();
    }
    
    /**
     * Get top pages - Last 30 minutes
     */
    private function getTopPagesLast30Minutes($siteId, $startTime)
    {
        return AnalyticsSessionPath::where('analytics_session_paths.site_id', $siteId)
            ->join('analytics_sessions', 'analytics_session_paths.session_id', '=', 'analytics_sessions.session_id')
            ->where('analytics_sessions.last_seen', '>=', $startTime)
            ->where('analytics_sessions.is_bot', false)
            ->select('analytics_session_paths.path', DB::raw('COUNT(*) as views'))
            ->groupBy('analytics_session_paths.path')
            ->orderByDesc('views')
            ->limit(10)
            ->get();
    }

    /**
     * Get top entry pages
     */
    private function getTopEntryPages($siteId, $dateFrom, $dateTo)
    {
        return AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->select('entry_path', DB::raw('COUNT(*) as entries'))
            ->groupBy('entry_path')
            ->orderByDesc('entries')
            ->limit(10)
            ->get();
    }

    /**
     * Get top exit pages
     */
    private function getTopExitPages($siteId, $dateFrom, $dateTo)
    {
        return AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('last_seen', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->select('exit_path', DB::raw('COUNT(*) as exits'))
            ->groupBy('exit_path')
            ->orderByDesc('exits')
            ->limit(10)
            ->get();
    }

    /**
     * Get top browsers
     */
    private function getTopBrowsers($siteId, $dateFrom, $dateTo)
    {
        return AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->whereNotNull('browser')
            ->select('browser', DB::raw('COUNT(*) as count'))
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
    }

    /**
     * Get top devices
     */
    private function getTopDevices($siteId, $dateFrom, $dateTo)
    {
        return AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->whereNotNull('device_type')
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->orderByDesc('count')
            ->get();
    }

    /**
     * Get top operating systems
     */
    private function getTopOs($siteId, $dateFrom, $dateTo)
    {
        return AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->whereNotNull('os')
            ->select('os', DB::raw('COUNT(*) as count'))
            ->groupBy('os')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
    }

    /**
     * Get top countries
     */
    private function getTopCountries($siteId, $dateFrom, $dateTo)
    {
        return AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->whereNotNull('country')
            ->select('country', DB::raw('COUNT(*) as count'))
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
    }

    /**
     * Get top campaigns
     */
    private function getTopCampaigns($siteId, $dateFrom, $dateTo)
    {
        return AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->whereNotNull('utm_campaign')
            ->select('utm_campaign', 'utm_source', 'utm_medium', DB::raw('COUNT(*) as count'))
            ->groupBy('utm_campaign', 'utm_source', 'utm_medium')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
    }

    /**
     * Get real-time visitors (last 5 minutes)
     */
    private function getRealtimeVisitors($siteId)
    {
        return AnalyticsSession::where('site_id', $siteId)
            ->where('last_seen', '>=', Carbon::now()->subMinutes(5))
            ->orderByDesc('last_seen')
            ->limit(50)
            ->get();
    }
    
    /**
     * Get page performance with detailed metrics
     */
    private function getPagePerformance($siteId, $dateFrom, $dateTo)
    {
        return AnalyticsSessionPath::where('analytics_session_paths.site_id', $siteId)
            ->whereBetween('analytics_session_paths.created_at', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->join('analytics_sessions', function($join) {
                $join->on('analytics_sessions.session_id', '=', 'analytics_session_paths.session_id')
                     ->on('analytics_sessions.site_id', '=', 'analytics_session_paths.site_id');
            })
            ->select(
                'analytics_session_paths.path',
                DB::raw('COUNT(DISTINCT analytics_session_paths.session_id) as sessions'),
                DB::raw('SUM(CASE WHEN analytics_session_paths.position = 1 THEN 1 ELSE 0 END) as entrances'),
                DB::raw('SUM(CASE WHEN analytics_sessions.exit_path = analytics_session_paths.path THEN 1 ELSE 0 END) as exits'),
                DB::raw('AVG(analytics_session_paths.time_spent_ms) as avg_time_on_page'),
                DB::raw('AVG(analytics_session_paths.scroll_percent) as avg_scroll_percent'),
                DB::raw('SUM(CASE WHEN analytics_sessions.pages_count = 1 AND analytics_sessions.exit_path = analytics_session_paths.path THEN 1 ELSE 0 END) as bounces'),
                DB::raw('COUNT(DISTINCT analytics_session_paths.session_id) as total_sessions_for_bounce')
            )
            ->groupBy('analytics_session_paths.path')
            ->havingRaw('COUNT(DISTINCT analytics_session_paths.session_id) > 0')
            ->orderByDesc('sessions')
            ->limit(50)
            ->get()
            ->map(function($page) {
                $page->bounce_rate = $page->total_sessions_for_bounce > 0 
                    ? round(($page->bounces / $page->total_sessions_for_bounce) * 100, 1)
                    : 0;
                $page->avg_time_on_page = $page->avg_time_on_page ? round($page->avg_time_on_page / 1000, 1) : 0;
                $page->avg_scroll_percent = $page->avg_scroll_percent ? round($page->avg_scroll_percent, 1) : 0;
                return $page;
            });
    }
    
    /**
     * Get user flow data (entry -> next paths -> exit)
     */
    private function getUserFlow($siteId, $dateFrom, $dateTo)
    {
        // Get entry paths and their next paths
        $entryPaths = AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->select('entry_path', DB::raw('COUNT(*) as count'))
            ->groupBy('entry_path')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        
        $flow = [];
        foreach ($entryPaths as $entry) {
            // Get next paths after entry
            $nextPaths = AnalyticsSessionPath::where('analytics_session_paths.site_id', $siteId)
                ->whereBetween('analytics_session_paths.created_at', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
                ->join('analytics_sessions', function($join) {
                    $join->on('analytics_sessions.session_id', '=', 'analytics_session_paths.session_id')
                         ->on('analytics_sessions.site_id', '=', 'analytics_session_paths.site_id');
                })
                ->where('analytics_sessions.entry_path', $entry->entry_path)
                ->where('analytics_session_paths.position', 2)
                ->select('analytics_session_paths.path', DB::raw('COUNT(*) as count'))
                ->groupBy('analytics_session_paths.path')
                ->orderByDesc('count')
                ->limit(5)
                ->get();
            
            $flow[] = [
                'entry' => $entry->entry_path,
                'entry_count' => $entry->count,
                'next_paths' => $nextPaths,
            ];
        }
        
        return $flow;
    }
    
    /**
     * Get source quality metrics
     */
    private function getSourceQuality($siteId, $dateFrom, $dateTo)
    {
        return AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->whereNotNull('utm_source')
            ->select(
                'utm_source',
                'utm_medium',
                'utm_campaign',
                DB::raw('COUNT(*) as sessions'),
                DB::raw('AVG(duration_ms) as avg_duration'),
                DB::raw('AVG(pages_count) as avg_pages'),
                DB::raw('SUM(CASE WHEN is_bounce = 1 THEN 1 ELSE 0 END) as bounces'),
                DB::raw('SUM(CASE WHEN is_bot = 1 THEN 1 ELSE 0 END) as bots')
            )
            ->groupBy('utm_source', 'utm_medium', 'utm_campaign')
            ->orderByDesc('sessions')
            ->get()
            ->map(function($source) {
                $source->avg_duration = $source->avg_duration ? round($source->avg_duration / 1000, 1) : 0;
                $source->avg_pages = $source->avg_pages ? round($source->avg_pages, 2) : 0;
                $source->bounce_rate = $source->sessions > 0 
                    ? round(($source->bounces / $source->sessions) * 100, 1)
                    : 0;
                $source->bot_rate = $source->sessions > 0
                    ? round(($source->bots / $source->sessions) * 100, 1)
                    : 0;
                return $source;
            });
    }
    
    /**
     * Get active users chart data (last 30 minutes, 24 data points - every 1.25 minutes)
     */
    private function getActiveUsersChartData($siteId, $startTime)
    {
        $data = [];
        $interval = 1.25; // 1.25-minute intervals (24 points for 30 minutes)
        
        for ($i = 0; $i < 24; $i++) {
            $pointStart = $startTime->copy()->addMinutes($i * $interval);
            $pointEnd = $pointStart->copy()->addMinutes($interval);
            
            $count = AnalyticsSession::where('site_id', $siteId)
                ->where('last_seen', '>=', $pointStart)
                ->where('last_seen', '<', $pointEnd)
                ->where('is_bot', false)
                ->distinct()
                ->count('session_id');
            
            $data[] = [
                'time' => $pointStart->format('H:i'),
                'count' => $count,
            ];
        }
        
        return $data;
    }
    
    /**
     * Get last 24 hours chart data (grouped by hour)
     */
    private function getLast24HoursChartData($siteId)
    {
        $data = [];
        $startTime = Carbon::now()->subHours(24);
        
        // Group by hour (24 points)
        for ($i = 0; $i < 24; $i++) {
            $hourStart = $startTime->copy()->addHours($i)->startOfHour();
            $hourEnd = $hourStart->copy()->endOfHour();
            
            $count = AnalyticsSession::where('site_id', $siteId)
                ->whereBetween('first_seen', [$hourStart->toDateTimeString(), $hourEnd->toDateTimeString()])
                ->where('is_bot', false)
                ->distinct()
                ->count('session_id');
            
            $data[] = [
                'hour' => $hourStart->format('H:i'),
                'count' => $count,
            ];
        }
        
        return $data;
    }
    
    /**
     * Get visits with paths for expandable paths section
     */
    private function getVisitsWithPaths($siteId, $dateFrom, $dateTo, $site, $perPage = 20, $referrerFilter = 'external')
    {
        $startDate = $dateFrom->copy()->startOfDay()->toDateTimeString();
        $endDate = $dateTo->copy()->endOfDay()->toDateTimeString();
        
        $query = AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$startDate, $endDate])
            ->where('is_bot', false);
        
        // Apply referrer filter based on actual referrer URL domain
        $siteDomain = strtolower($site->domain);
        $siteDomainClean = preg_replace('/^www\./', '', $siteDomain);
        
        if ($referrerFilter === 'external') {
            // External only: referrer is from different domain (not the site's domain)
            $query->where(function($q) use ($siteDomainClean) {
                $q->where(function($subQ) use ($siteDomainClean) {
                    // Has referrer AND referrer_source is not Direct AND referrer URL doesn't contain site domain
                    $subQ->whereNotNull('referrer')
                         ->where('referrer_source', '!=', 'Direct')
                         ->whereNotNull('referrer_source')
                         ->where(function($refQ) use ($siteDomainClean) {
                             $refQ->where('referrer', 'NOT LIKE', '%://' . $siteDomainClean . '%')
                                  ->where('referrer', 'NOT LIKE', '%://www.' . $siteDomainClean . '%');
                         });
                });
            });
        } elseif ($referrerFilter === 'internal') {
            // Internal only: referrer_source is 'Direct' OR referrer is from same domain OR no referrer
            $query->where(function($q) use ($siteDomainClean) {
                $q->where('referrer_source', 'Direct')
                  ->orWhereNull('referrer')
                  ->orWhereNull('referrer_source')
                  ->orWhere(function($subQ) use ($siteDomainClean) {
                      // Referrer URL contains the site's domain (internal navigation)
                      $subQ->where('referrer', 'LIKE', '%://' . $siteDomainClean . '%')
                           ->orWhere('referrer', 'LIKE', '%://www.' . $siteDomainClean . '%');
                  });
            });
        }
        // If 'all', no additional filter is applied
        
        $sessions = $query->withCount('paths')
            ->orderBy('first_seen', 'desc')
            ->paginate($perPage);
        
        return $sessions->through(function($session) use ($site) {
            return [
                'session_id' => $session->session_id,
                'entry_path' => $session->entry_path,
                'exit_path' => $session->exit_path,
                'paths_count' => $session->paths_count,
                'first_seen' => $session->first_seen,
                'last_seen' => $session->last_seen,
                'duration_ms' => $session->duration_ms,
                'country' => $session->country,
                'ip' => $session->ip ? inet_ntop($session->ip) : null,
                'device_type' => $session->device_type,
                'browser' => $session->browser,
                'browser_version' => $session->browser_version,
                'referrer_source' => $session->referrer_source,
                'referrer' => $session->referrer,
                'site_domain' => $site->domain,
            ];
        });
    }
    
    /**
     * Get visitors last 7 days data
     */
    private function getVisitorsLast7Days($siteId)
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $start = $date->copy()->startOfDay();
            $end = $date->copy()->endOfDay();
            
            $count = AnalyticsSession::where('site_id', $siteId)
                ->whereBetween('first_seen', [$start, $end])
                ->where('is_bot', false)
                ->distinct('device_fingerprint')
                ->count('device_fingerprint');
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('D'),
                'count' => $count,
            ];
        }
        
        return $data;
    }
    
    /**
     * Get top traffic sources (top 10)
     */
    private function getTopTrafficSources($siteId, $dateFrom, $dateTo)
    {
        // Ensure we have Carbon instances
        $dateFrom = $dateFrom instanceof \Carbon\Carbon ? $dateFrom : \Carbon\Carbon::parse($dateFrom);
        $dateTo = $dateTo instanceof \Carbon\Carbon ? $dateTo : \Carbon\Carbon::parse($dateTo);
        
        $startDate = $dateFrom->copy()->startOfDay()->toDateTimeString();
        $endDate = $dateTo->copy()->endOfDay()->toDateTimeString();
        
        // Get referrer sources with referrer URL (this is the main source of traffic data)
        // We need to get a sample referrer URL for each referrer_source to extract hostname
        $referrerSourcesRaw = AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$startDate, $endDate])
            ->whereNotNull('referrer_source')
            ->where('is_bot', false)
            ->select('referrer_source', 'referrer', DB::raw('COUNT(*) as count'))
            ->groupBy('referrer_source', 'referrer')
            ->orderByDesc('count')
            ->get();
        
        // Group by referrer_source and aggregate counts, keeping first referrer URL
        $referrerSourcesGrouped = [];
        foreach ($referrerSourcesRaw as $source) {
            $key = $source->referrer_source;
            if (!isset($referrerSourcesGrouped[$key])) {
                $referrerSourcesGrouped[$key] = [
                    'name' => $source->referrer_source,
                    'count' => 0,
                    'referrer_url' => $source->referrer,
                ];
            }
            $referrerSourcesGrouped[$key]['count'] += $source->count;
            // Keep first non-null referrer URL
            if (!$referrerSourcesGrouped[$key]['referrer_url'] && $source->referrer) {
                $referrerSourcesGrouped[$key]['referrer_url'] = $source->referrer;
            }
        }
        
        // Sort by count and take top 10
        $referrerSources = collect($referrerSourcesGrouped)
            ->sortByDesc('count')
            ->take(10)
            ->values();
        
        // Get UTM sources (for campaigns)
        $utmSources = AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$startDate, $endDate])
            ->whereNotNull('utm_source')
            ->where('is_bot', false)
            ->select('utm_source', DB::raw('COUNT(*) as count'))
            ->groupBy('utm_source')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        
        // Get direct traffic count (no referrer)
        $directCount = AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$startDate, $endDate])
            ->where(function($q) {
                $q->whereNull('referrer_source')
                  ->orWhere('referrer_source', 'Direct');
            })
            ->where('is_bot', false)
            ->count();
        
        // Combine referrer sources
        $sources = $referrerSources->map(function($source) {
            return [
                'name' => $source['name'],
                'count' => $source['count'],
                'type' => 'referrer',
                'referrer_url' => $source['referrer_url'] ?? null,
            ];
        });
        
        // Add UTM sources that aren't already in referrer sources
        foreach ($utmSources as $utmSource) {
            $exists = $sources->firstWhere('name', $utmSource->utm_source);
            if (!$exists) {
                $sources->push([
                    'name' => $utmSource->utm_source,
                    'count' => $utmSource->count,
                    'type' => 'utm',
                ]);
            }
        }
        
        // Add direct traffic
        if ($directCount > 0) {
            $sources->push([
                'name' => 'Direct',
                'count' => $directCount,
                'type' => 'direct',
            ]);
        }
        
        return $sources->sortByDesc('count')->values()->take(10);
    }

    /**
     * Show visit path details
     */
    public function visitDetails(AnalyticsSite $site, $sessionId)
    {
        // Authorization check
        if (!$this->isSuperAdmin() && !$site->canAccess(auth()->id())) {
            abort(403, 'You do not have access to this site.');
        }
        
        $session = AnalyticsSession::where('site_id', $site->id)
            ->where('session_id', $sessionId)
            ->with(['paths' => function($query) {
                $query->orderBy('position');
            }])
            ->firstOrFail();
        
        // Format IP
        $ipAddress = $session->ip ? inet_ntop($session->ip) : 'N/A';
        
        // Calculate duration
        $duration = $session->duration_ms > 0 
            ? $session->duration_ms 
            : ($session->last_seen->diffInSeconds($session->first_seen) * 1000);
        
        // Format paths with additional info
        $paths = $session->paths->map(function($path) {
            return [
                'position' => $path->position,
                'path' => $path->path,
                'time_spent_ms' => $path->time_spent_ms,
                'scroll_percent' => $path->scroll_percent,
                'created_at' => $path->created_at,
            ];
        });
        
        $isAdminRoute = request()->routeIs('admin.*');
        
        return view('admin.analytics.visit-details', compact(
            'site', 'session', 'ipAddress', 'duration', 'paths', 'isAdminRoute'
        ));
    }
    
    /**
     * Get tracking code for a site
     */
    public function trackingCode(AnalyticsSite $site)
    {
        
        // Superadmin can access any site, others need ownership or membership
        if (!$this->isSuperAdmin() && !$site->canAccess(auth()->id())) {
            abort(403, 'You do not have access to this site.');
        }
        $baseUrl = config('app.url');
        
        $trackingCode = <<<HTML
<script>
    window.ANALYTICS_SITE_KEY = '{$site->site_key}';
    window.ANALYTICS_API_URL = '{$baseUrl}/api/analytics/track';
</script>
<script async src="{$baseUrl}/js/analytics.js"></script>
HTML;
        
        return view('admin.analytics.tracking-code', compact('site', 'trackingCode'));
    }
    
    /**
     * Send invitation to user
     */
    public function sendInvitation(Request $request, AnalyticsSite $site)
    {
        
        // Only owner or superadmin can send invitations
        if (!$this->isSuperAdmin() && $site->user_id != auth()->id()) {
            abort(403, 'Only the site owner can send invitations.');
        }
        
        $request->validate([
            'email' => 'required|email',
        ]);
        
        $email = $request->email;
        
        // Check if user is already a member
        $user = User::where('email', $email)->first();
        if ($user && $site->canAccess($user->id)) {
            return back()->with('error', 'User is already a member of this site.');
        }
        
        // Check if there's a pending invitation
        $existingInvitation = AnalyticsSiteInvitation::where('site_id', $siteId)
            ->where('email', $email)
            ->where('status', 'pending')
            ->first();
        
        if ($existingInvitation && !$existingInvitation->isExpired()) {
            return back()->with('error', 'An invitation has already been sent to this email.');
        }
        
        // Create invitation
        $invitation = AnalyticsSiteInvitation::create([
            'site_id' => $siteId,
            'invited_by' => auth()->id(),
            'email' => $email,
            'token' => AnalyticsSiteInvitation::generateToken(),
            'status' => 'pending',
            'expires_at' => Carbon::now()->addDays(7),
        ]);
        
        // Send email notification (you can customize this)
        // Mail::to($email)->send(new AnalyticsInvitationMail($invitation));
        
        return back()->with('success', 'Invitation sent successfully.');
    }
    
    /**
     * Accept invitation
     */
    public function acceptInvitation($token)
    {
        $invitation = AnalyticsSiteInvitation::where('token', $token)
            ->where('status', 'pending')
            ->with('site')
            ->firstOrFail();
        
        if ($invitation->isExpired()) {
            return redirect()->route('user.analytics.index')
                ->with('error', 'This invitation has expired.');
        }
        
        // Check if user is logged in and email matches
        if (!auth()->check() || auth()->user()->email !== $invitation->email) {
            // Redirect to login with invitation token
            return redirect()->route('login')->with('invitation_token', $token);
        }
        
        $user = auth()->user();
        
        // Add user to site
        $invitation->site->users()->attach($user->id);
        
        // Update invitation
        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => Carbon::now(),
        ]);
        
        return redirect()->route('user.analytics.show', $invitation->site->site_key)
            ->with('success', 'Invitation accepted! You now have access to this site.');
    }
    
    /**
     * Reject invitation
     */
    public function rejectInvitation($token)
    {
        $invitation = AnalyticsSiteInvitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();
        
        $invitation->update([
            'status' => 'rejected',
        ]);
        
        return redirect()->route('user.analytics.index')
            ->with('success', 'Invitation rejected.');
    }
    
    /**
     * Show site members and invitations
     */
    public function members(AnalyticsSite $site)
    {
        
        // Only owner or superadmin can manage members
        if (!$this->isSuperAdmin() && $site->user_id != auth()->id()) {
            abort(403, 'Only the site owner can manage members.');
        }
        
        $members = $site->users;
        $invitations = $site->invitations()->where('status', 'pending')->get();
        $isSuperAdmin = $this->isSuperAdmin();
        $isAdminRoute = request()->routeIs('admin.*');
        
        return view('admin.analytics.members', compact('site', 'members', 'invitations', 'isSuperAdmin', 'isAdminRoute'));
    }
    
    /**
     * Remove member from site
     */
    public function removeMember(Request $request, AnalyticsSite $site)
    {
        
        // Only owner or superadmin can remove members
        if (!$this->isSuperAdmin() && $site->user_id != auth()->id()) {
            abort(403, 'Only the site owner can remove members.');
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        
        $site->users()->detach($request->user_id);
        
        return back()->with('success', 'Member removed successfully.');
    }
    
    /**
     * Cancel invitation
     */
    public function cancelInvitation($invitationId)
    {
        $invitation = AnalyticsSiteInvitation::findOrFail($invitationId);
        
        // Only owner or superadmin can cancel invitations
        if (!$this->isSuperAdmin() && $invitation->site->user_id != auth()->id()) {
            abort(403, 'Only the site owner can cancel invitations.');
        }
        
        $invitation->delete();
        
        return back()->with('success', 'Invitation cancelled.');
    }
    
    /**
     * Show the form for editing the specified site
     */
    public function edit(AnalyticsSite $site)
    {
        // Check authorization
        if (!$this->isSuperAdmin() && !$site->canAccess(auth()->id())) {
            abort(403, 'You do not have access to this site.');
        }
        
        return view('admin.analytics.edit', compact('site'));
    }
    
    /**
     * Update the specified site
     */
    public function update(Request $request, AnalyticsSite $site)
    {
        // Check authorization
        if (!$this->isSuperAdmin() && !$site->canAccess(auth()->id())) {
            abort(403, 'You do not have access to this site.');
        }
        
        $request->validate([
            'domain' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
        ]);
        
        $site->domain = $request->domain;
        $site->title = $request->input('title') ?: null;
        $site->save();
        
        $redirectRoute = request()->routeIs('admin.*') 
            ? route('admin.analytics.show', $site->site_key)
            : route('user.analytics.show', $site->site_key);
        
        return redirect($redirectRoute)
            ->with('success', 'تم تحديث الموقع بنجاح.');
    }
    
    /**
     * Delete an analytics site
     */
    public function destroy(AnalyticsSite $site)
    {
        // Check authorization - only owner or superadmin can delete
        if (!$this->isSuperAdmin() && $site->user_id != auth()->id()) {
            abort(403, 'You do not have permission to delete this site.');
        }
        
        $siteKey = $site->site_key;
        $site->delete();
        
        $redirectRoute = request()->routeIs('admin.*') 
            ? route('admin.analytics.index')
            : route('user.analytics.index');
        
        return redirect($redirectRoute)
            ->with('success', 'تم حذف الموقع بنجاح.');
    }
    
    /**
     * Show search form
     */
    public function search(AnalyticsSite $site)
    {
        // Check authorization
        if (!$this->isSuperAdmin() && !$site->canAccess(auth()->id())) {
            abort(403, 'You do not have access to this site.');
        }
        
        $isSuperAdmin = $this->isSuperAdmin();
        $isAdminRoute = request()->routeIs('admin.*');
        
        return view('admin.analytics.search', compact('site', 'isSuperAdmin', 'isAdminRoute'));
    }
    
    /**
     * Show search results
     */
    public function searchResults(Request $request, AnalyticsSite $site)
    {
        // Check authorization
        if (!$this->isSuperAdmin() && !$site->canAccess(auth()->id())) {
            abort(403, 'You do not have access to this site.');
        }
        
        $request->validate([
            'query' => 'required|string|max:500',
            'match_type' => 'required|in:prefix,exact,ip',
        ]);
        
        $query = trim($request->input('query'));
        $matchType = $request->input('match_type');
        $siteId = $site->id;
        
        // Get date range (default to last 30 days)
        $dateFrom = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $dateFromCarbon = Carbon::parse($dateFrom);
        $dateToCarbon = Carbon::parse($dateTo);
        
        // Build base query for sessions
        $baseQuery = AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$dateFromCarbon->startOfDay(), $dateToCarbon->endOfDay()])
            ->where('is_bot', false);
        
        // Get session IDs based on match type
        $sessionIds = collect();
        
        if ($matchType === 'ip') {
            // Search by IP address
            $ipBinary = inet_pton($query);
            if ($ipBinary) {
                $sessionIds = (clone $baseQuery)
                    ->where('ip', $ipBinary)
                    ->pluck('session_id');
            }
        } else {
            // Search by URL/path
            // Extract path from full URL if provided
            $searchPath = $query;
            if (preg_match('/https?:\/\/[^\/]+(\/.*)?$/', $query, $matches)) {
                $searchPath = $matches[1] ?? '/';
            }
            
            // Normalize path
            if (empty($searchPath) || $searchPath === '/') {
                $searchPath = '/';
            }
            
            // Get session IDs from paths
            $pathsQuery = AnalyticsSessionPath::where('analytics_session_paths.site_id', $siteId)
                ->join('analytics_sessions', 'analytics_session_paths.session_id', '=', 'analytics_sessions.session_id')
                ->whereBetween('analytics_sessions.first_seen', [$dateFromCarbon->startOfDay(), $dateToCarbon->endOfDay()]);
            
            if ($matchType === 'exact') {
                $pathsQuery->where('analytics_session_paths.path', $searchPath);
            } else { // prefix
                $pathsQuery->where('analytics_session_paths.path', 'LIKE', $searchPath . '%');
            }
            
            $sessionIds = $pathsQuery->pluck('analytics_session_paths.session_id')->unique();
        }
        
        if ($sessionIds->isEmpty()) {
            // No results found
            return view('admin.analytics.search-results', compact(
                'site', 'query', 'matchType', 'dateFrom', 'dateTo', 'sessionIds', 'isSuperAdmin', 'isAdminRoute'
            ))->with('noResults', true);
        }
        
        // Get filtered sessions
        $filteredSessions = (clone $baseQuery)
            ->whereIn('session_id', $sessionIds)
            ->get();
        
        // Calculate statistics for filtered sessions
        $stats = [
            'total_sessions' => $filteredSessions->count(),
            'unique_visitors' => $filteredSessions->pluck('device_fingerprint')->unique()->count(),
            'total_pageviews' => $filteredSessions->sum('pages_count'),
            'bounce_rate' => $this->calculateBounceRateForSessions($filteredSessions),
            'avg_duration' => $filteredSessions->avg('duration_ms'),
            'avg_pages_per_session' => $filteredSessions->avg('pages_count'),
            'new_visitors' => $filteredSessions->where('is_returning', false)->count(),
            'returning_visitors' => $filteredSessions->where('is_returning', true)->count(),
        ];
        
        // Today's date range
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();
        
        // Last 30 minutes for active users
        $activeUsersStart = Carbon::now()->subMinutes(30);
        
        // TODAY'S METRICS
        $todayQuery = (clone $baseQuery)
            ->whereIn('session_id', $sessionIds)
            ->whereBetween('first_seen', [$todayStart, $todayEnd]);
        
        $todayStats = [
            'visitors' => (clone $todayQuery)->distinct('device_fingerprint')->count('device_fingerprint'),
            'pageviews' => (clone $todayQuery)->sum('pages_count'),
        ];
        
        $todayUsersCount = (clone $todayQuery)->distinct('device_fingerprint')->count('device_fingerprint');
        
        // ACTIVE USERS (last 30 minutes) - from filtered sessions
        $activeUsersQuery = (clone $baseQuery)
            ->whereIn('session_id', $sessionIds)
            ->where('last_seen', '>=', $activeUsersStart);
        
        $activeUsersCount = (clone $activeUsersQuery)->distinct('session_id')->count('session_id');
        $activeUsersData = $this->getActiveUsersChartDataForSessions($siteId, $activeUsersStart, $sessionIds);
        
        // Check if has traffic in last 5 minutes
        $trafficLast5Minutes = Carbon::now()->subMinutes(5);
        $hasTrafficLast5Min = (clone $baseQuery)
            ->whereIn('session_id', $sessionIds)
            ->where('last_seen', '>=', $trafficLast5Minutes)
            ->exists();
        
        // Get top pages (from filtered sessions)
        $topPages = $this->getTopPagesForSessions($siteId, $dateFromCarbon, $dateToCarbon, $sessionIds);
        
        // Get top browsers
        $topBrowsers = $this->getTopBrowsersForSessions($siteId, $dateFromCarbon, $dateToCarbon, $sessionIds);
        
        // Get top traffic sources
        $topTrafficSources = $this->getTopTrafficSourcesForSessions($siteId, $dateFromCarbon, $dateToCarbon, $sessionIds);
        
        // Get visitors last 7 days
        $visitorsLast7Days = $this->getVisitorsLast7DaysForSessions($siteId, $sessionIds);
        
        // Get visits with paths
        $visitsWithPaths = $this->getVisitsWithPathsForSessions($siteId, $dateFromCarbon, $dateToCarbon, $site, $sessionIds, 20);
        
        $isSuperAdmin = $this->isSuperAdmin();
        $isAdminRoute = request()->routeIs('admin.*');
        
        return view('admin.analytics.search-results', compact(
            'site',
            'query',
            'matchType',
            'dateFrom',
            'dateTo',
            'stats',
            'todayStats',
            'todayUsersCount',
            'activeUsersCount',
            'activeUsersData',
            'hasTrafficLast5Min',
            'topPages',
            'topBrowsers',
            'topTrafficSources',
            'visitorsLast7Days',
            'visitsWithPaths',
            'isSuperAdmin',
            'isAdminRoute'
        ));
    }
    
    /**
     * Calculate bounce rate for a collection of sessions
     */
    private function calculateBounceRateForSessions($sessions)
    {
        $total = $sessions->count();
        if ($total == 0) return 0;
        
        $bounces = $sessions->where('is_bounce', true)->count();
        return round(($bounces / $total) * 100, 2);
    }
    
    /**
     * Get active users chart data for specific sessions
     */
    private function getActiveUsersChartDataForSessions($siteId, $startTime, $sessionIds)
    {
        $data = [];
        $interval = 1.25; // 1.25-minute intervals (24 points for 30 minutes)
        
        for ($i = 0; $i < 24; $i++) {
            $pointStart = $startTime->copy()->addMinutes($i * $interval);
            $pointEnd = $pointStart->copy()->addMinutes($interval);
            
            $count = AnalyticsSession::where('site_id', $siteId)
                ->whereIn('session_id', $sessionIds)
                ->where('last_seen', '>=', $pointStart)
                ->where('last_seen', '<', $pointEnd)
                ->where('is_bot', false)
                ->distinct()
                ->count('session_id');
            
            $data[] = [
                'time' => $pointStart->format('H:i'),
                'count' => $count,
            ];
        }
        
        return $data;
    }
    
    /**
     * Get top pages for specific sessions
     */
    private function getTopPagesForSessions($siteId, $dateFrom, $dateTo, $sessionIds)
    {
        return AnalyticsSessionPath::where('analytics_session_paths.site_id', $siteId)
            ->whereIn('analytics_session_paths.session_id', $sessionIds)
            ->join('analytics_sessions', 'analytics_session_paths.session_id', '=', 'analytics_sessions.session_id')
            ->whereBetween('analytics_sessions.first_seen', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->select('analytics_session_paths.path', DB::raw('COUNT(*) as views'))
            ->groupBy('analytics_session_paths.path')
            ->orderByDesc('views')
            ->limit(30)
            ->get();
    }
    
    /**
     * Get top browsers for specific sessions
     */
    private function getTopBrowsersForSessions($siteId, $dateFrom, $dateTo, $sessionIds)
    {
        return AnalyticsSession::where('site_id', $siteId)
            ->whereIn('session_id', $sessionIds)
            ->whereBetween('first_seen', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->whereNotNull('browser')
            ->select('browser', DB::raw('COUNT(*) as count'))
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
    }
    
    /**
     * Get top traffic sources for specific sessions
     */
    private function getTopTrafficSourcesForSessions($siteId, $dateFrom, $dateTo, $sessionIds)
    {
        $startDate = $dateFrom->copy()->startOfDay()->toDateTimeString();
        $endDate = $dateTo->copy()->endOfDay()->toDateTimeString();
        
        $referrerSourcesRaw = AnalyticsSession::where('site_id', $siteId)
            ->whereIn('session_id', $sessionIds)
            ->whereBetween('first_seen', [$startDate, $endDate])
            ->whereNotNull('referrer_source')
            ->where('is_bot', false)
            ->select('referrer_source', 'referrer', DB::raw('COUNT(*) as count'))
            ->groupBy('referrer_source', 'referrer')
            ->orderByDesc('count')
            ->get();
        
        $referrerSourcesGrouped = [];
        foreach ($referrerSourcesRaw as $source) {
            $key = $source->referrer_source;
            if (!isset($referrerSourcesGrouped[$key])) {
                $referrerSourcesGrouped[$key] = [
                    'name' => $source->referrer_source,
                    'count' => 0,
                    'referrer_url' => $source->referrer,
                ];
            }
            $referrerSourcesGrouped[$key]['count'] += $source->count;
            if (!$referrerSourcesGrouped[$key]['referrer_url'] && $source->referrer) {
                $referrerSourcesGrouped[$key]['referrer_url'] = $source->referrer;
            }
        }
        
        $referrerSources = collect($referrerSourcesGrouped)
            ->sortByDesc('count')
            ->take(10)
            ->values();
        
        $utmSources = AnalyticsSession::where('site_id', $siteId)
            ->whereIn('session_id', $sessionIds)
            ->whereBetween('first_seen', [$startDate, $endDate])
            ->whereNotNull('utm_source')
            ->where('is_bot', false)
            ->select('utm_source', DB::raw('COUNT(*) as count'))
            ->groupBy('utm_source')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        
        $directCount = AnalyticsSession::where('site_id', $siteId)
            ->whereIn('session_id', $sessionIds)
            ->whereBetween('first_seen', [$startDate, $endDate])
            ->where(function($q) {
                $q->whereNull('referrer_source')
                  ->orWhere('referrer_source', 'Direct');
            })
            ->where('is_bot', false)
            ->count();
        
        $sources = $referrerSources->map(function($source) {
            return [
                'name' => $source['name'],
                'count' => $source['count'],
                'type' => 'referrer',
                'referrer_url' => $source['referrer_url'] ?? null,
            ];
        });
        
        foreach ($utmSources as $utmSource) {
            $exists = $sources->firstWhere('name', $utmSource->utm_source);
            if (!$exists) {
                $sources->push([
                    'name' => $utmSource->utm_source,
                    'count' => $utmSource->count,
                    'type' => 'utm',
                ]);
            }
        }
        
        if ($directCount > 0) {
            $sources->push([
                'name' => 'Direct',
                'count' => $directCount,
                'type' => 'direct',
            ]);
        }
        
        return $sources->sortByDesc('count')->values()->take(10);
    }
    
    /**
     * Get visitors last 7 days for specific sessions
     */
    private function getVisitorsLast7DaysForSessions($siteId, $sessionIds)
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $start = $date->copy()->startOfDay();
            $end = $date->copy()->endOfDay();
            
            $count = AnalyticsSession::where('site_id', $siteId)
                ->whereIn('session_id', $sessionIds)
                ->whereBetween('first_seen', [$start, $end])
                ->where('is_bot', false)
                ->distinct('device_fingerprint')
                ->count('device_fingerprint');
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('D'),
                'count' => $count,
            ];
        }
        
        return $data;
    }
    
    /**
     * Get visits with paths for specific sessions
     */
    private function getVisitsWithPathsForSessions($siteId, $dateFrom, $dateTo, $site, $sessionIds, $perPage = 20)
    {
        $startDate = $dateFrom->copy()->startOfDay()->toDateTimeString();
        $endDate = $dateTo->copy()->endOfDay()->toDateTimeString();
        
        $sessions = AnalyticsSession::where('site_id', $siteId)
            ->whereIn('session_id', $sessionIds)
            ->whereBetween('first_seen', [$startDate, $endDate])
            ->where('is_bot', false)
            ->withCount('paths')
            ->orderBy('first_seen', 'desc')
            ->paginate($perPage);
        
        return $sessions->through(function($session) use ($site) {
            return [
                'session_id' => $session->session_id,
                'entry_path' => $session->entry_path,
                'exit_path' => $session->exit_path,
                'paths_count' => $session->paths_count,
                'first_seen' => $session->first_seen,
                'last_seen' => $session->last_seen,
                'duration_ms' => $session->duration_ms,
                'country' => $session->country,
                'ip' => $session->ip ? inet_ntop($session->ip) : null,
                'device_type' => $session->device_type,
                'browser' => $session->browser,
                'browser_version' => $session->browser_version,
                'referrer_source' => $session->referrer_source,
                'referrer' => $session->referrer,
                'site_domain' => $site->domain,
            ];
        });
    }
    
    /**
     * Fetch site title from website HTML
     */
    private function fetchSiteTitle($site)
    {
        try {
            $url = 'https://' . $site->domain;
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'Mozilla/5.0 (compatible; AnalyticsBot/1.0)',
                    'follow_location' => 1,
                    'max_redirects' => 3
                ]
            ]);
            
            $html = @file_get_contents($url, false, $context);
            if ($html === false) {
                return;
            }
            
            // Extract title from HTML
            if (preg_match('/<title[^>]*>([^<]+)<\/title>/i', $html, $matches)) {
                $title = trim($matches[1]);
                
                // Clean title: take first 2 words or until first special character
                $title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                
                // Split by special characters or take first 2 words
                $words = preg_split('/[\s\-_|:;.,!?]+/', $title, 3);
                if (count($words) >= 2) {
                    $title = $words[0] . ' ' . $words[1];
                } else {
                    // If less than 2 words, take until first special character
                    $title = preg_split('/[^\w\s]/', $title)[0];
                }
                
                // Limit length
                $title = mb_substr(trim($title), 0, 100);
                
                if (!empty($title)) {
                    $site->title = $title;
                    $site->save();
                }
            }
        } catch (\Exception $e) {
            // Silently fail - don't block page load
        }
    }
}
