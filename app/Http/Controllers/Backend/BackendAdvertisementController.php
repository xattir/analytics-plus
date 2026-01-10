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
        $sites = AnalyticsSite::orderBy('title')->get();
        $urlPatterns = AnalyticsUrlPattern::with('site')->orderBy('pattern')->get();
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
                foreach (array_unique($selectors) as $selector) {
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
        $advertisement->load(['sites', 'countries', 'devices', 'urlPatterns', 'excludedPatterns', 'selectors', 'subdomains']);
        $sites = AnalyticsSite::orderBy('title')->get();
        $urlPatterns = AnalyticsUrlPattern::with('site')->orderBy('pattern')->get();
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
                foreach (array_unique($selectors) as $selector) {
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

        // Get stats by date
        $impressionsByDate = DB::table('advertisement_impressions')
            ->where('advertisement_id', $advertisement->id)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        $clicksByDate = DB::table('advertisement_clicks')
            ->where('advertisement_id', $advertisement->id)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        // Get stats by country
        $impressionsByCountry = DB::table('advertisement_impressions')
            ->where('advertisement_id', $advertisement->id)
            ->whereNotNull('country_code')
            ->select('country_code', DB::raw('COUNT(*) as count'))
            ->groupBy('country_code')
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

        return view('admin.advertisements.stats', compact(
            'advertisement',
            'impressionsByDate',
            'clicksByDate',
            'impressionsByCountry',
            'impressionsByDevice'
        ));
    }
}

