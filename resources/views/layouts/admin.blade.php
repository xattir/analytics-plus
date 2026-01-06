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
            <div class="col-12 px-0 d-flex" style="height: 55px">
                <div class="col-12 p-1" style="color: var(--background-1)">
                    <div class="col-12 p-0 row">
                        <div class="col-3 py-1 px-1">
                             {{-- <span class="fas fa-chart-bar font-4 d-flex justify-content-center align-items-center" style="background: #38b59c;height: 40px;width: 40px;border-radius: 50%;color: var(--background-1);"></span> --}}
                        </div>
                        <div class="col-9 ">
                            {{-- <span class="d-inline-block px-2 font-3 pt-1">لوحة التحكم</span>  --}}
                            <span style="width: 55px;height: 55px;position: absolute;left: 0px;top: 0px;align-items: center;justify-content: center;cursor: pointer;border-color: transparent!important;" class="asideToggle d-flex d-md-none rounded-0" >
                                <span class="fal fa-bars font-4 "></span>
                            </span>
                        </div>
                    </div>
                    <div class="d-none d-md-none justify-content-center align-items-center px-0   asideToggle" style="width: 40px;height: 40px;">
                        <span class="fal fa-bars font-4 cursor-pointer"></span>
                    </div>
                </div>
            </div>
        <div class="col-12 px-0 pb-4 text-center justify-content-center align-items-center ">
            <a href="{{route('admin.profile.edit')}}">

            <img src="{{auth()->user()->getUserAvatar()}}" style="width: 80px;height: 80px;color: var(--background-1);border-radius: 50%" class="d-inline-block">
                </a>
                <div class="col-12 px-0 mt-2 text-center" style="color: #232323;">
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
                                'can'=>"settings-update",
                                'text'=>"الاعدادات",
                                'url'=>route('admin.settings.index'),
                                'icon'=>"fal fa-wrench"
                            ],
                            [
                                'attribute'=>"onclick=document.getElementById('logout-form').submit();",
                                'can'=>"profile-read",
                                'text'=>"تسجيل خروج",
                                'url'=>"#",
                                'icon'=>"fal fa-sign-out-alt"
                            ],
                        ]
                    ])
                    
                    <!-- Analytics Sites List -->
                    @php
                        $userId = auth()->id();
                        $isSuperAdmin = auth()->user()->hasRole('superadmin');
                        
                        if ($isSuperAdmin) {
                            $analyticsSites = \App\Models\AnalyticsSite::orderBy('order', 'asc')->orderBy('created_at', 'desc')->get();
                        } else {
                            $ownedSites = \App\Models\AnalyticsSite::where('user_id', $userId)->get();
                            $memberSites = \App\Models\AnalyticsSite::whereHas('users', function($query) use ($userId) {
                                $query->where('user_id', $userId);
                            })->get();
                            $analyticsSites = $ownedSites->merge($memberSites)->unique('id')->sortBy('order')->values();
                        }
                    @endphp
                    <div class="col-12 px-0 mt-3" style="border-top: 1px solid rgba(0,0,0,0.1); padding-top: 15px;">
                        <div class="col-12 px-3 mb-2" style="color: var(--color-2); font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                            المواقع
                        </div>
                        @if($analyticsSites->count() > 0)
                            @foreach($analyticsSites as $site)
                                <a href="{{ route('admin.analytics.show', ['site' => $site->site_key]) }}" class="col-12 px-0">
                                    @php
                                        $isActive = request()->routeIs('admin.analytics.show') && request()->route('site') && (is_string(request()->route('site')) ? request()->route('site') == $site->site_key : request()->route('site')->site_key == $site->site_key);
                                    @endphp
                                    <div class="col-12 item-container px-0 d-flex {{ $isActive ? 'active' : '' }}">
                                        <div style="width: 50px" class="px-3 text-center">
                                            <img src="https://icons.duckduckgo.com/ip3/{{ $site->domain }}.ico" 
                                                 alt="" 
                                                 style="width: 20px; height: 20px; border-radius: 4px;"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                                            <span class="fal fa-chart-line font-2" style="display: none;"></span>
                                        </div>
                                        <div style="width: calc(100% - 50px)" class="px-2 item-container-title">
                                            <div style="font-size: 13px; font-weight: 500;">{{ $site->title ?? $site->domain }}</div>
                                            @if($site->title && $site->title !== $site->domain)
                                            <div style="font-size: 11px; color: rgba(0,0,0,0.5); margin-top: 2px;">{{ $site->domain }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                        <a href="{{ route('admin.analytics.create') }}" class="col-12 px-0 mt-2">
                            <div class="col-12 item-container px-0 d-flex" style="background: rgba(123, 96, 251, 0.1); border: 1px dashed rgba(123, 96, 251, 0.3); border-radius: 6px;">
                                <div style="width: 50px" class="px-3 text-center">
                                    <span class="fal fa-plus font-2" style="color: #7b60fb;"></span>
                                </div>
                                <div style="width: calc(100% - 50px)" class="px-2 item-container-title" style="color: #7b60fb; font-weight: 600;">
                                    إضافة موقع جديد
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
           
        </div>
        <div class="main-content in-active" style="overflow: hidden;">
            <div class="col-12 px-0 d-flex justify-content-between top-nav" style="height: 55px;background: var(--background-1);position: fixed;width: 100%;width: calc(100% - 260px);z-index: 99;border-bottom: 1px solid var(--border-color);">

                <div class="col-12 px-0 d-flex justify-content-center align-items-center btn  asideToggle" style="width: 55px;height: 55px;">
                    <span class="fal fa-bars font-4"></span>
                </div> 
                <div class="col-auto px-0 d-flex justify-content-end  " style="height: 60px;">




                    <div class="btn-group" id="notificationDropdown" style="width:55px">

                        <div class="col-12 px-0 d-flex justify-content-center align-items-center btn  " style="width: 55px;height: 55px;" data-bs-toggle="dropdown" aria-expanded="false" id="dropdown-notifications">
                            <span class="fal fa-bell font-3 d-inline-block" style="color: var(--color-2);transform: rotate(15deg)"></span>
                            <span style="position: absolute;min-width: 25px;min-height: 25px;
                            @if($unreadNotifications!=0)
                            display: inline-block;
                            @else
                            display: none;
                            @endif
                            right: 0px;top: 0px;border-radius: 20px;background: #c00;color:#fff;font-size: 14px;" class="text-center" id="dropdown-notifications-icon">{{$unreadNotifications}}</span>

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

                    <div class="col-12 px-0 d-flex justify-content-center align-items-center  dropdown"  style="width: 55px;height: 55px;" >
                        <div style="width: 55px;height: 55px;cursor: pointer;" data-bs-toggle="dropdown" aria-expanded="false" class="d-flex justify-content-center align-items-center cursor-pointer">
                            <img src="{{auth()->user()->getUserAvatar()}}" style="padding: 10px;border-radius: 50%;width: 55px;height: 55px;">
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
</body>
</html>