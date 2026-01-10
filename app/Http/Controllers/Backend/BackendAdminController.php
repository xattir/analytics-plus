<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Backend\BackendAnalyticsController;

class BackendAdminController extends Controller
{
    public function index(Request $request)
    {
        // For all users (including superadmin), show analytics index (my websites) as home page
        // This makes the home page display user's own websites, treating superadmin as normal user
        // Superadmin powers (all websites view) are only available via dropdown menu link
        $analyticsController = new BackendAnalyticsController();
        return $analyticsController->index($request);
    }
       
}
