<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\AnalyticsSite;
use App\Models\AnalyticsUrlPattern;
use App\Helpers\UserSystemInfoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdvertisementController extends Controller
{
    /**
     * Get matching advertisements
     */
    public function getAds(Request $request)
    {
        // Disable Debugbar
        if (class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class)) {
            \Barryvdh\Debugbar\Facades\Debugbar::disable();
        }

        try {
            $siteKey = $request->input('site_key');
            if (!$siteKey) {
                return response()->json(['error' => 'site_key is required'], 400);
            }

            $site = AnalyticsSite::where('site_key', $siteKey)->first();
            if (!$site) {
                return response()->json(['ads' => []]);
            }

            $deviceType = $request->input('device_type');
            $countryCode = $request->input('country_code');
            $url = $request->input('url');
            $subdomain = $this->extractSubdomain($request->input('url') ? parse_url($url, PHP_URL_HOST) : '');
            $selectors = $request->input('selectors', []);

            if (empty($selectors)) {
                return response()->json(['ads' => []]);
            }

            // Get matching ads
            $ads = Advertisement::getMatchingAds(
                $site->id,
                $deviceType,
                $countryCode,
                $url,
                $subdomain,
                $selectors
            );

            // Render content and prepare response
            $result = [];
            foreach ($ads as $ad) {
                // Get the selector for this ad
                $adSelectors = $ad->selectors->pluck('selector')->toArray();
                $matchingSelector = null;
                foreach ($selectors as $selector) {
                    $predefinedSelectors = config('advertisements.predefined_selectors', []);
                    $cssSelector = isset($predefinedSelectors[$selector]) ? $predefinedSelectors[$selector] : $selector;
                    if (in_array($cssSelector, $adSelectors)) {
                        $matchingSelector = $cssSelector;
                        break;
                    }
                }

                if ($matchingSelector) {
                    // Find matching URL pattern
                    $urlPatternId = null;
                    if ($url) {
                        $urlPath = parse_url($url, PHP_URL_PATH);
                        $pattern = $ad->urlPatterns()->first(function ($pattern) use ($urlPath) {
                            return $this->matchesUrlPattern($urlPath, $pattern->pattern);
                        });
                        if ($pattern) {
                            $urlPatternId = $pattern->id;
                        }
                    }

                    $result[] = [
                        'id' => $ad->id,
                        'selector' => $matchingSelector,
                        'content' => $ad->renderContent(),
                        'url' => $ad->url,
                        'url_pattern_id' => $urlPatternId,
                    ];
                }
            }

            return response()->json(['ads' => $result], 200, [
                'Content-Type' => 'application/json',
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Track impression
     */
    public function trackImpression(Request $request)
    {
        if (class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class)) {
            \Barryvdh\Debugbar\Facades\Debugbar::disable();
        }

        try {
            $siteKey = $request->input('site_key');
            $adId = $request->input('ad_id');
            $sessionId = $request->input('session_id');
            $url = $request->input('url');
            $selector = $request->input('selector');
            $urlPatternId = $request->input('url_pattern_id');

            if (!$siteKey || !$adId) {
                return response()->json(['success' => false], 400);
            }

            $site = AnalyticsSite::where('site_key', $siteKey)->first();
            if (!$site) {
                return response()->json(['success' => false], 404);
            }

            $ad = Advertisement::find($adId);
            if (!$ad) {
                return response()->json(['success' => false], 404);
            }

            // Detect device type and country from request
            $deviceType = $this->detectDeviceType($request);
            $countryCode = $this->getCountryCode($request);

            $ad->trackImpression([
                'site_id' => $site->id,
                'session_id' => $sessionId,
                'device_type' => $deviceType,
                'country_code' => $countryCode,
                'url_pattern_id' => $urlPatternId,
                'selector' => $selector,
                'ip' => UserSystemInfoHelper::get_ip(),
                'user_agent' => $request->header('User-Agent'),
                'url' => $url,
            ]);

            return response()->json(['success' => true], 200, [
                'Content-Type' => 'application/json',
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Track click
     */
    public function trackClick(Request $request)
    {
        if (class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class)) {
            \Barryvdh\Debugbar\Facades\Debugbar::disable();
        }

        try {
            $siteKey = $request->input('site_key');
            $adId = $request->input('ad_id');
            $sessionId = $request->input('session_id');
            $url = $request->input('url');
            $selector = $request->input('selector');
            $urlPatternId = $request->input('url_pattern_id');

            if (!$siteKey || !$adId) {
                return response()->json(['success' => false], 400);
            }

            $site = AnalyticsSite::where('site_key', $siteKey)->first();
            if (!$site) {
                return response()->json(['success' => false], 404);
            }

            $ad = Advertisement::find($adId);
            if (!$ad) {
                return response()->json(['success' => false], 404);
            }

            // Detect device type and country from request
            $deviceType = $this->detectDeviceType($request);
            $countryCode = $this->getCountryCode($request);

            $ad->trackClick([
                'site_id' => $site->id,
                'session_id' => $sessionId,
                'device_type' => $deviceType,
                'country_code' => $countryCode,
                'url_pattern_id' => $urlPatternId,
                'selector' => $selector,
                'ip' => UserSystemInfoHelper::get_ip(),
                'user_agent' => $request->header('User-Agent'),
                'url' => $url,
            ]);

            return response()->json(['success' => true], 200, [
                'Content-Type' => 'application/json',
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Extract subdomain from hostname
     */
    private function extractSubdomain($hostname)
    {
        if (!$hostname) {
            return null;
        }

        $parts = explode('.', $hostname);
        if (count($parts) > 2) {
            return $parts[0];
        }
        return null;
    }

    /**
     * Match URL pattern with wildcards
     */
    private function matchesUrlPattern($urlPath, $pattern)
    {
        // Convert pattern wildcard (*) to regex
        $regex = str_replace(['*', '/'], ['.*', '\/'], $pattern);
        $regex = '/^' . $regex . '$/';
        return preg_match($regex, $urlPath);
    }

    /**
     * Detect device type from request
     */
    private function detectDeviceType(Request $request)
    {
        $userAgent = $request->header('User-Agent', '');
        $deviceType = $request->input('device_type');

        if ($deviceType) {
            return $deviceType;
        }

        // Fallback to user agent detection
        if (preg_match('/mobile|android|iphone|ipad/i', $userAgent)) {
            if (preg_match('/tablet|ipad/i', $userAgent)) {
                return 'tablet';
            }
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Get country code from request
     */
    private function getCountryCode(Request $request)
    {
        $countryCode = $request->input('country_code');
        if ($countryCode) {
            return $countryCode;
        }

        // Try to get from IP
        $ip = UserSystemInfoHelper::get_ip();
        if ($ip && $ip !== 'UNKNOWN') {
            try {
                $location = \Stevebauman\Location\Facades\Location::get($ip);
                if ($location && $location->countryCode) {
                    return $location->countryCode;
                }
            } catch (\Exception $e) {
                // Ignore
            }
        }

        return null;
    }
}

