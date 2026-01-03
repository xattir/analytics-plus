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
            $sites = AnalyticsSite::withCount('sessions')
                ->with('owner')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
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
            
            // Merge and paginate
            $allSites = $ownedSites->merge($memberSites)->unique('id');
            $sites = new \Illuminate\Pagination\LengthAwarePaginator(
                $allSites->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 20),
                $allSites->count(),
                20,
                \Illuminate\Pagination\Paginator::resolveCurrentPage(),
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );
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
    public function show(Request $request, $siteId)
    {
        $site = AnalyticsSite::findOrFail($siteId);
        
        // Superadmin can access any site, others need ownership or membership
        if (!$this->isSuperAdmin() && !$site->canAccess(auth()->id())) {
            abort(403, 'You do not have access to this site.');
        }
        
        // Get date range
        $dateFrom = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        $dateFromCarbon = Carbon::parse($dateFrom);
        $dateToCarbon = Carbon::parse($dateTo);
        
        // Build query
        $query = AnalyticsSession::where('site_id', $siteId)
            ->whereBetween('first_seen', [$dateFromCarbon->startOfDay(), $dateToCarbon->endOfDay()]);
        
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
        
        // Get top pages
        $topPages = $this->getTopPages($siteId, $dateFromCarbon, $dateToCarbon);
        
        // Get top entry pages
        $topEntryPages = $this->getTopEntryPages($siteId, $dateFromCarbon, $dateToCarbon);
        
        // Get top exit pages
        $topExitPages = $this->getTopExitPages($siteId, $dateFromCarbon, $dateToCarbon);
        
        // Get browser statistics
        $topBrowsers = $this->getTopBrowsers($siteId, $dateFromCarbon, $dateToCarbon);
        
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
        
        $isSuperAdmin = $this->isSuperAdmin();
        $isAdminRoute = request()->routeIs('admin.*');
        
        return view('admin.analytics.show', compact(
            'site',
            'stats',
            'timeSeries',
            'topPages',
            'topEntryPages',
            'topExitPages',
            'topBrowsers',
            'topDevices',
            'topOs',
            'topCountries',
            'topCampaigns',
            'realtimeVisitors',
            'dateFrom',
            'dateTo',
            'isSuperAdmin',
            'isAdminRoute'
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
        ]);
        
        $site = AnalyticsSite::create([
            'user_id' => auth()->id(),
            'site_key' => $this->generateSiteKey(),
            'domain' => $request->domain,
        ]);
        
        $redirectRoute = request()->routeIs('admin.*') 
            ? route('admin.analytics.show', $site->id)
            : route('user.analytics.show', $site->id);
        
        return redirect($redirectRoute)
            ->with('success', 'Analytics site created successfully. Use the tracking code below.');
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
        return AnalyticsSessionPath::where('site_id', $siteId)
            ->whereBetween('created_at', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->select('path', DB::raw('COUNT(*) as views'))
            ->groupBy('path')
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
     * Get tracking code for a site
     */
    public function trackingCode($siteId)
    {
        $site = AnalyticsSite::findOrFail($siteId);
        
        // Superadmin can access any site, others need ownership or membership
        if (!$this->isSuperAdmin() && !$site->canAccess(auth()->id())) {
            abort(403, 'You do not have access to this site.');
        }
        $baseUrl = config('app.url');
        
        $trackingCode = <<<HTML
<!-- Analytics Tracking Code -->
<script>
    window.ANALYTICS_SITE_KEY = '{$site->site_key}';
    window.ANALYTICS_API_URL = '{$baseUrl}/api/analytics/track';
</script>
<script src="{$baseUrl}/js/analytics.js"></script>
<!-- End Analytics Tracking Code -->
HTML;
        
        return view('admin.analytics.tracking-code', compact('site', 'trackingCode'));
    }
    
    /**
     * Send invitation to user
     */
    public function sendInvitation(Request $request, $siteId)
    {
        $site = AnalyticsSite::findOrFail($siteId);
        
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
        
        return redirect()->route('user.analytics.show', $invitation->site_id)
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
    public function members($siteId)
    {
        $site = AnalyticsSite::findOrFail($siteId);
        
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
    public function removeMember(Request $request, $siteId)
    {
        $site = AnalyticsSite::findOrFail($siteId);
        
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
}
