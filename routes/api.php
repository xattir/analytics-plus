<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AdvertisementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Analytics tracking endpoint (public, no auth required, CORS enabled)
// Using FormData (simple request) - no OPTIONS preflight needed
// OPTIONS route removed because FormData POST requests are "simple requests" that don't trigger preflight
// If browser still sends OPTIONS (some edge cases), handle it gracefully
Route::options('/analytics/track', function() {
    // Handle OPTIONS preflight request - nginx will add CORS headers
    // Don't add CORS headers here to avoid duplicate headers
    return response('', 204);
})->withoutMiddleware(['throttle:api', 'cors']);

Route::post('/analytics/track', [AnalyticsController::class, 'track']);

// Advertisement endpoints (public, no auth required, CORS enabled)
Route::prefix('ads')->group(function () {
    Route::post('get', [AdvertisementController::class, 'getAds']);
    Route::post('impression', [AdvertisementController::class, 'trackImpression']);
    Route::post('click', [AdvertisementController::class, 'trackClick']);
});
