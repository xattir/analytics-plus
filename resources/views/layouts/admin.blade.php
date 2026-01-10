<!DOCTYPE html>
<html lang="ar">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite('resources/css/dashboard.css')
    

    <style type="text/css">
        html{
            --background-0: #eef4f5;
            --background-1: #fff;
            --background-aside: #11233b;
            --background-active-link: #141e2e;
            --background-form-control-focus: #161d26;
            --color-1: #fff;
            --color-2: #575f66;
            --border-color: #f1f1f1;
            --bs-table-hover-color: #f7f7f7!important; 
        }
        
        /* Modern Aside Styles */
        .aside {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%) !important;
            border-left: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.04), 0 0 1px rgba(0, 0, 0, 0.08) !important;
        }
        
        .aside-menu {
            padding: 8px 12px !important;
        }
        
        .aside-menu::-webkit-scrollbar {
            width: 6px;
        }
        
        .aside-menu::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .aside-menu::-webkit-scrollbar-thumb {
            background: rgba(123, 96, 251, 0.2);
            border-radius: 10px;
        }
        
        .aside-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(123, 96, 251, 0.4);
        }
        
        .aside .user-profile-section {
            padding: 24px 16px;
            text-align: center;
            background: linear-gradient(135deg, rgba(123, 96, 251, 0.05) 0%, rgba(123, 96, 251, 0.02) 100%);
            border-bottom: 1px solid rgba(123, 96, 251, 0.1);
            margin-bottom: 8px;
        }
        
        .aside .user-profile-section img {
            width: 72px !important;
            height: 72px !important;
            border-radius: 50% !important;
            border: 3px solid rgba(123, 96, 251, 0.15);
            box-shadow: 0 4px 12px rgba(123, 96, 251, 0.15);
            transition: all 0.3s ease;
        }
        
        .aside .user-profile-section img:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(123, 96, 251, 0.25);
        }
        
        .aside .user-profile-section .user-name {
            color: #1f2937 !important;
            font-weight: 600;
            font-size: 15px;
            margin-top: 12px;
            margin-bottom: 0;
            text-align: center;
        }
        
        .aside .item-container {
            padding: 10px 12px !important;
            margin: 4px 0;
            border-radius: 10px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .aside .item-container::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, #7b60fb 0%, #667eea 100%);
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        
        .aside .item-container:hover {
            background: linear-gradient(135deg, rgba(123, 96, 251, 0.08) 0%, rgba(123, 96, 251, 0.04) 100%) !important;
            transform: translateX(-2px);
            border-color: rgba(123, 96, 251, 0.15);
            box-shadow: 0 2px 8px rgba(123, 96, 251, 0.1);
        }
        
        .aside .item-container:hover::before {
            opacity: 1;
        }
        
        .aside .item-container.active {
            background: linear-gradient(135deg, rgba(123, 96, 251, 0.12) 0%, rgba(123, 96, 251, 0.06) 100%) !important;
            border-color: rgba(123, 96, 251, 0.2);
            box-shadow: 0 4px 12px rgba(123, 96, 251, 0.15);
        }
        
        .aside .item-container.active::before {
            opacity: 1;
        }
        
        .aside .item-container.active * {
            color: #7b60fb !important;
            font-weight: 600;
        }
        
        .aside .item-container .item-container-title {
            font-size: 14px !important;
            font-weight: 500;
            color: #374151;
            transition: color 0.2s ease;
        }
        
        .aside .item-container:hover .item-container-title {
            color: #7b60fb;
        }
        
        .aside .item-container span[class*="fa"] {
            font-size: 18px;
            color: #6b7280;
            transition: all 0.25s ease;
        }
        
        .aside .item-container:hover span[class*="fa"] {
            color: #7b60fb;
            transform: scale(1.1);
        }
        
        .aside .item-container.active span[class*="fa"] {
            color: #7b60fb !important;
        }
        
        .aside .sites-section {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
        }
        
        .aside .sites-section-title {
            color: #6b7280 !important;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 12px 8px;
            margin-bottom: 4px;
        }
        
        .aside .site-item {
            padding: 6px 10px !important;
            margin: 2px 0;
            border-radius: 8px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
            min-height: auto;
        }
        
        .aside .site-item:hover {
            background: linear-gradient(135deg, rgba(123, 96, 251, 0.08) 0%, rgba(123, 96, 251, 0.04) 100%) !important;
            transform: translateX(-2px);
            border-color: rgba(123, 96, 251, 0.15);
        }
        
        .aside .site-item.active {
            background: linear-gradient(135deg, rgba(123, 96, 251, 0.12) 0%, rgba(123, 96, 251, 0.06) 100%) !important;
            border-color: rgba(123, 96, 251, 0.2);
        }
        
        .aside .site-item img {
            width: 18px !important;
            height: 18px !important;
            border-radius: 4px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .aside .site-item-title {
            font-size: 12px;
            font-weight: 500;
            color: #374151;
            line-height: 1.3;
        }
        
        .aside .site-item-domain {
            font-size: 10px;
            color: #9ca3af;
            margin-top: 1px;
            line-height: 1.2;
        }
        
        .aside .add-site-button {
            background: linear-gradient(135deg, rgba(123, 96, 251, 0.1) 0%, rgba(123, 96, 251, 0.05) 100%) !important;
            border: 2px dashed rgba(123, 96, 251, 0.3) !important;
            border-radius: 8px !important;
            padding: 8px 10px !important;
            margin-top: 6px;
            transition: all 0.25s ease;
            font-size: 12px;
        }
        
        .aside .add-site-button:hover {
            background: linear-gradient(135deg, rgba(123, 96, 251, 0.15) 0%, rgba(123, 96, 251, 0.08) 100%) !important;
            border-color: rgba(123, 96, 251, 0.5) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(123, 96, 251, 0.2);
        }
        
        .aside .add-site-button * {
            color: #7b60fb !important;
            font-weight: 600 !important;
        }
        
        /* Modern Top Navbar Styles */
        .top-nav {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .top-nav::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(123, 96, 251, 0.2) 50%, transparent 100%);
        }
        
        .top-nav .asideToggle {
            background: transparent !important;
            border: none !important;
            color: var(--color-2) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
            margin: 0 8px;
        }
        
        .top-nav .asideToggle:hover {
            background: linear-gradient(135deg, rgba(123, 96, 251, 0.1) 0%, rgba(123, 96, 251, 0.05) 100%) !important;
            color: #7b60fb !important;
            transform: scale(1.05);
        }
        
        .top-nav .asideToggle:active {
            transform: scale(0.95);
        }
        
        .top-nav .asideToggle span {
            transition: transform 0.3s ease;
        }
        
        .top-nav .asideToggle:hover span {
            transform: rotate(90deg);
        }
        
        .top-nav #notificationDropdown {
            margin: 0 4px;
        }
        
        .top-nav #notificationDropdown > div {
            background: transparent !important;
            border: none !important;
            color: var(--color-2) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
            position: relative;
        }
        
        .top-nav #notificationDropdown > div:hover {
            background: linear-gradient(135deg, rgba(123, 96, 251, 0.1) 0%, rgba(123, 96, 251, 0.05) 100%) !important;
            color: #7b60fb !important;
            transform: scale(1.05);
        }
        
        .top-nav #notificationDropdown > div:hover .fal {
            transform: rotate(15deg) scale(1.1);
        }
        
        .top-nav #dropdown-notifications-icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
            font-weight: 700;
            animation: pulse-badge 2s ease-in-out infinite;
        }
        
        @keyframes pulse-badge {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        .top-nav .dropdown {
            margin: 0 4px;
        }
        
        .top-nav .dropdown > div {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
        }
        
        .top-nav .dropdown > div:hover {
            transform: scale(1.05);
        }
        
        .top-nav .dropdown img {
            border: 2px solid rgba(123, 96, 251, 0.15) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(123, 96, 251, 0.1);
        }
        
        .top-nav .dropdown > div:hover img {
            border-color: rgba(123, 96, 251, 0.3) !important;
            box-shadow: 0 4px 12px rgba(123, 96, 251, 0.2);
        }
        
        .top-nav .dropdown-menu {
            border-radius: 12px !important;
            border: 1px solid rgba(123, 96, 251, 0.15) !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
            padding: 8px !important;
            margin-top: 8px !important;
            backdrop-filter: blur(20px);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%) !important;
        }
        
        .top-nav .dropdown-item {
            border-radius: 8px !important;
            padding: 10px 16px !important;
            transition: all 0.2s ease !important;
            font-weight: 500 !important;
            margin: 2px 0 !important;
        }
        
        .top-nav #fileUploadIcon {
            display: inline-block !important;
            visibility: visible !important;
        }
        
        .top-nav #fileUploadIcon > div {
            background: transparent;
            border: none;
            transition: all 0.3s ease;
        }
        
        .top-nav #fileUploadIcon > div:hover {
            background: linear-gradient(135deg, rgba(123, 96, 251, 0.1) 0%, rgba(123, 96, 251, 0.05) 100%) !important;
            color: #7b60fb !important;
            transform: scale(1.05);
        }
        
        .top-nav #fileUploadIcon > div:hover .fal {
            transform: scale(1.1);
        }
        
        .top-nav #fileUploadIcon .fal {
            display: inline-block !important;
            visibility: visible !important;
        }
        
        .top-nav .dropdown-item:hover {
            background: linear-gradient(135deg, rgba(123, 96, 251, 0.1) 0%, rgba(123, 96, 251, 0.05) 100%) !important;
            color: #7b60fb !important;
            transform: translateX(-4px);
        }
        
        .top-nav .dropdown-item span[class*="fa"] {
            margin-left: 8px;
            transition: transform 0.2s ease;
        }
        
        .top-nav .dropdown-item:hover span[class*="fa"] {
            transform: scale(1.15);
        }
        
        .top-nav .notifications-container {
            scrollbar-width: thin;
            scrollbar-color: rgba(123, 96, 251, 0.3) transparent;
        }
        
        .top-nav .notifications-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .top-nav .notifications-container::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .top-nav .notifications-container::-webkit-scrollbar-thumb {
            background: rgba(123, 96, 251, 0.3);
            border-radius: 10px;
        }
        
        .top-nav .notifications-container::-webkit-scrollbar-thumb:hover {
            background: rgba(123, 96, 251, 0.5);
        }
        
        /* Mobile Aside Toggle Button */
    </style>
    @php
    $page_title="لوحة التحكم";
    @endphp
    @include('seo.index')
    @livewireStyles
    @yield('styles')
    @if(auth()->check())
        @php
        if(session('seen_notifications')==null)
            session(['seen_notifications'=>0]);
        $notifications=auth()->user()->notifications()->orderBy('created_at','DESC')->limit(50)->get();
        $unreadNotifications=auth()->user()->unreadNotifications()->count();
        @endphp
    @endif
    @if($settings['dashboard_dark_mode']=="1")
    <style type="text/css">

        html{

            --background-0: #131923;
            --background-1: #1c222b;
            --background-aside: #11233b;
            --background-active-link: #141e2e;
            --background-form-control-focus: #161d26;

            --color-1: #fff;
            --color-2: #f1f1f1;
            --border-color: #282b2f;
            --bs-table-hover-color: #f7f7f7!important; 
        }
        .select2-dropdown,.select2-container--default .select2-selection--multiple,.select2-container--default .select2-selection--multiple .select2-selection__choice{
            background-color: var(--background-0)!important;
        }
        td, th{
            border-color: var(--border-color)!important;
        }
        .aside{
            background: #171f2a!important;
        }
        .aside *{
            color: var(--color-1)!important;
        }
        .aside .item-container.active{
            background: #192230!important;
            box-shadow: 0px 12px 17px #101d30!important;
            border-bottom: unset!important;
        }
        .aside .item-container.active *{
            color: #38b59c!important;
        }
        .sub-item.active a.active *, .sub-item.active a.active {
            color: #38b59c!important;
        }
        #home-dashboard-divider{
            background: #7b60fb!important;
        }
        body{
            color: var(--color-1)!important;
            background: #131923!important;
        }
        .main-box-wedit {
            box-shadow: unset;
            border-radius: 10px 25px 10px 25px;
            background: #1c222b!important;
        }
        .main-box{
            background: #1c222b!important;
            box-shadow: unset!important;
        }
        .btn{
            color: var(--color-2)!important;
        }
        table{
            color: var(--color-2)!important;
            border-color: var(--border-color)!important;
        }
        thead th{
            border-color: var(--border-color)!important;
        }
        .table-hover>tbody>tr:hover{

        }
        *,.dropdown-item{
            color: var(--color-1);
        }
        .dropdown-menu{
            background-color: var(--background-1)!important;
        }
        .dropdown-item:focus, .dropdown-item:hover {
            color: var(--color-1);
            background-color: var(--background-2)!important;
        }
        *[class*='border-']{
            border-color: var(--border-color)!important;
        }
        hr{
            background: var(--border-color);
        }
        .form-control {
            background: rgb(39 38 37 / 20%);
            border-color: #8c6934;
        }
        .form-control{
            background: var(--background-1);
            border-color: var(--border-color);
        }
        .form-control:focus {
            box-shadow: unset!important;
            border: 1px solid #ff9800!important;
            background: #0e0d0c!important;
        }
        /*.form-control:focus {
            box-shadow: unset!important;
            border: 1px solid var(--border-color)!important;
            background: var(--background-form-control-focus)!important;
        }*/
        .form-control ,.form-control:focus{
            color: var(--color-1);
        }
        .settings-tab-opener.active,.settings-tab-opener{
            box-shadow: unset!important;
        }
    </style>
    @endif
    
