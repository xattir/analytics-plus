<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsSite;
use App\Models\AnalyticsSession;
use App\Models\AnalyticsSessionPath;
use App\Helpers\UserSystemInfoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Stevebauman\Location\Facades\Location;

class AnalyticsController extends Controller
{
    /**
     * Track a page view event
     */
    public function track(Request $request)
    {
        // Explicitly disable Debugbar to prevent large response headers
        // This prevents "upstream sent too big header" errors in nginx
        if (class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class)) {
            \Barryvdh\Debugbar\Facades\Debugbar::disable();
        }
        
        try {
            // Handle FormData (simple request) - extract site_key from form data or JSON
            $siteKey = $request->input('site_key') ?? $request->json('site_key');
            if (!$siteKey) {
                return response()->json(['error' => 'site_key is required'], 400, [
                    'Content-Type' => 'application/json',
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            // Get or create site
            $site = AnalyticsSite::firstOrCreate(
                ['site_key' => $siteKey],
                ['domain' => $request->input('domain', parse_url($request->input('url', ''), PHP_URL_HOST) ?? 'unknown')]
            );

            // Get session ID from request or generate new one
            $sessionId = $request->input('session_id') ?? $this->generateSessionId();
            
            // Check if this is a periodic/interval event (has engagement metrics)
            // Skip periodic updates - only process initial page view events
            // IMPORTANT: Check this BEFORE creating session to avoid empty rows
            $hasEngagementData = $request->has('duration_ms') || $request->has('scroll_percent') || 
                                 $request->has('active_time_ms') || $request->has('time_spent_ms') ||
                                 $request->has('idle_time_ms');
            
            if ($hasEngagementData) {
                // Skip: This is a periodic/interval update, don't process
                return response()->json([
                    'success' => true,
                    'session_id' => $sessionId,
                    'skipped' => true,
                ], 200, [
                    'Content-Type' => 'application/json',
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
            
            // Get request data
            $path = $request->input('path', '/');
            $url = $request->input('url', ''); // Full URL (e.g., https://subdomain.example.com/page)
            $userAgent = $request->input('user_agent') ?? $request->header('User-Agent', '');
            $ip = $this->getIpAddress($request);
            
            // Use full URL for entry_path and exit_path if available, otherwise fallback to path
            $entryUrl = !empty($url) ? $url : $path;
            $exitUrl = !empty($url) ? $url : $path;
            
            // Parse user agent
            $deviceInfo = $this->parseDeviceInfo($userAgent, $request);
            
            // Get screen info to override device type if needed
            $screenInfo = $this->getScreenInfo($request);
            // Override device type based on screen width if available
            if (isset($screenInfo['screen_width']) && $screenInfo['screen_width'] > 0) {
                if ($screenInfo['screen_width'] < 768) {
                    $deviceInfo['device_type'] = 'mobile';
                } elseif ($screenInfo['screen_width'] < 1024) {
                    $deviceInfo['device_type'] = 'tablet';
                } else {
                    $deviceInfo['device_type'] = 'desktop';
                }
            }
            
            // Check if bot FIRST (fast check, no external calls)
            $isBot = $this->isBot($userAgent);
            
            // Get geo location (only if not bot, to save time)
            $geoInfo = $isBot ? ['country_code' => null, 'city' => null, 'isp' => null] : $this->getGeoInfo($ip);
            
            // Get device fingerprint
            $fingerprint = $request->input('fingerprint') ?? $this->generateFingerprint($request);
            
            // Check if returning visitor
            $isReturning = $this->isReturningVisitor($site->id, $fingerprint);
            
            // Parse UTM parameters
            $utmParams = $this->parseUtmParams($request);
            
            // Get referrer and extract source and domain
            $referrer = $request->input('referrer');
            $referrerSource = $this->extractReferrerSource($referrer, $site->domain);
            $referrerDomain = $this->extractReferrerDomain($referrer, $site->domain);
            
            // Get network info
            $networkInfo = $this->getNetworkInfo($request);
            
            // Get screen/viewport info (already got above, but ensure we have it)
            $screenInfo = $this->getScreenInfo($request);
            
            // Get or create session - use firstOrCreate to ensure session exists and is loaded
            // IMPORTANT: Only create session AFTER checking for engagement data to avoid empty rows
            $session = AnalyticsSession::firstOrCreate(
                [
                    'site_id' => $site->id,
                    'session_id' => $sessionId,
                ],
                [
                    // Default values for new session
                    'first_seen' => now(),
                    'last_seen' => now(),
                    'pages_count' => 0,
                    'is_bot' => false,
                ]
            );

            $isNewSession = $session->wasRecentlyCreated;
            $now = now();
            
            // Update or create session (ONLY on initial page view - no engagement metrics)
            {
                // CRITICAL: Always update last_seen FIRST for active users tracking
                // This ensures "المستخدمون النشطون" query works correctly
                // last_seen must be updated on EVERY page view to track active users
                $session->last_seen = $now;
                $session->exit_path = $exitUrl; // Store full URL (e.g., https://subdomain.example.com/page)
                
                // Update or create session
                if ($isNewSession) {
                    $session->first_seen = $now;
                    $session->entry_path = $entryUrl; // Store full URL (e.g., https://subdomain.example.com/page)
                    $session->pages_count = 1;
                    // Save referrer only for new sessions (entry point)
                    $session->referrer = $referrer;
                    $session->referrer_source = $referrerSource;
                    $session->referrer_domain = $referrerDomain;
                    $session->utm_source = $utmParams['utm_source'];
                    $session->utm_medium = $utmParams['utm_medium'];
                    $session->utm_campaign = $utmParams['utm_campaign'];
                    $session->is_returning = $isReturning;
                } else {
                    // Update existing session - increment page count
                    $session->pages_count = ($session->pages_count ?? 0) + 1;
                    // Don't overwrite entry_path and referrer for existing sessions
                }
                $session->duration_ms = 0; // Will be updated later if needed
                $session->user_agent = $userAgent;
                $session->device_fingerprint = $fingerprint;
                $session->device_type = $deviceInfo['device_type'];
                $session->os = $deviceInfo['os'];
                $session->os_version = $deviceInfo['os_version'];
                $session->browser = $deviceInfo['browser'];
                $session->browser_version = $deviceInfo['browser_version'];
                $session->browser_engine = $deviceInfo['browser_engine'];
                $session->screen_width = $screenInfo['screen_width'];
                $session->screen_height = $screenInfo['screen_height'];
                $session->viewport_width = $screenInfo['viewport_width'];
                $session->viewport_height = $screenInfo['viewport_height'];
                $session->device_pixel_ratio = $screenInfo['device_pixel_ratio'];
                $session->network_type = $networkInfo['network_type'];
                $session->rtt_ms = $networkInfo['rtt_ms'];
                $session->downlink_mbps = $networkInfo['downlink_mbps'];
                $session->country = $geoInfo['country_code'];
                $session->city = $geoInfo['city'];
                $session->isp = $geoInfo['isp'];
                $session->is_bounce = false; // Default, will be updated later
                $session->is_bot = $isBot;
                $session->max_scroll_percent = 0; // Will be updated later if needed
                $session->active_time_ms = 0; // Will be updated later if needed
                $session->idle_time_ms = 0; // Will be updated later if needed
                $session->ip = inet_pton($ip);
                
                // Precompute quality flags at insert/update time for fast aggregations
                // This eliminates expensive CASE expressions in dashboard queries
                $isHighQuality = false; // Will be computed later
                $isLowQuality = false; // Will be computed later
                
                $session->is_high_quality = false;
                $session->is_low_quality = false;
                
                // CRITICAL: Always save session to update last_seen for active users tracking
                // This ensures "المستخدمون النشطون" shows correct count
                // Use updateOrInsert as fallback if save fails
                try {
                    $session->save();
                } catch (\Exception $saveError) {
                    // If save fails, use updateOrInsert as fallback
                    // This ensures last_seen is always updated
                    DB::table('analytics_sessions')
                        ->where('site_id', $site->id)
                        ->where('session_id', $sessionId)
                        ->update([
                            'last_seen' => $now,
                            'exit_path' => $exitUrl,
                            'pages_count' => $isNewSession ? 1 : DB::raw('pages_count + 1'),
                        ]);
                }
            }
            
            // Update rollup tables incrementally (fast, atomic upserts)
            // Only update on initial page view
            $date = $now->format('Y-m-d');
            
            try {
                // Update daily path rollup
                \App\Models\AnalyticsDailyPath::incrementPath($site->id, $date, $path, 1);
            
                // Update daily dimension rollups (only for new sessions to avoid double counting)
                if ($isNewSession && !$isBot) {
                    // Entry path
                    if ($session->entry_path) {
                        \App\Models\AnalyticsDailyDimension::incrementDimension(
                            $site->id, 
                            $date, 
                            'entry_path', 
                            $session->entry_path
                        );
                    }
                    
                    // Country
                    if ($session->country) {
                        \App\Models\AnalyticsDailyDimension::incrementDimension(
                            $site->id, 
                            $date, 
                            'country', 
                            $session->country
                        );
                    }
                    
                    // Browser
                    if ($session->browser) {
                        \App\Models\AnalyticsDailyDimension::incrementDimension(
                            $site->id, 
                            $date, 
                            'browser', 
                            $session->browser
                        );
                    }
                    
                    // OS
                    if ($session->os) {
                        \App\Models\AnalyticsDailyDimension::incrementDimension(
                            $site->id, 
                            $date, 
                            'os', 
                            $session->os
                        );
                    }
                    
                    // Device type
                    if ($session->device_type) {
                        \App\Models\AnalyticsDailyDimension::incrementDimension(
                            $site->id, 
                            $date, 
                            'device_type', 
                            $session->device_type
                        );
                    }
                    
                    // Referrer source (keep for backward compatibility)
                    if ($session->referrer_source) {
                        \App\Models\AnalyticsDailyDimension::incrementDimension(
                            $site->id, 
                            $date, 
                            'referrer_source', 
                            $session->referrer_source
                        );
                    }
                    
                    // Referrer domain (actual domain with subdomain)
                    if ($session->referrer_domain) {
                        \App\Models\AnalyticsDailyDimension::incrementDimension(
                            $site->id, 
                            $date, 
                            'referrer_domain', 
                            $session->referrer_domain
                        );
                    }
                }
                
                // Update exit path rollup
                if (!$isBot && $session->exit_path) {
                    \App\Models\AnalyticsDailyDimension::incrementDimension(
                        $site->id, 
                        $date, 
                        'exit_path', 
                        $session->exit_path
                    );
                }
            } catch (\Exception $rollupError) {
                // Silently fail - rollup updates are non-critical
                // Don't log to prevent storage/logs growth
            }
            
            // Track path - MUST be saved after session to ensure session exists
            // Use DB::table for better performance and error handling
            try {
                // Get max position using raw query for better performance
                $pathPosition = DB::table('analytics_session_paths')
                    ->where('session_id', $sessionId)
                    ->where('site_id', $site->id)
                    ->max('position') ?? 0;
                
                // Insert directly using DB::table for better performance
                DB::table('analytics_session_paths')->insert([
                    'site_id' => $site->id,
                    'session_id' => $sessionId,
                    'path' => $path,
                    'position' => $pathPosition + 1,
                    'scroll_percent' => 0, // Will be updated later if needed
                    'time_spent_ms' => 0, // Will be updated later if needed
                    'created_at' => $now,
                ]);
            } catch (\Exception $pathError) {
                // Silently fail - path saving is non-critical
                // Don't log to prevent storage/logs growth
            }
            
            // Return minimal response - nginx will add CORS headers
            return response()->json([
                'success' => true,
                'session_id' => $sessionId,
            ], 200, [
                'Content-Type' => 'application/json',
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            
        } catch (\Exception $e) {
            // Don't log errors to prevent storage/logs growth
            // Return minimal error response - nginx will add CORS headers
            return response()->json(['error' => 'Tracking failed'], 500, [
                'Content-Type' => 'application/json',
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * Generate a unique session ID
     */
    private function generateSessionId(): string
    {
        return (string) Str::uuid();
    }
    
    /**
     * Get IP address from request
     */
    private function getIpAddress(Request $request): string
    {
        $ip = $request->input('ip');
        if ($ip) {
            return $ip;
        }
        
        return UserSystemInfoHelper::get_ip();
    }
    
    /**
     * Parse device information from user agent and request
     */
    private function parseDeviceInfo(string $userAgent, Request $request): array
    {
        $device = UserSystemInfoHelper::get_device();
        $os = UserSystemInfoHelper::get_os();
        
        // Map device type
        $deviceType = null;
        $deviceLower = strtolower($device);
        if (stripos($deviceLower, 'mobile') !== false) {
            $deviceType = 'mobile';
        } elseif (stripos($deviceLower, 'tablet') !== false) {
            $deviceType = 'tablet';
        } else {
            $deviceType = 'desktop';
        }
        
        // Extract OS version
        $osVersion = null;
        if (preg_match('/(?:Windows|Mac OS X|Linux|Android|iOS)\s+([\d.]+)/i', $userAgent, $matches)) {
            $osVersion = $matches[1];
        }
        
        // Detect browser - IMPORTANT: Check Edge and Chrome BEFORE Safari
        // because most browsers include "Safari" in their user agent
        $browser = 'Unknown Browser';
        $browserVersion = null;
        $browserEngine = null;
        
        $uaLower = strtolower($userAgent);
        
        // Edge (check first, as it may contain Chrome)
        if (stripos($userAgent, 'Edg/') !== false || stripos($userAgent, 'Edge/') !== false) {
            $browser = 'Edge';
            if (preg_match('/Edg[e]?\/([\d.]+)/i', $userAgent, $matches)) {
                $browserVersion = $matches[1];
            }
            $browserEngine = 'Blink';
        }
        // Chrome (check before Safari, as Chrome contains Safari in UA)
        elseif (stripos($userAgent, 'Chrome/') !== false && stripos($userAgent, 'Edg') === false) {
            $browser = 'Chrome';
            if (preg_match('/Chrome\/([\d.]+)/i', $userAgent, $matches)) {
                $browserVersion = $matches[1];
            }
            $browserEngine = 'Blink';
        }
        // Firefox
        elseif (stripos($userAgent, 'Firefox/') !== false) {
            $browser = 'Firefox';
            if (preg_match('/Firefox\/([\d.]+)/i', $userAgent, $matches)) {
                $browserVersion = $matches[1];
            }
            $browserEngine = 'Gecko';
        }
        // Opera
        elseif (stripos($userAgent, 'Opera/') !== false || stripos($userAgent, 'OPR/') !== false) {
            $browser = 'Opera';
            if (preg_match('/(?:Opera|OPR)\/([\d.]+)/i', $userAgent, $matches)) {
                $browserVersion = $matches[1];
            }
            $browserEngine = 'Blink';
        }
        // Safari (check last, as it's often included in other browsers)
        elseif (stripos($userAgent, 'Safari/') !== false && stripos($userAgent, 'Chrome') === false) {
            $browser = 'Safari';
            if (preg_match('/Version\/([\d.]+)/i', $userAgent, $matches)) {
                $browserVersion = $matches[1];
            }
            $browserEngine = 'WebKit';
        }
        // Internet Explorer
        elseif (stripos($userAgent, 'MSIE') !== false || stripos($userAgent, 'Trident') !== false) {
            $browser = 'Internet Explorer';
            if (preg_match('/MSIE\s+([\d.]+)/i', $userAgent, $matches)) {
                $browserVersion = $matches[1];
            }
            $browserEngine = 'Trident';
        }
        
        return [
            'device_type' => $deviceType,
            'os' => $os,
            'os_version' => $osVersion,
            'browser' => $browser,
            'browser_version' => $browserVersion,
            'browser_engine' => $browserEngine,
        ];
    }
    
    /**
     * Get geo location information
     */
    private function getGeoInfo(string $ip): array
    {
        try {
            $location = Location::get($ip);
            if ($location) {
                return [
                    'country_code' => $location->countryCode ?? null,
                    'city' => $location->cityName ?? null,
                    'isp' => null, // Location package may not provide ISP
                ];
            }
        } catch (\Exception $e) {
            // Fall through to default
        }
        
        return [
            'country_code' => null,
            'city' => null,
            'isp' => null,
        ];
    }
    
    /**
     * Check if user agent is a bot
     */
    private function isBot(string $userAgent): bool
    {
        $botPatterns = [
            'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget',
            'python', 'java', 'go-http', 'php', 'ruby', 'perl',
            'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
            'yandexbot', 'sogou', 'exabot', 'facebot', 'ia_archiver',
        ];
        
        $userAgentLower = strtolower($userAgent);
        foreach ($botPatterns as $pattern) {
            if (strpos($userAgentLower, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Generate device fingerprint
     */
    private function generateFingerprint(Request $request): string
    {
        $components = [
            $request->input('screen_width'),
            $request->input('screen_height'),
            $request->input('timezone'),
            $request->input('language'),
            $request->header('User-Agent'),
        ];
        
        return hash('sha256', implode('|', array_filter($components)));
    }
    
    /**
     * Check if visitor is returning
     */
    private function isReturningVisitor(int $siteId, string $fingerprint): bool
    {
        return AnalyticsSession::where('site_id', $siteId)
            ->where('device_fingerprint', $fingerprint)
            ->where('created_at', '<', now()->subHours(24))
            ->exists();
    }
    
    /**
     * Parse UTM parameters from request
     */
    private function parseUtmParams(Request $request): array
    {
        return [
            'utm_source' => $request->input('utm_source'),
            'utm_medium' => $request->input('utm_medium'),
            'utm_campaign' => $request->input('utm_campaign'),
        ];
    }
    
    /**
     * Get network information
     */
    private function getNetworkInfo(Request $request): array
    {
        return [
            'network_type' => $request->input('network_type'),
            'rtt_ms' => $request->input('rtt_ms'),
            'downlink_mbps' => $request->input('downlink_mbps'),
        ];
    }
    
    /**
     * Get screen/viewport information
     */
    private function getScreenInfo(Request $request): array
    {
        return [
            'screen_width' => $request->input('screen_width'),
            'screen_height' => $request->input('screen_height'),
            'viewport_width' => $request->input('viewport_width'),
            'viewport_height' => $request->input('viewport_height'),
            'device_pixel_ratio' => $request->input('device_pixel_ratio'),
        ];
    }
    
    /**
     * Get engagement metrics
     */
    private function getEngagementMetrics(Request $request): array
    {
        return [
            'scroll_percent' => $request->input('scroll_percent', 0),
            'time_spent_ms' => $request->input('time_spent_ms', 0),
            'active_time_ms' => $request->input('active_time_ms', 0),
            'idle_time_ms' => $request->input('idle_time_ms', 0),
        ];
    }
    
    /**
     * Extract referrer source from referrer URL
     * Examples:
     * - https://www.google.com/search?q=... -> Google
     * - https://www.facebook.com/... -> Facebook
     * - https://twitter.com/... -> Twitter
     * - (empty) -> Direct
     * - Same domain as site -> Direct (internal)
     */
    private function extractReferrerSource(?string $referrer, ?string $siteDomain = null): ?string
    {
        if (empty($referrer)) {
            return 'Direct';
        }
        
        try {
            $parsedUrl = parse_url($referrer);
            if (!isset($parsedUrl['host'])) {
                return 'Direct';
            }
            
            $host = strtolower($parsedUrl['host']);
            
            // Remove www. prefix
            $host = preg_replace('/^www\./', '', $host);
            
            // Check if referrer is from the same domain (internal traffic)
            if ($siteDomain) {
                $siteHost = strtolower($siteDomain);
                $siteHost = preg_replace('/^www\./', '', $siteHost);
                
                // Compare domains (exact match or subdomain)
                if ($host === $siteHost || substr($host, -strlen('.' . $siteHost)) === '.' . $siteHost) {
                    return 'Direct'; // Internal traffic is treated as Direct
                }
            }
            
            // Known search engines and social media platforms
            $sources = [
                // Search Engines
                'google.com' => 'Google',
                'google.ae' => 'Google',
                'google.co.uk' => 'Google',
                'google.ca' => 'Google',
                'google.fr' => 'Google',
                'google.de' => 'Google',
                'google.it' => 'Google',
                'google.es' => 'Google',
                'google.com.br' => 'Google',
                'google.com.mx' => 'Google',
                'google.co.jp' => 'Google',
                'google.com.au' => 'Google',
                'google.co.in' => 'Google',
                'google.ru' => 'Google',
                'google.co.za' => 'Google',
                'google.com.tr' => 'Google',
                'google.com.sa' => 'Google',
                'google.com.eg' => 'Google',
                'bing.com' => 'Bing',
                'yahoo.com' => 'Yahoo',
                'duckduckgo.com' => 'DuckDuckGo',
                'yandex.com' => 'Yandex',
                'yandex.ru' => 'Yandex',
                'baidu.com' => 'Baidu',
                'ask.com' => 'Ask',
                
                // Social Media
                'facebook.com' => 'Facebook',
                'fb.com' => 'Facebook',
                'm.facebook.com' => 'Facebook',
                'twitter.com' => 'Twitter',
                'x.com' => 'Twitter',
                'instagram.com' => 'Instagram',
                'linkedin.com' => 'LinkedIn',
                'pinterest.com' => 'Pinterest',
                'reddit.com' => 'Reddit',
                'tiktok.com' => 'TikTok',
                'youtube.com' => 'YouTube',
                'snapchat.com' => 'Snapchat',
                'whatsapp.com' => 'WhatsApp',
                'telegram.org' => 'Telegram',
                'telegram.me' => 'Telegram',
                
                // Other common sources
                't.co' => 'Twitter',
                'bit.ly' => 'Bitly',
                'tinyurl.com' => 'TinyURL',
            ];
            
            // Check exact match first
            if (isset($sources[$host])) {
                return $sources[$host];
            }
            
            // Check if host contains any known domain
            foreach ($sources as $domain => $source) {
                if (strpos($host, $domain) !== false) {
                    return $source;
                }
            }
            
            // If no match, return the domain name (capitalized)
            $parts = explode('.', $host);
            if (count($parts) >= 2) {
                $domainName = $parts[count($parts) - 2];
                return ucfirst($domainName);
            }
            
            return $host;
            
        } catch (\Exception $e) {
            return 'Direct';
        }
    }
    
    /**
     * Extract actual referrer domain (with subdomain) from referrer URL
     * Examples:
     * - https://subdomain.example.com/page -> subdomain.example.com
     * - https://www.google.com/search -> www.google.com
     * - https://example.com -> example.com
     * - (empty) -> null
     * - Same domain as site -> null (internal)
     */
    private function extractReferrerDomain(?string $referrer, ?string $siteDomain = null): ?string
    {
        if (empty($referrer)) {
            return null;
        }
        
        try {
            $parsedUrl = parse_url($referrer);
            if (!isset($parsedUrl['host'])) {
                return null;
            }
            
            $host = strtolower($parsedUrl['host']);
            
            // Check if referrer is from the same domain (internal traffic)
            if ($siteDomain) {
                $siteHost = strtolower($siteDomain);
                $siteHost = preg_replace('/^www\./', '', $siteHost);
                $hostWithoutWww = preg_replace('/^www\./', '', $host);
                
                // Compare domains (exact match or subdomain)
                if ($hostWithoutWww === $siteHost || substr($hostWithoutWww, -strlen('.' . $siteHost)) === '.' . $siteHost) {
                    return null; // Internal traffic - don't store domain
                }
            }
            
            // Return the actual host (with subdomain if exists)
            return $host;
            
        } catch (\Exception $e) {
            return null;
        }
    }
}
