<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Advertisement extends Model
{
    protected $fillable = [
        'name',
        'type',
        'content',
        'url',
        'padding_x',
        'padding_y',
        'interval_period',
        'custom_patterns',
        'priority',
        'is_active',
        'impressions_count',
        'clicks_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'padding_x' => 'integer',
        'padding_y' => 'integer',
        'interval_period' => 'integer',
        'impressions_count' => 'integer',
        'clicks_count' => 'integer',
    ];

    /**
     * Get the sites this advertisement is assigned to
     */
    public function sites()
    {
        return $this->belongsToMany(AnalyticsSite::class, 'advertisement_sites', 'advertisement_id', 'site_id');
    }

    /**
     * Get the countries this advertisement targets
     */
    public function countries()
    {
        return $this->hasMany(AdvertisementCountry::class);
    }

    /**
     * Get the device types this advertisement targets
     */
    public function devices()
    {
        return $this->hasMany(AdvertisementDevice::class);
    }

    /**
     * Get the URL patterns this advertisement targets
     */
    public function urlPatterns()
    {
        return $this->belongsToMany(
            AnalyticsUrlPattern::class, 
            'advertisement_url_patterns', 
            'advertisement_id', 
            'url_pattern_id'
        )->using(AdvertisementUrlPattern::class);
    }

    /**
     * Get the excluded URL patterns
     */
    public function excludedPatterns()
    {
        return $this->belongsToMany(
            AnalyticsUrlPattern::class, 
            'advertisement_excluded_patterns', 
            'advertisement_id', 
            'url_pattern_id'
        )->using(AdvertisementExcludedPattern::class);
    }

    /**
     * Get the selectors for this advertisement
     */
    public function selectors()
    {
        return $this->hasMany(AdvertisementSelector::class);
    }

    /**
     * Get the subdomains for this advertisement
     */
    public function subdomains()
    {
        return $this->hasMany(AdvertisementSubdomain::class);
    }

    /**
     * Get impressions for this advertisement
     */
    public function impressions()
    {
        return $this->hasMany(AdvertisementImpression::class);
    }

    /**
     * Get clicks for this advertisement
     */
    public function clicks()
    {
        return $this->hasMany(AdvertisementClick::class);
    }

    /**
     * Render content based on type
     */
    public function renderContent()
    {
        $content = $this->content;
        
        // For special ad types (pop_from_bottom, pop_from_top, Interstitial), 
        // return ONLY the raw content - JavaScript will handle the structure
        // This prevents nested ads and ensures content is displayed correctly
        if (in_array($this->type, ['pop_from_bottom', 'pop_from_top', 'Interstitial'])) {
            return $content;
        }
        
        $rendered = '';
        $paddingX = $this->padding_x ?? 20;
        $paddingY = $this->padding_y ?? 20;
        $intervalPeriod = $this->interval_period ?? null;

        switch ($this->type) {
            case 'in_content':
                // Regular in-content ad - just return the content as-is
                $rendered = $content;
                break;
                
            default:
                $rendered = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        }

        // Wrap in link if URL is provided (except for special types)
        if ($this->url && !in_array($this->type, ['pop_from_bottom', 'pop_from_top', 'Interstitial'])) {
            $rendered = '<a href="' . htmlspecialchars($this->url, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer" class="ad-link" data-ad-id="' . $this->id . '">' . $rendered . '</a>';
        }

        return $rendered;
    }

    /**
     * Extract YouTube video ID from URL
     */
    private function extractYouTubeId($url)
    {
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
        return $matches[1] ?? '';
    }

    /**
     * Extract Vimeo video ID from URL
     */
    private function extractVimeoId($url)
    {
        preg_match('/vimeo\.com\/(\d+)/', $url, $matches);
        return $matches[1] ?? '';
    }

    /**
     * Get matching advertisements based on criteria (without selector filter)
     */
    public static function getMatchingAdsForSite($siteId, $deviceType, $countryCode, $url, $subdomain)
    {
        $countryPart = $countryCode ? "_{$countryCode}" : '';
        $cacheKey = "ads_matching_{$siteId}_{$deviceType}{$countryPart}_" . md5($url . ($subdomain ?? ''));
        
        // Track this cache key for efficient deletion
        $cacheKeysSetKey = "ads_cache_keys_site_{$siteId}";
        $cacheKeys = Cache::get($cacheKeysSetKey, []);
        if (!in_array($cacheKey, $cacheKeys)) {
            $cacheKeys[] = $cacheKey;
            Cache::put($cacheKeysSetKey, $cacheKeys, 86400); // Store for 24 hours
        }
        
        // Try to use cache tags if available
        $store = Cache::getStore();
        if (method_exists($store, 'tags')) {
            return Cache::tags(['ads_site_' . $siteId])->remember($cacheKey, 300, function () use ($siteId, $deviceType, $countryCode, $url, $subdomain) {
                return self::getMatchingAdsForSiteQuery($siteId, $deviceType, $countryCode, $url, $subdomain);
            });
        }
        
        return Cache::remember($cacheKey, 300, function () use ($siteId, $deviceType, $countryCode, $url, $subdomain) {
            return self::getMatchingAdsForSiteQuery($siteId, $deviceType, $countryCode, $url, $subdomain);
        });
    }
    
    /**
     * Get matching ads query (extracted for reuse)
     */
    private static function getMatchingAdsForSiteQuery($siteId, $deviceType, $countryCode, $url, $subdomain)
    {
            $query = self::where('is_active', true)
                ->whereHas('sites', function ($q) use ($siteId) {
                    $q->where('analytics_sites.id', $siteId);
                });

            // Filter by device type
            $query->where(function ($q) use ($deviceType) {
                $q->whereDoesntHave('devices')
                  ->orWhereHas('devices', function ($q) use ($deviceType) {
                      $q->where('device_type', $deviceType);
                  });
            });

            // Filter by country
            $query->where(function ($q) use ($countryCode) {
                $q->whereDoesntHave('countries')
                  ->orWhereHas('countries', function ($q) use ($countryCode) {
                      $q->where('country_code', $countryCode);
                  });
            });

            // Filter by subdomain
            if ($subdomain) {
                $query->where(function ($q) use ($subdomain) {
                    $q->whereHas('subdomains', function ($q) use ($subdomain) {
                        $q->whereNull('subdomain')->orWhere('subdomain', $subdomain);
                    });
                });
            } else {
                $query->whereHas('subdomains', function ($q) {
                    $q->whereNull('subdomain');
                });
            }

            // Order by priority and created_at
            $ads = $query->orderBy('priority', 'desc')
                ->orderBy('created_at', 'desc')
                ->with(['selectors', 'urlPatterns', 'excludedPatterns'])
                ->get();

            // Filter by URL pattern matching (in PHP for better regex support)
            $urlPath = parse_url($url, PHP_URL_PATH) ?: '/';
            $ads = $ads->filter(function ($ad) use ($urlPath) {
                // If ad has URL patterns, check if any matches
                if ($ad->urlPatterns->count() > 0) {
                    $matches = false;
                    foreach ($ad->urlPatterns as $pattern) {
                        if (self::matchesUrlPatternStatic($urlPath, $pattern->pattern)) {
                            $matches = true;
                            break;
                        }
                    }
                    if (!$matches) {
                        return false;
                    }
                }

                // Check excluded patterns
                if ($ad->excludedPatterns->count() > 0) {
                    foreach ($ad->excludedPatterns as $pattern) {
                        if (self::matchesUrlPatternStatic($urlPath, $pattern->pattern)) {
                            return false; // Excluded
                        }
                    }
                }

                return true;
            });

            return $ads->values();
    }

    /**
     * Get matching advertisements based on criteria
     */
    public static function getMatchingAds($siteId, $deviceType, $countryCode, $url, $subdomain, $selectors)
    {
        $cacheKey = "ads_matching_{$siteId}_{$deviceType}_{$countryCode}_" . md5($url . $subdomain . implode(',', $selectors));
        
        return Cache::remember($cacheKey, 300, function () use ($siteId, $deviceType, $countryCode, $url, $subdomain, $selectors) {
            $query = self::where('is_active', true)
                ->whereHas('sites', function ($q) use ($siteId) {
                    $q->where('analytics_sites.id', $siteId);
                });

            // Filter by device type
            $query->where(function ($q) use ($deviceType) {
                $q->whereDoesntHave('devices')
                  ->orWhereHas('devices', function ($q) use ($deviceType) {
                      $q->where('device_type', $deviceType);
                  });
            });

            // Filter by country
            $query->where(function ($q) use ($countryCode) {
                $q->whereDoesntHave('countries')
                  ->orWhereHas('countries', function ($q) use ($countryCode) {
                      $q->where('country_code', $countryCode);
                  });
            });

            // Filter by URL pattern - get all ads first, then filter in PHP
            // This is more efficient than complex SQL queries with regex

            // Filter by subdomain
            if ($subdomain) {
                $query->where(function ($q) use ($subdomain) {
                    $q->whereHas('subdomains', function ($q) use ($subdomain) {
                        $q->whereNull('subdomain')->orWhere('subdomain', $subdomain);
                    });
                });
            } else {
                $query->whereHas('subdomains', function ($q) {
                    $q->whereNull('subdomain');
                });
            }

            // Filter by selectors
            $predefinedSelectors = config('advertisements.predefined_selectors', []);
            $allSelectors = [];
            foreach ($selectors as $selector) {
                if (isset($predefinedSelectors[$selector])) {
                    $allSelectors[] = $predefinedSelectors[$selector];
                } else {
                    $allSelectors[] = $selector;
                }
            }

            $query->whereHas('selectors', function ($q) use ($allSelectors) {
                $q->whereIn('selector', $allSelectors);
            });

            // Order by priority and created_at
            $ads = $query->orderBy('priority', 'desc')
                ->orderBy('created_at', 'desc')
                ->with(['urlPatterns', 'excludedPatterns'])
                ->get();

            // Filter by URL pattern matching (in PHP for better regex support)
            $urlPath = parse_url($url, PHP_URL_PATH) ?: '/';
            $ads = $ads->filter(function ($ad) use ($urlPath) {
                // If ad has URL patterns, check if any matches
                if ($ad->urlPatterns->count() > 0) {
                    $matches = false;
                    foreach ($ad->urlPatterns as $pattern) {
                        if ($this->matchesUrlPattern($urlPath, $pattern->pattern)) {
                            $matches = true;
                            break;
                        }
                    }
                    if (!$matches) {
                        return false;
                    }
                }

                // Check excluded patterns
                if ($ad->excludedPatterns->count() > 0) {
                    foreach ($ad->excludedPatterns as $pattern) {
                        if ($this->matchesUrlPattern($urlPath, $pattern->pattern)) {
                            return false; // Excluded
                        }
                    }
                }

                return true;
            });

            // Group by selector and return highest priority ad for each selector
            $groupedAds = [];
            foreach ($ads as $ad) {
                foreach ($ad->selectors as $adSelector) {
                    $selectorKey = $adSelector->selector;
                    if (!isset($groupedAds[$selectorKey])) {
                        $groupedAds[$selectorKey] = $ad;
                    }
                }
            }

            return array_values($groupedAds);
        });
    }

    /**
     * Track impression
     */
    public function trackImpression($data)
    {
        $impression = $this->impressions()->create($data);
        
        // Update cache counter
        $this->increment('impressions_count');
        
        return $impression;
    }

    /**
     * Track click
     */
    public function trackClick($data)
    {
        $click = $this->clicks()->create($data);
        
        // Update cache counter
        $this->increment('clicks_count');
        
        return $click;
    }

    /**
     * Match URL pattern with wildcards
     */
    private function matchesUrlPattern($urlPath, $pattern)
    {
        return self::matchesUrlPatternStatic($urlPath, $pattern);
    }

    /**
     * Clear cache for all sites associated with this advertisement
     */
    public function clearAdsCache(): void
    {
        $sites = $this->sites()->get();
        foreach ($sites as $site) {
            $this->clearCacheForSite($site->id);
        }
    }

    /**
     * Clear cache for a specific site
     */
    public static function clearCacheForSite(int $siteId): void
    {
        // Try to use cache tags if available
        $store = Cache::getStore();
        if (method_exists($store, 'tags')) {
            try {
                Cache::tags(['ads_site_' . $siteId])->flush();
                // Also clear the tracking set
                Cache::forget("ads_cache_keys_site_{$siteId}");
                return;
            } catch (\Exception $e) {
                // Fallback to manual clearing
            }
        }
        
        // Use tracked cache keys for efficient deletion
        $cacheKeysSetKey = "ads_cache_keys_site_{$siteId}";
        $cacheKeys = Cache::get($cacheKeysSetKey, []);
        
        // Delete all tracked cache keys
        foreach ($cacheKeys as $cacheKey) {
            Cache::forget($cacheKey);
        }
        
        // Clear the tracking set
        Cache::forget($cacheKeysSetKey);
        
        // Also clear common patterns as fallback (in case some weren't tracked)
        // This ensures we don't miss any cache entries
        $deviceTypes = ['desktop', 'mobile', 'tablet'];
        $commonCountryCodes = [null, 'US', 'GB', 'SA', 'AE', 'EG', 'JO', 'KW', 'QA', 'BH', 'OM', 'FR', 'DE', 'IT', 'ES'];
        $commonUrls = ['/', '/home', '/index', '/about', '/contact', '/products', '/services'];
        $commonSubdomains = [null, 'www', 'api', 'blog', 'shop', 'admin', 'app'];
        
        foreach ($deviceTypes as $deviceType) {
            foreach ($commonCountryCodes as $countryCode) {
                foreach ($commonUrls as $url) {
                    foreach ($commonSubdomains as $subdomain) {
                        $countryPart = $countryCode ? "_{$countryCode}" : '';
                        $cacheKey = "ads_matching_{$siteId}_{$deviceType}{$countryPart}_" . md5($url . ($subdomain ?? ''));
                        Cache::forget($cacheKey);
                    }
                }
            }
        }
    }

    /**
     * Match URL pattern with wildcards (static version)
     */
    private static function matchesUrlPatternStatic($urlPath, $pattern)
    {
        // Normalize paths - remove leading/trailing slashes for comparison
        $urlPath = trim($urlPath, '/');
        $pattern = trim($pattern, '/');
        
        // If pattern is empty or just '*', match everything
        if (empty($pattern) || $pattern === '*') {
            return true;
        }
        
        // Convert pattern to regex by replacing * with .*
        // First, escape all special regex characters
        $escaped = preg_quote($pattern, '/');
        
        // Replace escaped \* back to * (preg_quote escapes * as \*)
        $escaped = str_replace('\\*', '___WILDCARD___', $escaped);
        
        // Now replace our placeholder with regex wildcard .*
        $regex = str_replace('___WILDCARD___', '.*', $escaped);
        
        // Match from start to end
        $regex = '/^' . $regex . '$/';
        
        return preg_match($regex, $urlPath) === 1;
    }

    /**
     * Get custom patterns from this advertisement
     * 
     * @return array Array of custom pattern strings
     */
    public function getCustomPatterns(): array
    {
        if (empty($this->custom_patterns)) {
            return [];
        }
        
        // Split by newlines and filter empty lines
        $patterns = array_filter(
            array_map('trim', explode("\n", $this->custom_patterns)),
            function ($pattern) {
                return !empty($pattern);
            }
        );
        
        return array_values($patterns);
    }

    /**
     * Resolve URL pattern with priority rules for this advertisement
     * 
     * This method combines custom patterns from this advertisement with regular patterns
     * and resolves the best matching pattern using strict priority rules.
     * 
     * @param string $url The URL to match
     * @param array $regularPatterns Array of regular patterns (AnalyticsUrlPattern models or strings)
     * @return string|null The resolved pattern, or null if no match
     */
    public function resolveUrlPattern(string $url, array $regularPatterns = []): ?string
    {
        $customPatterns = $this->getCustomPatterns();
        return AnalyticsUrlPattern::resolveUrlPattern($url, $customPatterns, $regularPatterns);
    }
}

