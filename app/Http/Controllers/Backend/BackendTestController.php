<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Nafezly\Payments\Classes\YallaPayPayment;
use App\Models\AnalyticsSite;
use App\Http\Controllers\Backend\BackendAnalyticsController;

class BackendTestController extends Controller
{
    public function test(Request $request)
    {

       
        
        dd("TEST");
    }
    public function user(Request $request , \App\Models\User $user){
        dd($user);
    }
    
    /**
     * Test URL patterns extraction
     */
    public function index(Request $request)
    {
        // Get site_id from request or use first site
        $siteId = $request->input('site_id');
        $limit = $request->input('limit', 10000);
        
        if (!$siteId) {
            $site = AnalyticsSite::first();
            if (!$site) {
                return response()->json([
                    'error' => 'No sites found. Please provide site_id parameter.',
                ], 404);
            }
            $siteId = $site->id;
        }
        
        // Verify site exists
        $site = AnalyticsSite::find($siteId);
        if (!$site) {
            return response()->json([
                'error' => "Site with ID {$siteId} not found.",
            ], 404);
        }
        
        // Create controller instance and call method
        $analyticsController = new BackendAnalyticsController();
                
        try {
                $patterns = $analyticsController->extractUrlPatternsForSite($siteId, $limit);
            
            return response()->json([
                'success' => true,
                'site_id' => $siteId,
                'site_title' => $site->title,
                'site_domain' => $site->domain,
                'limit' => $limit,
                'patterns_count' => count($patterns),
                'patterns' => $patterns,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to extract URL patterns',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
}