</head>



<body style="background: #f5f5f5" class="dash">
    <style type="text/css">
        #toast-container>div {
            opacity: 1;
        }
        .phpdebugbar *{ direction:ltr!important }
        .fl-wrapper{
            z-index:999999!important;
        }
        ::-webkit-scrollbar {
            display: none;
        }
        .fancybox__content{
            background:var(--background-1);;
        }
    </style>
    @yield('after-body')
    <div class="col-12 justify-content-end d-flex">
        @if($errors->any())
        <div class="col-12" style="position: absolute;top: 80px;left: 10px;">
            {!! implode('', $errors->all('<div class="alert-click-hide alert alert-danger alert alert-danger col-9 col-xl-3 rounded-0 mb-1" style="position: fixed!important;z-index: 11;opacity:.9;left:25px;cursor:pointer;" onclick="$(this).fadeOut();">:message</div>')) !!}
        </div>
        @endif
    </div>
    <form method="POST" action="{{route('logout')}}" id="logout-form" class="d-none">@csrf</form>
    <div class="col-12 d-flex">
        <div style="width: 260px;background: #ffffff;min-height: 100vh;position: fixed;z-index: 900;box-shadow: 0 0 1rem rgba(0,0,0,.1)!important;" class="aside active">
            <!-- Mobile Close Button -->
            <div class="aside-mobile-close d-flex d-md-none justify-content-end align-items-center px-3 py-2" style="border-bottom: 1px solid rgba(0, 0, 0, 0.08);">
                <button class="asideToggle d-flex justify-content-center align-items-center" style="width: 36px;height: 36px;border: none;background: transparent;border-radius: 8px;cursor: pointer;transition: all 0.3s ease;">
                    <span class="fal fa-times font-4" style="color: var(--color-2);"></span>
                </button>
            </div>
        <div class="col-12 px-0 user-profile-section">
            <a href="{{route('admin.profile.edit')}}">
                <img src="{{auth()->user()->getUserAvatar()}}" class="d-inline-block">
            </a>
            <div class="user-name">
                مرحباً {{auth()->user()->name}}
            </div> 
        </div>
            <div class="col-12 px-0">



                <div class="col-12 px-0 aside-menu" style="height: calc(100vh - 260px);overflow: auto;">

                    @include('admin.views-components.aside',[
                        'links'=>[
                            [
                                'text'=>"الرئيسية",
                                'url'=>route('admin.index'),
                                'icon'=>"fal fa-home"
                            ],
                            [
                                'can'=>"advertisements-read",
                                'text'=>"الوحدات الإعلانية",
                                'url'=>route('admin.advertisements.index'),
                                'icon'=>"fal fa-bullhorn"
                            ],
                            [
                                'can'=>"settings-update",
                                'text'=>"الاعدادات",
                                'url'=>route('admin.settings.index'),
                                'icon'=>"fal fa-wrench"
                            ],
                        ]
                    ])
                    
                    <!-- Analytics Sites List -->
                    @php
                        $userId = auth()->id();
                        // All users (including superadmin) see only their own sites + sites they're members of
                        $ownedSites = \App\Models\AnalyticsSite::where('user_id', $userId)->get();
                        $memberSites = \App\Models\AnalyticsSite::whereHas('users', function($query) use ($userId) {
                            $query->where('user_id', $userId);
                        })->get();
                        $analyticsSites = $ownedSites->merge($memberSites)->unique('id')->sortBy('order')->values();
                    @endphp
                    <div class="col-12 px-0 sites-section">
                        <div class="sites-section-title">
                            المواقع
                        </div>
                        @if($analyticsSites->count() > 0)
                            @foreach($analyticsSites as $site)
                                <a href="{{ route('admin.analytics.show', ['site' => $site->site_key]) }}" class="col-12 px-0">
                                    @php
                                        $isActive = request()->routeIs('admin.analytics.show') && request()->route('site') && (is_string(request()->route('site')) ? request()->route('site') == $site->site_key : request()->route('site')->site_key == $site->site_key);
                                    @endphp
                                    <div class="col-12 site-item px-0 d-flex align-items-center {{ $isActive ? 'active' : '' }}">
                                        <div style="width: 40px; flex-shrink: 0;" class="px-2 text-center d-flex align-items-center justify-content-center">
                                            <img src="https://icons.duckduckgo.com/ip3/{{ $site->domain }}.ico" 
                                                 alt="" 
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                                            <span class="fal fa-chart-line font-2" style="display: none; color: #7b60fb; font-size: 14px;"></span>
                                        </div>
                                        <div style="width: calc(100% - 40px); min-width: 0;" class="px-2">
                                            <div class="site-item-title">{{ $site->title ?? $site->domain }}</div>
                                            @if($site->title && $site->title !== $site->domain)
                                            <div class="site-item-domain">{{ $site->domain }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                        <a href="{{ route('admin.analytics.create') }}" class="col-12 px-0">
                            <div class="col-12 add-site-button px-0 d-flex align-items-center">
                                <div style="width: 40px; flex-shrink: 0;" class="px-2 text-center d-flex align-items-center justify-content-center">
                                    <span class="fal fa-plus font-2" style="font-size: 14px;"></span>
                                </div>
                                <div style="width: calc(100% - 40px); min-width: 0;" class="px-2 item-container-title">
                                    إضافة موقع جديد
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
           
        </div>
        <div class="main-content in-active" style="overflow: hidden;">
            <div class="col-12 px-0 d-flex justify-content-between align-items-center top-nav" style="height: 60px;position: fixed;width: 100%;width: calc(100% - 260px);z-index: 99;padding: 0 16px;">

                <div class="col-auto px-0 d-flex justify-content-center align-items-center btn asideToggle" style="width: 44px;height: 44px;">
                    <span class="fal fa-bars font-4"></span>
                </div> 
                <div class="col-auto px-0 d-flex justify-content-end align-items-center" style="gap: 8px;">




                    <!-- File Upload Icon -->
                    @can('hub-files-create')
                    <div class="btn-group" id="fileUploadIcon" style="width:44px;height:44px;margin-right: 8px;">
                        <div class="d-flex justify-content-center align-items-center btn" style="width: 44px;height: 44px;position: relative;cursor: pointer;border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#fileUploadModal">
                            <span class="fal fa-cloud-upload-alt font-3 d-inline-block" style="color: var(--color-2);transition: all 0.3s ease;font-size: 18px;"></span>
                        </div>
                    </div>
                    @endcan

                    <div class="btn-group" id="notificationDropdown" style="width:44px;height:44px;">

                        <div class="d-flex justify-content-center align-items-center btn" style="width: 44px;height: 44px;position: relative;" data-bs-toggle="dropdown" aria-expanded="false" id="dropdown-notifications">
                            <span class="fal fa-bell font-3 d-inline-block" style="color: var(--color-2);transform: rotate(15deg);transition: all 0.3s ease;"></span>
                            <span style="position: absolute;min-width: 20px;min-height: 20px;
                            @if($unreadNotifications!=0)
                            display: inline-flex;
                            @else
                            display: none;
                            @endif
                            align-items: center;justify-content: center;right: 0px;top: 0px;border-radius: 10px;font-size: 11px;font-weight: 700;padding: 2px 6px;" class="text-center" id="dropdown-notifications-icon">{{$unreadNotifications}}</span>

                        </div>
                        <div class="dropdown-menu py-0 rounded-0 border-0 shadow " style="cursor: auto!important;z-index: 20000;width: 350px;height: 450px;top: -3px!important;">
                            <div class="col-12 notifications-container" style="height:406px;overflow: auto;">
                                <x-notifications :notifications="$notifications" />
                            </div>
                            <div class="col-12 d-flex border-top"> 
                                <a href="{{route('admin.notifications.index')}}" class="d-block py-2 px-3 ">
                                    <div class="col-12 align-items-center">
                                      <span class="fal fa-bells"></span>  عرض كل الإشعارات
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center align-items-center dropdown" style="width: 44px;height: 44px;">
                        <div style="width: 44px;height: 44px;cursor: pointer;" data-bs-toggle="dropdown" aria-expanded="false" class="d-flex justify-content-center align-items-center cursor-pointer">
                            <img src="{{auth()->user()->getUserAvatar()}}" style="border-radius: 50%;width: 44px;height: 44px;object-fit: cover;">
                        </div>
                        <ul class="dropdown-menu shadow border-0" aria-labelledby="dropdownMenuButton1" style="top: -3px; max-height: 80vh; overflow-y: auto;">
                                <li><a class="dropdown-item font-1" href="/" target="_blank"><span class="fal fa-desktop font-1"></span> عرض الموقع</a></li>
                                <li><a class="dropdown-item font-1" href="{{route('admin.profile.index')}}"><span class="fal fa-user font-1"></span> ملفي الشخصي</a></li>
                                <li><a class="dropdown-item font-1" href="{{route('admin.profile.edit')}}"><span class="fal fa-edit font-1"></span> تعديل ملفي الشخصي</a></li> 
                                
                                <li><hr style="height: 1px;margin: 10px 0px 5px;"></li>
                                
                                @can('roles-read')
                                <li><a class="dropdown-item font-1" href="{{route('admin.roles.index')}}"><span class="fal fa-key font-1"></span> الصلاحيات</a></li>
                                @endcan
                                
                                @can('users-read')
                                <li><a class="dropdown-item font-1" href="{{route('admin.users.index')}}"><span class="fal fa-users font-1"></span> المستخدمين</a></li>
                                @endcan
                                
                                @can('profile-read')
                                <li><hr style="height: 1px;margin: 10px 0px 5px;"></li>
                                <li><a class="dropdown-item font-1" href="#" style="font-weight: 600;"><span class="fal fa-newspaper font-1"></span> المحتوى</a></li>
                                @can('categories-read')
                                <li><a class="dropdown-item font-1" href="{{route('admin.categories.index')}}" style="padding-right: 40px;"><span class="fal fa-tag font-1"></span> الأقسام</a></li>
                                @endcan
                                @can('articles-read')
                                <li><a class="dropdown-item font-1" href="{{route('admin.articles.index')}}" style="padding-right: 40px;"><span class="fal fa-book font-1"></span> المقالات</a></li>
                                @endcan
                                @can('comments-read')
                                <li><a class="dropdown-item font-1" href="{{route('admin.article-comments.index')}}" style="padding-right: 40px;"><span class="fal fa-comments font-1"></span> التعليقات</a></li>
                                @endcan
                                @can('announcements-read')
                                <li><a class="dropdown-item font-1" href="{{route('admin.announcements.index')}}" style="padding-right: 40px;"><span class="fal fa-bullhorn font-1"></span> الاعلانات</a></li>
                                @endcan
                                @can('pages-read')
                                <li><a class="dropdown-item font-1" href="{{route('admin.pages.index')}}" style="padding-right: 40px;"><span class="fal fa-file-invoice font-1"></span> الصفحات</a></li>
                                @endcan
                                @can('menus-read')
                                <li><a class="dropdown-item font-1" href="{{route('admin.menus.index')}}" style="padding-right: 40px;"><span class="fal fa-list font-1"></span> القوائم</a></li>
                                @endcan
                                @can('faqs-read')
                                <li><a class="dropdown-item font-1" href="{{route('admin.faqs.index')}}" style="padding-right: 40px;"><span class="fal fa-question font-1"></span> الأسئلة الشائعة</a></li>
                                @endcan
                                @can('redirections-read')
                                <li><a class="dropdown-item font-1" href="{{route('admin.redirections.index')}}" style="padding-right: 40px;"><span class="fal fa-directions font-1"></span> التحويلات</a></li>
                                @endcan
                                @can('tags-read')
                                <li><a class="dropdown-item font-1" href="{{route('admin.tags.index')}}" style="padding-right: 40px;"><span class="fal fa-tags font-1"></span> الوسوم</a></li>
                                @endcan
                                @endcan
                                
                                @can('contacts-read')
                                <li><hr style="height: 1px;margin: 10px 0px 5px;"></li>
                                <li><a class="dropdown-item font-1" href="{{route('admin.contacts.index')}}">
                                    <span class="fal fa-phone font-1"></span> طلب التواصل
                                    @if(\App\Models\Contact::where('status','PENDING')->count() > 0)
                                    <span style="background: #d34339;border-radius: 2px;color:#fff!important;display: inline-block;font-size: 11px;text-align: center;padding: 1px 5px;margin: 0px 8px">{{ \App\Models\Contact::where('status','PENDING')->count() }}</span>
                                    @endif
                                </a></li>
                                @endcan
                                
                                @can('plugins-read')
                                <li><a class="dropdown-item font-1" href="{{route('admin.plugins.index')}}"><span class="fal fa-puzzle-piece-simple font-1"></span> الاضافات</a></li>
                                @endcan
                                
                                <li><a class="dropdown-item font-1" href="{{route('admin.analytics.index')}}"><span class="fal fa-chart-line font-1"></span> التحليلات</a></li>
                                @if(auth()->user()->hasRole('superadmin'))
                                <li><a class="dropdown-item font-1" href="{{route('admin.analytics.websites')}}"><span class="fal fa-globe font-1"></span> جميع المواقع</a></li>
                                @endif
                                
                                @can('hub-files-read')
                                <li><hr style="height: 1px;margin: 10px 0px 5px;"></li>
                                <li><a class="dropdown-item font-1" href="{{route('admin.files.index')}}"><span class="fal fa-file font-1"></span> الملفات</a></li> 
                                @endcan

                                @can('traffics-read')
                                <li><a class="dropdown-item font-1" href="{{route('admin.traffics.index')}}"><span class="fal fa-traffic-light font-1"></span> الترافيك</a></li> 
                                @endcan

                                @can('error-reports-read')
                                <li><a class="dropdown-item font-1" href="{{route('admin.traffics.error-reports')}}"><span class="fal fa-bug font-1"></span> تقارير الأخطاء</a></li> 
                                @endcan
                                
                                <li><hr style="height: 1px;margin: 10px 0px 5px;"></li>
                                <li><a class="dropdown-item font-1" href="#" onclick="document.getElementById('logout-form').submit();"><span class="fal fa-sign-out-alt font-1"></span> تسجيل خروج</a></li>
                        </ul>

                    </div>

                    {{-- <div class="dropdown" style="width: 55px;height: 55px;">
                        <span class="d-inline-block fal fa-user"></span> 
                    </div> --}}

                </div>
            </div>
            <div class="col-12 px-0  " style="margin-top: 55px;position: relative;">
                <div style="position:fixed;display: flex;align-items: center;justify-content: center;height: 100vh;background: var(--background-1);z-index: 10;margin-top: -15px;" id="loading-image-container">
                    <img src="/images/loading2.gif" style="position:fixed;width: 220px;max-width: 80%;margin-top: -60px;" id="loading-image">
                </div>
                
                @yield('content')
            </div>
        </div>
    </div>

    @vite('resources/js/dashboard.js')
    @livewireScripts
    @include('layouts.scripts')
    @yield('scripts')
    @stack('scripts')
    
    @can('hub-files-create')
    <!-- File Upload Modal -->
    <div class="modal fade" id="fileUploadModal" tabindex="-1" aria-labelledby="fileUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                <div class="modal-header" style="border-bottom: 1px solid #e9ecef; padding: 20px 24px;">
                    <h5 class="modal-title font-1" id="fileUploadModalLabel" style="font-weight: 600; color: #212529;">
                        <i class="fal fa-cloud-upload-alt me-2" style="color: #7b60fb;"></i> رفع ملف جديد
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 24px;">
                    <div class="upload-area-modal" id="uploadAreaModal" style="border: 2px dashed #7b60fb; border-radius: 12px; padding: 60px 40px; background: #f8f9fa; transition: all 0.3s ease; cursor: pointer; position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 200px;">
                        <div class="upload-icon-modal" style="font-size: 64px; color: #7b60fb; margin-bottom: 20px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-text-modal" style="font-size: 18px; color: #495057; margin-bottom: 12px; font-weight: 500; text-align: center;">
                            اسحب الملفات هنا أو اضغط للاختيار
                        </div>
                        <div class="upload-hint-modal" style="font-size: 14px; color: #6c757d; text-align: center;">
                            صور أو فيديو - الحد الأقصى 100MB
                        </div>
                        <input type="file" id="fileInputModal" accept="image/*,video/*" style="position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer;">
                    </div>
                    
                    <div class="file-upload-progress-modal mt-4" id="uploadProgressModal" style="display: none;">
                        <div class="progress" style="height: 24px; border-radius: 12px; overflow: hidden;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; background: linear-gradient(90deg, #7b60fb 0%, #667eea 100%);"></div>
                        </div>
                        <div class="mt-3 text-center" id="uploadStatusModal" style="font-size: 14px; color: #495057;"></div>
                    </div>
                    
                    <div class="uploaded-file-result mt-4" id="uploadedFileResult" style="display: none;">
                        <div class="alert alert-success d-flex align-items-center justify-content-between" style="border-radius: 12px; border: none; background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); padding: 16px 20px;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle me-2" style="font-size: 24px; color: #28a745;"></i>
                                <div>
                                    <div style="font-weight: 600; color: #155724; margin-bottom: 4px;">تم رفع الملف بنجاح!</div>
                                    <div id="fileLinkContainer" style="display: flex; align-items: center; gap: 8px; margin-top: 8px;">
                                        <input type="text" id="fileLinkInput" readonly style="flex: 1; padding: 8px 12px; border: 1px solid #c3e6cb; border-radius: 8px; background: #fff; font-size: 13px;">
                                        <button type="button" class="btn btn-sm btn-primary" id="copyLinkBtn" onclick="copyModalLink()" style="padding: 8px 16px; border-radius: 8px; font-weight: 500;">
                                            <i class="fas fa-copy me-1"></i> نسخ
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 16px 24px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 10px 20px;">إغلاق</button>
                    <button type="button" class="btn btn-primary" id="uploadAnotherBtn" onclick="resetUploadModal()" style="border-radius: 8px; padding: 10px 20px; background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%); border: none; display: none;">
                        <i class="fas fa-plus me-1"></i> رفع ملف آخر
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    (function() {
        'use strict';
        
        const uploadAreaModal = document.getElementById('uploadAreaModal');
        const fileInputModal = document.getElementById('fileInputModal');
        const uploadProgressModal = document.getElementById('uploadProgressModal');
        const uploadStatusModal = document.getElementById('uploadStatusModal');
        const progressBarModal = uploadProgressModal.querySelector('.progress-bar');
        const uploadedFileResult = document.getElementById('uploadedFileResult');
        const fileLinkInput = document.getElementById('fileLinkInput');
        const copyLinkBtn = document.getElementById('copyLinkBtn');
        const uploadAnotherBtn = document.getElementById('uploadAnotherBtn');
        
        // Click to select files
        uploadAreaModal.addEventListener('click', function(e) {
            if (e.target !== fileInputModal) {
                fileInputModal.click();
            }
        });
        
        // File input change
        fileInputModal.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                handleModalFile(e.target.files[0]);
            }
        });
        
        // Drag and drop
        uploadAreaModal.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadAreaModal.style.background = '#e3f2fd';
            uploadAreaModal.style.borderColor = '#1976d2';
            uploadAreaModal.style.transform = 'scale(1.02)';
        });
        
        uploadAreaModal.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadAreaModal.style.background = '#f8f9fa';
            uploadAreaModal.style.borderColor = '#7b60fb';
            uploadAreaModal.style.transform = 'scale(1)';
        });
        
        uploadAreaModal.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadAreaModal.style.background = '#f8f9fa';
            uploadAreaModal.style.borderColor = '#7b60fb';
            uploadAreaModal.style.transform = 'scale(1)';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleModalFile(files[0]);
            }
        });
        
        function handleModalFile(file) {
            // Validate file size (100MB = 104857600 bytes)
            const maxSize = 100 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('حجم الملف ' + file.name + ' أكبر من 100MB');
                return;
            }
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 
                               'video/mp4', 'video/avi', 'video/quicktime', 'video/x-msvideo', 
                               'video/x-ms-wmv', 'video/x-flv', 'video/webm'];
            if (!validTypes.includes(file.type)) {
                alert('نوع الملف غير مدعوم: ' + file.name);
                return;
            }
            
            // Hide previous result
            uploadedFileResult.style.display = 'none';
            uploadAnotherBtn.style.display = 'none';
            
            // Show progress
            uploadProgressModal.style.display = 'block';
            progressBarModal.style.width = '0%';
            progressBarModal.classList.remove('bg-success', 'bg-danger');
            progressBarModal.classList.add('progress-bar-animated');
            uploadStatusModal.innerHTML = 'جاري رفع <strong>' + file.name + '</strong>...';
            
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{csrf_token()}}');
            
            const xhr = new XMLHttpRequest();
            
            // Upload progress
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progressBarModal.style.width = percentComplete + '%';
                }
            });
            
            // Upload complete
            xhr.addEventListener('load', function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Hide progress
                        uploadProgressModal.style.display = 'none';
                        
                        // Show result with link
                        fileLinkInput.value = response.file.url;
                        uploadedFileResult.style.display = 'block';
                        uploadAnotherBtn.style.display = 'inline-block';
                        
                        // Add file to table if on files page
                        if (typeof addFileToTable === 'function') {
                            addFileToTable(response.file);
                        }
                        
                        uploadStatusModal.innerHTML = '<span style="color: #28a745;"><i class="fas fa-check-circle"></i> تم رفع الملف بنجاح!</span>';
                    } else {
                        uploadStatusModal.innerHTML = '<span style="color: #dc3545;"><i class="fas fa-times-circle"></i> خطأ: ' + (response.message || 'فشل الرفع') + '</span>';
                        progressBarModal.classList.remove('progress-bar-animated');
                        progressBarModal.classList.add('bg-danger');
                    }
                } else {
                    uploadStatusModal.innerHTML = '<span style="color: #dc3545;"><i class="fas fa-times-circle"></i> خطأ في رفع الملف</span>';
                    progressBarModal.classList.remove('progress-bar-animated');
                    progressBarModal.classList.add('bg-danger');
                }
            });
            
            // Upload error
            xhr.addEventListener('error', function() {
                uploadStatusModal.innerHTML = '<span style="color: #dc3545;"><i class="fas fa-times-circle"></i> حدث خطأ أثناء الرفع</span>';
                progressBarModal.classList.remove('progress-bar-animated');
                progressBarModal.classList.add('bg-danger');
            });
            
            xhr.open('POST', '{{route("admin.files.upload")}}');
            xhr.send(formData);
        }
        
        // Reset modal on close
        const modal = document.getElementById('fileUploadModal');
        modal.addEventListener('hidden.bs.modal', function() {
            resetUploadModal();
        });
        
        window.resetUploadModal = function() {
            uploadedFileResult.style.display = 'none';
            uploadProgressModal.style.display = 'none';
            uploadAnotherBtn.style.display = 'none';
            fileInputModal.value = '';
            uploadAreaModal.style.background = '#f8f9fa';
            uploadAreaModal.style.borderColor = '#7b60fb';
            uploadAreaModal.style.transform = 'scale(1)';
        };
        
        window.copyModalLink = function() {
            fileLinkInput.select();
            fileLinkInput.setSelectionRange(0, 99999);
            
            try {
                document.execCommand('copy');
                const originalText = copyLinkBtn.innerHTML;
                copyLinkBtn.innerHTML = '<i class="fas fa-check me-1"></i> تم النسخ!';
                copyLinkBtn.classList.remove('btn-primary');
                copyLinkBtn.classList.add('btn-success');
                
                setTimeout(function() {
                    copyLinkBtn.innerHTML = originalText;
                    copyLinkBtn.classList.remove('btn-success');
                    copyLinkBtn.classList.add('btn-primary');
                }, 2000);
            } catch (err) {
                alert('فشل نسخ الرابط');
            }
        };
    })();
    </script>
    @endcan
</body>
</html>