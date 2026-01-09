<?php

namespace App\Observers;

use App\Models\Advertisement;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AdvertisementObserver
{
    /**
     * Handle the Advertisement "created" event.
     */
    public function created(Advertisement $advertisement): void
    {
        $this->clearAdsCache($advertisement);
    }

    /**
     * Handle the Advertisement "updated" event.
     */
    public function updated(Advertisement $advertisement): void
    {
        $this->clearAdsCache($advertisement);
    }

    /**
     * Handle the Advertisement "deleted" event.
     */
    public function deleted(Advertisement $advertisement): void
    {
        $this->clearAdsCache($advertisement);
    }

    /**
     * Handle the Advertisement "saved" event.
     */
    public function saved(Advertisement $advertisement): void
    {
        $this->clearAdsCache($advertisement);
    }

    /**
     * Clear all ads cache for sites associated with this advertisement
     */
    private function clearAdsCache(Advertisement $advertisement): void
    {
        try {
            // Get all sites associated with this advertisement
            $sites = $advertisement->sites()->get();
            
            if ($sites->isEmpty()) {
                // If no sites, try to get sites from the relationship if it's loaded
                if ($advertisement->relationLoaded('sites')) {
                    $sites = $advertisement->sites;
                }
            }

            // If still no sites, we need to load them
            if ($sites->isEmpty() && $advertisement->exists) {
                $sites = $advertisement->sites()->get();
            }

            foreach ($sites as $site) {
                $this->clearCacheForSite($site->id);
            }
        } catch (\Exception $e) {
            // Silently fail - don't break the application if cache clearing fails
            Log::warning('Failed to clear ads cache for advertisement: ' . $e->getMessage());
        }
    }

    /**
     * Clear all cache entries for a specific site
     */
    private function clearCacheForSite(int $siteId): void
    {
        // Use the static method from Advertisement model
        Advertisement::clearCacheForSite($siteId);
    }
}

