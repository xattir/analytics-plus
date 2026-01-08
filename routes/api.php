<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AnalyticsController;

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
// Handle OPTIONS separately with minimal middleware to avoid large headers (nginx buffer limit)
Route::options('/analytics/track', function() {
    // Explicitly disable Debugbar to prevent large headers
    if (class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class)) {
        \Barryvdh\Debugbar\Facades\Debugbar::disable();
    }
    
    return response('', 204)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'POST,OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type')
        ->header('Access-Control-Max-Age', '86400');
})->withoutMiddleware(['throttle:api', 'cors']);

Route::post('/analytics/track', [AnalyticsController::class, 'track']);
