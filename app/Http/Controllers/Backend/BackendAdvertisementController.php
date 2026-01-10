<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\AnalyticsSite;
use App\Models\AnalyticsUrlPattern;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BackendAdvertisementController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:advertisements-create', ['only' => ['create', 'store']]);
        $this->middleware('can:advertisements-read', ['only' => ['show', 'index', 'stats']]);
        $this->middleware('can:advertisements-update', ['only' => ['edit', 'update']]);
        $this->middleware('can:advertisements-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $advertisements = Advertisement::where(function ($q) use ($request) {
            if ($request->id != null) {
                $q->where('id', $request->id);
            }
            if ($request->q != null) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            }
            if ($request->is_active !== null) {
                $q->where('is_active', $request->is_active);
            }
        })->orderBy('id', 'DESC')->paginate(20);

        return view('admin.advertisements.index', compact('advertisements'));
    }

    public function create()
    {
        $user = auth()->user();
        
        // Filter sites: show only user-owned or member sites (treat superadmin as normal user)
        if ($user->hasRole('superadmin')) {
            $sites = AnalyticsSite::where('user_id', $user->id)->orderBy('title')->get();
        } else {
            $sites = AnalyticsSite::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhereHas('members', function($q) use ($user) {
                          $q->where('user_id', $user->id);
                      });
            })->orderBy('title')->get();
        }
        
        // Filter URL patterns to only show patterns for user's sites
        $siteIds = $sites->pluck('id');
        $urlPatterns = AnalyticsUrlPattern::whereIn('site_id', $siteIds)->with('site')->orderBy('pattern')->get();
        
        $countries = config('countries', []);
        $predefinedSelectors = config('advertisements.predefined_selectors', []);

        return view('admin.advertisements.create', compact('sites', 'urlPatterns', 'countries', 'predefinedSelectors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:in_content,pop_from_bottom,pop_from_top,Interstitial',
            'content' => 'required|string',
            'url' => 'nullable|url|max:2048',
            'padding_x' => 'nullable|integer|min:0|max:100',
            'padding_y' => 'nullable|integer|min:0|max:100',
            'interval_period' => 'nullable|integer|min:0',
            'priority' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'site_ids' => 'nullable|array',
            'site_ids.*' => 'exists:analytics_sites,id',
            'country_codes' => 'nullable|array',
            'country_codes.*' => 'string|size:2',
            'device_types' => 'nullable|array',
            'device_types.*' => 'in:desktop,mobile,tablet',
            'url_pattern_ids' => 'nullable|array',
            'url_pattern_ids.*' => 'exists:analytics_url_patterns,id',
            'excluded_pattern_ids' => 'nullable|array',
            'excluded_pattern_ids.*' => 'exists:analytics_url_patterns,id',
            'predefined_selectors' => 'nullable|array',
            'custom_selectors' => 'nullable|string',
            'custom_patterns' => 'nullable|string',
            'subdomains' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Prepare custom_patterns text
            $customPatternsText = null;
            if ($request->custom_patterns) {
                $customPatterns = array_filter(array_map('trim', explode("\n", $request->custom_patterns)));
                $customPatternsText = implode("\n", $customPatterns);
            }
            
            $advertisement = Advertisement::create([
                'name' => $request->name,
                'type' => $request->type,
                'content' => $request->content,
                'url' => $request->url,
                'open_in_new_tab' => $request->open_in_new_tab ?? true,
                'padding_x' => $request->padding_x ?? 20,
                'padding_y' => $request->padding_y ?? 20,
                'interval_period' => $request->interval_period,
                'custom_patterns' => $customPatternsText,
                'priority' => $request->priority ?? 0,
                'is_active' => $request->is_active ?? true,
            ]);

            // Attach sites
            if ($request->site_ids) {
                $advertisement->sites()->sync($request->site_ids);
            }

            // Attach countries
            if ($request->country_codes) {
                foreach ($request->country_codes as $countryCode) {
                    $advertisement->countries()->create(['country_code' => $countryCode]);
                }
            }

            // Attach devices
            if ($request->device_types) {
                foreach ($request->device_types as $deviceType) {
                    $advertisement->devices()->create(['device_type' => $deviceType]);
                }
            }

            // Attach URL patterns
            $urlPatternIds = $request->url_pattern_ids ?? [];
            
            // Handle custom patterns - create URL patterns for matching
            if ($request->custom_patterns) {
                $customPatterns = array_filter(array_map('trim', explode("\n", $request->custom_patterns)));
                
                // Create or get URL pattern for each site for matching
                foreach ($customPatterns as $pattern) {
                    if (!empty($pattern)) {
                        foreach ($advertisement->sites as $site) {
                            $urlPattern = \App\Models\AnalyticsUrlPattern::firstOrCreate([
                                'site_id' => $site->id,
                                'domain' => parse_url($site->domain, PHP_URL_HOST) ?: $site->domain,
                                'pattern' => $pattern,
                            ], [
                                'generated_at' => now(),
                            ]);
                            if (!in_array($urlPattern->id, $urlPatternIds)) {
                                $urlPatternIds[] = $urlPattern->id;
                            }
                        }
                    }
                }
            }
            
            if (!empty($urlPatternIds)) {
                $advertisement->urlPatterns()->sync($urlPatternIds);
            }

            // Attach excluded patterns
            if ($request->excluded_pattern_ids) {
                $advertisement->excludedPatterns()->sync($request->excluded_pattern_ids);
            }
            
            // Clear cache after syncing relationships
            $advertisement->clearAdsCache();

            // Attach selectors (only for in_content type)
            $specialTypes = ['pop_from_bottom', 'pop_from_top', 'Interstitial'];
            if (!in_array($advertisement->type, $specialTypes)) {
                $selectors = [];
                if ($request->predefined_selectors) {
                    $predefinedSelectors = config('advertisements.predefined_selectors', []);
                    foreach ($request->predefined_selectors as $tag) {
                        if (isset($predefinedSelectors[$tag])) {
                            $selectors[] = $predefinedSelectors[$tag];
                        } else {
                            $selectors[] = $tag; // Use as-is if not found
                        }
                    }
                }
                if ($request->custom_selectors) {
                    $customSelectors = array_filter(array_map('trim', explode("\n", $request->custom_selectors)));
                    $selectors = array_merge($selectors, $customSelectors);
                }
                // Filter out empty selectors before creating
                foreach (array_unique(array_filter($selectors, function($selector) {
                    return !empty($selector) && trim($selector) !== '';
                })) as $selector) {
                    $advertisement->selectors()->create(['selector' => $selector]);
                }
            }

            // Attach subdomains
            if ($request->subdomains) {
                $subdomains = array_filter(array_map('trim', explode(',', $request->subdomains)));
                foreach ($subdomains as $subdomain) {
                    $advertisement->subdomains()->create(['subdomain' => $subdomain ?: null]);
                }
            } else {
                // If no subdomains specified, create one with null (all subdomains)
                $advertisement->subdomains()->create(['subdomain' => null]);
            }

            DB::commit();
            flash()->success('تم إنشاء الإعلان بنجاح');
            return redirect()->route('admin.advertisements.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error('حدث خطأ أثناء إنشاء الإعلان: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function show(Advertisement $advertisement)
    {
        $advertisement->load(['sites', 'countries', 'devices', 'urlPatterns', 'excludedPatterns', 'selectors', 'subdomains']);
        return view('admin.advertisements.show', compact('advertisement'));
    }

    public function edit(Advertisement $advertisement)
    {
        $user = auth()->user();
        $advertisement->load(['sites', 'countries', 'devices', 'urlPatterns', 'excludedPatterns', 'selectors', 'subdomains']);
        
        // Filter sites: show only user-owned or member sites (treat superadmin as normal user)
        if ($user->hasRole('superadmin')) {
            $sites = AnalyticsSite::where('user_id', $user->id)->orderBy('title')->get();
        } else {
            $sites = AnalyticsSite::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhereHas('members', function($q) use ($user) {
                          $q->where('user_id', $user->id);
                      });
            })->orderBy('title')->get();
        }
        
        // Filter URL patterns to only show patterns for user's sites
        $siteIds = $sites->pluck('id');
        $urlPatterns = AnalyticsUrlPattern::whereIn('site_id', $siteIds)->with('site')->orderBy('pattern')->get();
        
        $countries = config('countries', []);
        $predefinedSelectors = config('advertisements.predefined_selectors', []);

        // Get current predefined selector tags
        $currentPredefinedTags = [];
        $predefinedSelectorsMap = array_flip($predefinedSelectors);
        foreach ($advertisement->selectors as $selector) {
            if (isset($predefinedSelectorsMap[$selector->selector])) {
                $currentPredefinedTags[] = $predefinedSelectorsMap[$selector->selector];
            }
        }

        // Get current custom selectors
        $currentCustomSelectors = [];
        foreach ($advertisement->selectors as $selector) {
            if (!isset($predefinedSelectorsMap[$selector->selector])) {
                $currentCustomSelectors[] = $selector->selector;
            }
        }

        // Get current custom patterns from custom_patterns field
        $currentCustomPatterns = $advertisement->getCustomPatterns();

        return view('admin.advertisements.edit', compact(
            'advertisement',
            'sites',
            'urlPatterns',
            'countries',
            'predefinedSelectors',
            'currentPredefinedTags',
            'currentCustomSelectors',
            'currentCustomPatterns'
        ));
    }

    public function update(Request $request, Advertisement $advertisement)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:in_content,pop_from_bottom,pop_from_top,Interstitial',
            'content' => 'required|string',
            'url' => 'nullable|url|max:2048',
            'padding_x' => 'nullable|integer|min:0|max:100',
            'padding_y' => 'nullable|integer|min:0|max:100',
            'interval_period' => 'nullable|integer|min:0',
            'priority' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'site_ids' => 'nullable|array',
            'site_ids.*' => 'exists:analytics_sites,id',
            'country_codes' => 'nullable|array',
            'country_codes.*' => 'string|size:2',
            'device_types' => 'nullable|array',
            'device_types.*' => 'in:desktop,mobile,tablet',
            'url_pattern_ids' => 'nullable|array',
            'url_pattern_ids.*' => 'exists:analytics_url_patterns,id',
            'excluded_pattern_ids' => 'nullable|array',
            'excluded_pattern_ids.*' => 'exists:analytics_url_patterns,id',
            'predefined_selectors' => 'nullable|array',
            'custom_selectors' => 'nullable|string',
            'custom_patterns' => 'nullable|string',
            'subdomains' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Prepare custom_patterns text
            $customPatternsText = null;
            if ($request->custom_patterns) {
                $customPatterns = array_filter(array_map('trim', explode("\n", $request->custom_patterns)));
                $customPatternsText = implode("\n", $customPatterns);
            }
            
            $advertisement->update([
                'name' => $request->name,
                'type' => $request->type,
                'content' => $request->content,
                'url' => $request->url,
                'open_in_new_tab' => $request->has('open_in_new_tab') ? ($request->open_in_new_tab == '1' || $request->open_in_new_tab == 1) : ($advertisement->open_in_new_tab ?? true),
                'padding_x' => $request->padding_x ?? 20,
                'padding_y' => $request->padding_y ?? 20,
                'interval_period' => $request->interval_period,
                'custom_patterns' => $customPatternsText,
                'priority' => $request->priority ?? 0,
                'is_active' => $request->is_active ?? true,
            ]);

            // Sync sites
            if ($request->has('site_ids')) {
                $advertisement->sites()->sync($request->site_ids ?? []);
            }

            // Sync countries
            $advertisement->countries()->delete();
            if ($request->country_codes) {
                foreach ($request->country_codes as $countryCode) {
                    $advertisement->countries()->create(['country_code' => $countryCode]);
                }
            }

            // Sync devices
            $advertisement->devices()->delete();
            if ($request->device_types) {
                foreach ($request->device_types as $deviceType) {
                    $advertisement->devices()->create(['device_type' => $deviceType]);
                }
            }

            // Sync URL patterns
            $urlPatternIds = $request->url_pattern_ids ?? [];
            
            // Handle custom patterns - create URL patterns for matching
            if ($request->custom_patterns) {
                $customPatterns = array_filter(array_map('trim', explode("\n", $request->custom_patterns)));
                
                // Create or get URL pattern for each site for matching
                foreach ($customPatterns as $pattern) {
                    if (!empty($pattern)) {
                        foreach ($advertisement->sites as $site) {
                            $urlPattern = \App\Models\AnalyticsUrlPattern::firstOrCreate([
                                'site_id' => $site->id,
                                'domain' => parse_url($site->domain, PHP_URL_HOST) ?: $site->domain,
                                'pattern' => $pattern,
                            ], [
                                'generated_at' => now(),
                            ]);
                            if (!in_array($urlPattern->id, $urlPatternIds)) {
                                $urlPatternIds[] = $urlPattern->id;
                            }
                        }
                    }
                }
            }
            
            if ($request->has('url_pattern_ids') || $request->custom_patterns) {
                $advertisement->urlPatterns()->sync($urlPatternIds);
            }

            // Sync excluded patterns
            if ($request->has('excluded_pattern_ids')) {
                $advertisement->excludedPatterns()->sync($request->excluded_pattern_ids ?? []);
            }
            
            // Clear cache after syncing relationships
            $advertisement->clearAdsCache();

            // Sync selectors (only for in_content type)
            $specialTypes = ['pop_from_bottom', 'pop_from_top', 'Interstitial'];
            if (!in_array($advertisement->type, $specialTypes)) {
                $advertisement->selectors()->delete();
                $selectors = [];
                if ($request->predefined_selectors) {
                    $predefinedSelectors = config('advertisements.predefined_selectors', []);
                    foreach ($request->predefined_selectors as $tag) {
                        if (isset($predefinedSelectors[$tag])) {
                            $selectors[] = $predefinedSelectors[$tag];
                        } else {
                            $selectors[] = $tag;
                        }
                    }
                }
                if ($request->custom_selectors) {
                    $customSelectors = array_filter(array_map('trim', explode("\n", $request->custom_selectors)));
                    $selectors = array_merge($selectors, $customSelectors);
                }
                // Filter out empty selectors before creating
                foreach (array_unique(array_filter($selectors, function($selector) {
                    return !empty($selector) && trim($selector) !== '';
                })) as $selector) {
                    $advertisement->selectors()->create(['selector' => $selector]);
                }
            } else {
                // Remove selectors for special types
                $advertisement->selectors()->delete();
            }

            // Sync subdomains
            $advertisement->subdomains()->delete();
            if ($request->subdomains) {
                $subdomains = array_filter(array_map('trim', explode(',', $request->subdomains)));
                foreach ($subdomains as $subdomain) {
                    $advertisement->subdomains()->create(['subdomain' => $subdomain ?: null]);
                }
            } else {
                $advertisement->subdomains()->create(['subdomain' => null]);
            }
            
            // Clear cache after all syncing is done
            $advertisement->clearAdsCache();

            DB::commit();
            flash()->success('تم تحديث الإعلان بنجاح');
            return redirect()->route('admin.advertisements.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error('حدث خطأ أثناء تحديث الإعلان: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function destroy(Advertisement $advertisement)
    {
        $advertisement->delete();
        flash()->success('تم حذف الإعلان بنجاح');
        return redirect()->route('admin.advertisements.index');
    }

    public function stats(Advertisement $advertisement)
    {
        $advertisement->load(['sites', 'impressions', 'clicks']);

        // Get clicks for last 30 minutes (bars chart)
        $thirtyMinutesAgo = now()->subMinutes(30);
        $clicksLast30Minutes = DB::table('advertisement_clicks')
            ->where('advertisement_id', $advertisement->id)
            ->where('created_at', '>=', $thirtyMinutesAgo)
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i") as minute'), DB::raw('COUNT(*) as count'))
            ->groupBy('minute')
            ->orderBy('minute', 'asc')
            ->get();

        // Get stats by date (last 30 days)
        $impressionsByDate = DB::table('advertisement_impressions')
            ->where('advertisement_id', $advertisement->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $clicksByDate = DB::table('advertisement_clicks')
            ->where('advertisement_id', $advertisement->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Get top sites by clicks
        $topSitesByClicks = DB::table('advertisement_clicks')
            ->join('analytics_sites', 'advertisement_clicks.site_id', '=', 'analytics_sites.id')
            ->where('advertisement_clicks.advertisement_id', $advertisement->id)
            ->select('analytics_sites.title', 'analytics_sites.domain', DB::raw('COUNT(*) as clicks_count'))
            ->groupBy('analytics_sites.id', 'analytics_sites.title', 'analytics_sites.domain')
            ->orderBy('clicks_count', 'desc')
            ->limit(10)
            ->get();

        // Get top sites by impressions
        $topSitesByImpressions = DB::table('advertisement_impressions')
            ->join('analytics_sites', 'advertisement_impressions.site_id', '=', 'analytics_sites.id')
            ->where('advertisement_impressions.advertisement_id', $advertisement->id)
            ->select('analytics_sites.title', 'analytics_sites.domain', DB::raw('COUNT(*) as impressions_count'))
            ->groupBy('analytics_sites.id', 'analytics_sites.title', 'analytics_sites.domain')
            ->orderBy('impressions_count', 'desc')
            ->limit(10)
            ->get();

        // Get top countries by clicks
        $topCountriesByClicks = DB::table('advertisement_clicks')
            ->where('advertisement_id', $advertisement->id)
            ->whereNotNull('country_code')
            ->select('country_code', DB::raw('COUNT(*) as count'))
            ->groupBy('country_code')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Get top countries by impressions
        $topCountriesByImpressions = DB::table('advertisement_impressions')
            ->where('advertisement_id', $advertisement->id)
            ->whereNotNull('country_code')
            ->select('country_code', DB::raw('COUNT(*) as count'))
            ->groupBy('country_code')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Get top pages/URLs by clicks
        $topPagesByClicks = DB::table('advertisement_clicks')
            ->where('advertisement_id', $advertisement->id)
            ->whereNotNull('url')
            ->select('url', DB::raw('COUNT(*) as count'))
            ->groupBy('url')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Get top pages/URLs by impressions
        $topPagesByImpressions = DB::table('advertisement_impressions')
            ->where('advertisement_id', $advertisement->id)
            ->whereNotNull('url')
            ->select('url', DB::raw('COUNT(*) as count'))
            ->groupBy('url')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Get stats by device
        $impressionsByDevice = DB::table('advertisement_impressions')
            ->where('advertisement_id', $advertisement->id)
            ->whereNotNull('device_type')
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->orderBy('count', 'desc')
            ->get();

        $clicksByDevice = DB::table('advertisement_clicks')
            ->where('advertisement_id', $advertisement->id)
            ->whereNotNull('device_type')
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->orderBy('count', 'desc')
            ->get();

        // Get top browsers by clicks - extract from user_agent
        $clicksWithUserAgent = DB::table('advertisement_clicks')
            ->where('advertisement_id', $advertisement->id)
            ->whereNotNull('user_agent')
            ->select('user_agent')
            ->get();
        
        $browserCounts = [];
        $browser_array = [
            '/msie/i'  => 'Internet Explorer',
            '/Trident/i'  => 'Internet Explorer',
            '/firefox/i'  => 'Firefox',
            '/safari/i'  => 'Safari',
            '/chrome/i'  => 'Chrome',
            '/edge/i'  => 'Edge',
            '/opera/i'  => 'Opera',
            '/netscape/'  => 'Netscape',
            '/maxthon/i'  => 'Maxthon',
            '/knoqueror/i'  => 'Konqueror',
            '/ubrowser/i'  => 'UC Browser',
            '/mobile/i'  => 'Safari Browser',
        ];
        
        foreach ($clicksWithUserAgent as $click) {
            $browser = "Unknown Browser";
            foreach($browser_array as $regex => $value){
                if(preg_match($regex, $click->user_agent)){
                    $browser = $value;
                }
            }
            
            if ($browser && $browser !== 'Unknown Browser') {
                $browserCounts[$browser] = ($browserCounts[$browser] ?? 0) + 1;
            }
        }
        arsort($browserCounts);
        $topBrowsers = collect(array_slice($browserCounts, 0, 10, true))->map(function ($count, $browser) {
            return (object)['browser' => $browser, 'count' => $count];
        });

        // Get top operating systems by clicks - extract from user_agent
        $osCounts = [];
        $os_array = [
            '/windows nt 10/i'  => 'Windows 10',
            '/windows nt 6.3/i'  => 'Windows 8.1',
            '/windows nt 6.2/i'  => 'Windows 8',
            '/windows nt 6.1/i'  => 'Windows 7',
            '/windows nt 6.0/i'  => 'Windows Vista',
            '/windows nt 5.2/i'  => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'  => 'Windows XP',
            '/windows xp/i'  => 'Windows XP',
            '/windows nt 5.0/i'  => 'Windows 2000',
            '/windows me/i'  => 'Windows ME',
            '/win98/i'  => 'Windows 98',
            '/win95/i'  => 'Windows 95',
            '/win16/i'  => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i'  => 'Mac OS 9',
            '/linux/i'  => 'Linux',
            '/ubuntu/i'  => 'Ubuntu',
            '/iphone/i'  => 'iPhone',
            '/ipod/i'  => 'iPod',
            '/ipad/i'  => 'iPad',
            '/android/i'  => 'Android',
            '/blackberry/i'  => 'BlackBerry',
            '/webos/i'  => 'Mobile',
        ];
        
        foreach ($clicksWithUserAgent as $click) {
            $os = "Unknown OS Platform";
            foreach ($os_array as $regex => $value){
                if(preg_match($regex, $click->user_agent)){
                    $os = $value;
                }
            }
            
            if ($os && $os !== 'Unknown OS Platform') {
                $osCounts[$os] = ($osCounts[$os] ?? 0) + 1;
            }
        }
        arsort($osCounts);
        $topOperatingSystems = collect(array_slice($osCounts, 0, 10, true))->map(function ($count, $os) {
            return (object)['operating_system' => $os, 'count' => $count];
        });

        $countries = config('countries', []);

        return view('admin.advertisements.stats', compact(
            'advertisement',
            'clicksLast30Minutes',
            'impressionsByDate',
            'clicksByDate',
            'topSitesByClicks',
            'topSitesByImpressions',
            'topCountriesByClicks',
            'topCountriesByImpressions',
            'topPagesByClicks',
            'topPagesByImpressions',
            'impressionsByDevice',
            'clicksByDevice',
            'topBrowsers',
            'topOperatingSystems',
            'countries'
        ));
    }
}

