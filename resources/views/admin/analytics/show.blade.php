@extends('layouts.admin')
@section('content')
<style>
    .analytics-dashboard {
        --analytics-bg: var(--background-1, #ffffff);
        --analytics-border: var(--border-color, #e5e7eb);
        --analytics-text: var(--color-2, #1f2937);
        --analytics-text-muted: #6b7280;
        --analytics-primary: #7b60fb;
        --analytics-success: #10b981;
        --analytics-active: #10b981;
        
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif;
        color: var(--analytics-text);
        background: var(--background-0, #f9fafb);
        min-height: 100vh;
        padding: 0;
    }
    
    .analytics-header {
        background: var(--analytics-bg);
        border-bottom: 1px solid var(--analytics-border);
        padding: 24px 32px;
        margin-bottom: 32px;
    }
    
    .analytics-header h1 {
        font-size: 24px;
        font-weight: 600;
        margin: 0 0 4px 0;
        color: var(--analytics-text);
    }
    
    .analytics-header p {
        font-size: 14px;
        color: var(--analytics-text-muted);
        margin: 0 0 12px 0;
    }
    
    .header-stats {
        display: flex;
        gap: 24px;
        margin-top: 8px;
        flex-wrap: wrap;
    }
    
    .header-stat-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--analytics-text-muted);
    }
    
    .header-stat-icon {
        font-size: 14px;
    }
    
    .header-stat-label {
        font-weight: 500;
    }
    
    .header-stat-value {
        font-weight: 600;
        color: var(--analytics-text);
    }
    
    .hero-card {
        background: var(--analytics-bg);
        border: 1px solid var(--analytics-border);
        border-radius: 12px;
        padding: 24px;
        transition: all 0.2s;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .hero-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    .hero-card-active {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, rgba(16, 185, 129, 0.02) 100%);
        border-color: var(--analytics-active);
        box-shadow: 0 0 0 1px rgba(16, 185, 129, 0.1), 0 4px 16px rgba(16, 185, 129, 0.15);
        animation: subtlePulse 3s ease-in-out infinite;
    }
    
    @keyframes subtlePulse {
        0%, 100% { box-shadow: 0 0 0 1px rgba(16, 185, 129, 0.1), 0 4px 16px rgba(16, 185, 129, 0.15); }
        50% { box-shadow: 0 0 0 1px rgba(16, 185, 129, 0.15), 0 4px 20px rgba(16, 185, 129, 0.2); }
    }
    
    .metric-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        background: rgba(123, 96, 251, 0.1);
        color: var(--analytics-primary);
        margin-bottom: 16px;
    }
    
    .hero-card-active .metric-icon {
        background: rgba(16, 185, 129, 0.15);
        color: var(--analytics-active);
    }
    
    .metric-label {
        font-size: 13px;
        color: var(--analytics-text-muted);
        font-weight: 500;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .metric-value {
        font-size: 36px;
        font-weight: 700;
        line-height: 1.2;
        margin: 0;
        color: var(--analytics-text);
    }
    
    .hero-card-active .metric-value {
        color: var(--analytics-active);
    }
    
    .section-card {
        background: var(--analytics-bg);
        border: 1px solid var(--analytics-border);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 32px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 24px 0;
        color: var(--analytics-text);
        padding-bottom: 16px;
        border-bottom: 2px solid var(--analytics-border);
    }
    
    .page-row {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        margin-bottom: 4px;
        border-radius: 6px;
        background: var(--analytics-bg);
        border: 1px solid var(--analytics-border);
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
    }
    
    .page-row:hover {
        border-color: var(--analytics-primary);
        box-shadow: 0 1px 4px rgba(123, 96, 251, 0.1);
    }
    
    .page-row::before {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: var(--progress-width, 0%);
        background: rgba(123, 96, 251, 0.08);
        z-index: 0;
    }
    
    .page-row-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        gap: 12px;
    }
    
    .page-path-link {
        text-decoration: none;
        flex: 1;
        margin-left: 8px;
    }
    
    .page-path {
        font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
        font-size: 12px;
        color: var(--analytics-text);
        word-break: break-all;
        line-height: 1.4;
        display: block;
        transition: color 0.2s;
    }
    
    .page-path-link:hover .page-path {
        color: var(--analytics-primary);
    }
    
    .page-visits {
        font-size: 13px;
        font-weight: 600;
        color: var(--analytics-text);
        white-space: nowrap;
    }
    
    /* Visits Table Styles */
    .visits-table-container {
        overflow-x: auto;
        margin-top: 16px;
    }
    
    .visits-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    
    .visits-table thead {
        background: rgba(123, 96, 251, 0.05);
        border-bottom: 2px solid var(--analytics-border);
    }
    
    .visits-table th {
        padding: 12px 16px;
        text-align: right;
        font-weight: 600;
        font-size: 12px;
        color: var(--analytics-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    
    .visits-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--analytics-border);
        vertical-align: middle;
    }
    
    .visits-table-row {
        transition: background-color 0.15s ease;
        cursor: default;
    }
    
    .visits-table-row:hover {
        background-color: rgba(123, 96, 251, 0.03);
    }
    
    .visits-table-path {
        max-width: 200px;
    }
    
    .path-link {
        font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
        font-size: 12px;
        color: var(--analytics-primary);
        background: rgba(123, 96, 251, 0.08);
        padding: 4px 8px;
        border-radius: 4px;
        display: inline-block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: help;
    }
    
    .path-link-clickable {
        text-decoration: none;
        display: inline-block;
        max-width: 100%;
    }
    
    .path-link-clickable code {
        font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
        font-size: 12px;
        color: var(--analytics-primary);
        background: rgba(123, 96, 251, 0.08);
        padding: 4px 8px;
        border-radius: 4px;
        display: inline-block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .path-link-clickable:hover code {
        background: rgba(123, 96, 251, 0.15);
        color: var(--analytics-primary);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(123, 96, 251, 0.2);
    }
    
    .visits-table-time {
        color: var(--analytics-text-muted);
        font-size: 12px;
        white-space: nowrap;
    }
    
    .visits-table-country {
        font-size: 13px;
        white-space: nowrap;
    }
    
    .visits-table-ip {
        font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
    }
    
    .ip-address {
        font-size: 12px;
        color: var(--analytics-text-muted);
        background: rgba(0, 0, 0, 0.03);
        padding: 4px 8px;
        border-radius: 4px;
    }
    
    .visits-table-device,
    .visits-table-browser {
        font-size: 12px;
        white-space: nowrap;
    }
    
    .visits-table-paths-count {
        text-align: center;
    }
    
    .paths-count-link {
        display: inline-block;
        font-weight: 600;
        color: var(--analytics-primary);
        background: rgba(123, 96, 251, 0.1);
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.2s;
        min-width: 40px;
        text-align: center;
    }
    
    .paths-count-link:hover {
        background: var(--analytics-primary);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(123, 96, 251, 0.3);
    }
    
    .visits-pagination {
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid var(--analytics-border);
        display: flex;
        justify-content: center;
    }
    
    .visits-pagination .pagination {
        margin: 0;
    }
    
    .visits-pagination .page-link {
        color: var(--analytics-primary);
        border-color: var(--analytics-border);
        padding: 8px 16px;
    }
    
    .visits-pagination .page-link:hover {
        background-color: rgba(123, 96, 251, 0.1);
        border-color: var(--analytics-border);
    }
    
    .visits-pagination .page-item.active .page-link {
        background-color: var(--analytics-primary);
        border-color: var(--analytics-primary);
    }
    
    .path-sequence {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }
    
    .path-badge {
        padding: 6px 12px;
        background: rgba(123, 96, 251, 0.1);
        border: 1px solid rgba(123, 96, 251, 0.2);
        border-radius: 6px;
        font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
        font-size: 12px;
        color: var(--analytics-primary);
    }
    
    .path-arrow {
        color: var(--analytics-text-muted);
        font-size: 14px;
    }
    
    .source-row {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        margin-bottom: 4px;
        border-radius: 6px;
        background: var(--analytics-bg);
        border: 1px solid var(--analytics-border);
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
    }
    
    .source-row:hover {
        border-color: var(--analytics-primary);
        box-shadow: 0 1px 4px rgba(123, 96, 251, 0.1);
    }
    
    .source-row::before {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: var(--progress-width, 0%);
        background: rgba(123, 96, 251, 0.08);
        z-index: 0;
    }
    
    .source-row-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        gap: 12px;
    }
    
    .source-icon-name {
        font-size: 13px;
        font-weight: 500;
        color: var(--analytics-text);
        display: flex;
        align-items: center;
        gap: 6px;
        flex: 1;
    }
    
    .source-count {
        font-size: 13px;
        font-weight: 600;
        color: var(--analytics-text);
        white-space: nowrap;
    }
    
    .chart-container {
        position: relative;
        height: 200px;
        margin-top: 16px;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--analytics-text-muted);
    }
    
    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.4;
    }
</style>
<div class="analytics-dashboard">
    <!-- Header -->
    <div class="analytics-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>{{ $site->domain }}</h1>
                <p>لوحة تحكم التحليلات</p>
                <div class="header-stats">
                    <span class="header-stat-item">
                        <span class="header-stat-icon">👥</span>
                        <span class="header-stat-label">الزوار اليوم:</span>
                        <span class="header-stat-value">{{ number_format($todayStats['visitors'] ?? 0) }}</span>
                    </span>
                    <span class="header-stat-item">
                        <span class="header-stat-icon">📄</span>
                        <span class="header-stat-label">مشاهدات الصفحة اليوم:</span>
                        <span class="header-stat-value">{{ number_format($todayStats['pageviews'] ?? 0) }}</span>
                    </span>
                </div>
            </div>
            <div>
                @if(isset($isAdminRoute) && $isAdminRoute)
                    <a href="{{ route('admin.analytics.tracking-code', ['site' => $site->site_key]) }}" class="btn btn-sm btn-success">كود التتبع</a>
                    @if(isset($isSuperAdmin) && $isSuperAdmin || $site->user_id == auth()->id())
                        <a href="{{ route('admin.analytics.members', ['site' => $site->site_key]) }}" class="btn btn-sm btn-primary">إدارة الفريق</a>
                    @endif
                @else
                    <a href="{{ route('user.analytics.tracking-code', ['site' => $site->site_key]) }}" class="btn btn-sm btn-success">كود التتبع</a>
                    @if($site->user_id == auth()->id())
                        <a href="{{ route('user.analytics.members', ['site' => $site->site_key]) }}" class="btn btn-sm btn-primary">إدارة الفريق</a>
                    @endif
                @endif
            </div>
        </div>
    </div>
    
    <div style="padding: 0 32px 32px;">
        <!-- HERO SECTION -->
        <div class="row mb-5">
            <!-- ACTIVE USERS (HERO) - نصف الشاشة -->
            <div class="col-lg-6 mb-4">
                <div class="hero-card hero-card-active">
                    <div class="metric-icon">⚡</div>
                    <div class="metric-label">المستخدمون النشطون (آخر 30 دقيقة)</div>
                    <div class="metric-value">{{ number_format($activeUsersCount ?? 0) }}</div>
                    <div class="chart-container">
                        <canvas id="activeUsersChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- TOP TRAFFIC SOURCES & VISITORS CHART - نصف الشاشة -->
            <div class="col-lg-6 mb-4">
                <div class="row">
                    <!-- TOP TRAFFIC SOURCES -->
                    <div class="col-12 mb-4">
                        <div class="section-card">
                            <h2 class="section-title">أفضل المصادر</h2>
                            @if(isset($topTrafficSources) && $topTrafficSources->count() > 0)
                                @php
                                    $maxSourceCount = $topTrafficSources->first()['count'] ?? 1;
                                @endphp
                                @foreach($topTrafficSources->take(5) as $source)
                                    @php
                                        $sourceName = strtolower($source['name'] ?? '');
                                        $sourceCount = $source['count'] ?? 0;
                                        $sourcePercent = $maxSourceCount > 0 ? ($sourceCount / $maxSourceCount) * 100 : 0;
                                    @endphp
                                    <div class="source-row" style="--progress-width: {{ $sourcePercent }}%;">
                                        <div class="source-row-content">
                                            <span class="source-icon-name">
                                                @if($source['type'] == 'direct' || $sourceName == 'direct')
                                                    🔗 <span>مباشر</span>
                                                @elseif($sourceName == 'google')
                                                    🔍 <span>Google</span>
                                                @elseif($sourceName == 'facebook' || $sourceName == 'fb.com')
                                                    📘 <span>Facebook</span>
                                                @elseif($sourceName == 'instagram')
                                                    📷 <span>Instagram</span>
                                                @elseif($sourceName == 'twitter' || $sourceName == 'x.com')
                                                    🐦 <span>Twitter</span>
                                                @elseif($sourceName == 'youtube')
                                                    📺 <span>YouTube</span>
                                                @elseif($sourceName == 'linkedin')
                                                    💼 <span>LinkedIn</span>
                                                @elseif($sourceName == 'pinterest')
                                                    📌 <span>Pinterest</span>
                                                @elseif($sourceName == 'reddit')
                                                    🤖 <span>Reddit</span>
                                                @elseif($sourceName == 'tiktok')
                                                    🎵 <span>TikTok</span>
                                                @elseif($sourceName == 'bing')
                                                    🔎 <span>Bing</span>
                                                @elseif($sourceName == 'yahoo')
                                                    📧 <span>Yahoo</span>
                                                @elseif($sourceName == 'duckduckgo')
                                                    🦆 <span>DuckDuckGo</span>
                                                @else
                                                    🔗 <span>{{ $source['name'] }}</span>
                                                @endif
                                            </span>
                                            <span class="source-count">{{ number_format($sourceCount) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="empty-state" style="padding: 40px 20px;">
                                    <div>لا توجد بيانات</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- VISITORS OVER TIME -->
                    <div class="col-12">
                        <div class="hero-card">
                            <div class="metric-icon">📊</div>
                            <div class="metric-label">الزوار - آخر 7 أيام</div>
                            <div class="chart-container" style="height: 200px; margin-top: 16px;">
                                <canvas id="visitorsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- TOP PAGES & VISITS -->
        <div class="row">
            <!-- TOP PAGES -->
            <div class="col-lg-4 mb-4">
                <div class="section-card">
                    <h2 class="section-title">أفضل الصفحات</h2>
                    @if($topPages->count() > 0)
                        @php
                            $maxVisits = $topPages->first()->views ?? 1;
                        @endphp
                        @foreach($topPages as $page)
                            @php
                                // Decode URL-encoded paths
                                $decodedPath = urldecode($page->path);
                                $pageUrl = 'https://' . $site->domain . $decodedPath;
                            @endphp
                            <div class="page-row" style="--progress-width: {{ ($page->views / $maxVisits) * 100 }}%;">
                                <div class="page-row-content">
                                    <a href="{{ $pageUrl }}" target="_blank" class="page-path-link" title="{{ $decodedPath }}">
                                        <code class="page-path">{{ Str::limit($decodedPath, 50) }}</code>
                                    </a>
                                    <span class="page-visits">{{ number_format($page->views) }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">📄</div>
                            <div>لا توجد بيانات للصفحات</div>
                        </div>
                    @endif
                </div>
            </div>
      
        
    
            <div class="col-lg-8">
                <div class="section-card">
                    <h2 class="section-title">الزيارات والمسارات الأخيرة</h2>
            @if(isset($visitsWithPaths) && $visitsWithPaths->count() > 0)
                <div class="visits-table-container">
                    <table class="visits-table">
                        <thead>
                            <tr>
                                <th>من الصفحة</th>
                                <th>إلى الصفحة</th>
                                <th>الوقت</th>
                                <th>الدولة</th>
                                <th>عنوان IP</th>
                                <th>الجهاز</th>
                                <th>المتصفح</th>
                                <th>عدد المسارات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visitsWithPaths as $visit)
                                @php
                                    $routeName = isset($isAdminRoute) && $isAdminRoute 
                                        ? 'admin.analytics.visit-details' 
                                        : 'user.analytics.visit-details';
                                @endphp
                                @php
                                    // Decode URL-encoded paths
                                    $decodedEntryPath = urldecode($visit['entry_path']);
                                    $decodedExitPath = urldecode($visit['exit_path']);
                                    
                                    // Determine source to display
                                    $referrerUrl = $visit['referrer'] ?? null;
                                    $siteDomain = $visit['site_domain'] ?? $site->domain;
                                    $referrerSource = $visit['referrer_source'] ?? 'Direct';
                                    
                                    // Check if referrer is from same domain
                                    $isSameDomain = false;
                                    if ($referrerUrl) {
                                        $referrerHost = parse_url($referrerUrl, PHP_URL_HOST);
                                        $isSameDomain = $referrerHost && (
                                            $referrerHost === $siteDomain || 
                                            $referrerHost === 'www.' . $siteDomain ||
                                            'www.' . $referrerHost === $siteDomain
                                        );
                                    }
                                    
                                    // Source to display: if same domain show entry_path, else show referrer_source
                                    if ($isSameDomain || $referrerSource === 'Direct') {
                                        // Same domain or direct: show entry path
                                        $sourceDisplay = $decodedEntryPath;
                                        $sourceUrl = 'https://' . $site->domain . $decodedEntryPath;
                                    } else {
                                        // External source: show referrer source name
                                        $sourceDisplay = $referrerSource;
                                        $sourceUrl = $referrerUrl;
                                    }
                                    
                                    // Build full URLs for clickable links
                                    $entryUrl = $site->domain . $decodedEntryPath;
                                    $exitUrl = $site->domain . $decodedExitPath;
                                @endphp
                                <tr class="visits-table-row">
                                    <td class="visits-table-path">
                                        @if($sourceUrl)
                                            <a href="{{ $sourceUrl }}" target="_blank" class="path-link-clickable" title="{{ $sourceDisplay }}">
                                                <code>{{ Str::limit($sourceDisplay, 40) }}</code>
                                            </a>
                                        @else
                                            <code class="path-link" title="{{ $sourceDisplay }}">
                                                {{ Str::limit($sourceDisplay, 40) }}
                                            </code>
                                        @endif
                                    </td>
                                    <td class="visits-table-path">
                                        <a href="https://{{ $exitUrl }}" target="_blank" class="path-link-clickable" title="{{ $decodedExitPath }}">
                                            <code>{{ Str::limit($decodedExitPath, 40) }}</code>
                                        </a>
                                    </td>
                                    <td class="visits-table-time">
                                        <span>{{ \Carbon\Carbon::parse($visit['first_seen'])->diffForHumans() }}</span>
                                    </td>
                                    <td class="visits-table-country">
                                        <span title="{{ $visit['country'] ?? 'غير معروف' }}">
                                            {{ $visit['country'] ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="visits-table-ip">
                                        <code class="ip-address">{{ $visit['ip'] ?? '—' }}</code>
                                    </td>
                                    <td class="visits-table-device">
                                        @if($visit['device_type'] == 'desktop')
                                            <span title="Desktop">🖥️ Desktop</span>
                                        @elseif($visit['device_type'] == 'mobile')
                                            <span title="Mobile">📱 Mobile</span>
                                        @elseif($visit['device_type'] == 'tablet')
                                            <span title="Tablet">📱 Tablet</span>
                                        @else
                                            <span>—</span>
                                        @endif
                                    </td>
                                    <td class="visits-table-browser">
                                        @php
                                            $browser = strtolower($visit['browser'] ?? '');
                                            $version = $visit['browser_version'] ?? '';
                                        @endphp
                                        @if(strpos($browser, 'chrome') !== false)
                                            <span title="Chrome {{ $version }}">🌐 Chrome</span>
                                        @elseif(strpos($browser, 'safari') !== false && strpos($browser, 'chrome') === false)
                                            <span title="Safari {{ $version }}">🧭 Safari</span>
                                        @elseif(strpos($browser, 'firefox') !== false)
                                            <span title="Firefox {{ $version }}">🦊 Firefox</span>
                                        @elseif(strpos($browser, 'edge') !== false)
                                            <span title="Edge {{ $version }}">🔷 Edge</span>
                                        @else
                                            <span title="{{ $visit['browser'] ?? 'Unknown' }}">{{ $visit['browser'] ?? '—' }}</span>
                                        @endif
                                    </td>
                                    <td class="visits-table-paths-count">
                                        <a href="{{ route($routeName, ['site' => $site->site_key, 'sessionId' => $visit['session_id']]) }}" 
                                           class="paths-count-link" 
                                           title="عرض تفاصيل المسار">
                                            {{ $visit['paths_count'] ?? 0 }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($visitsWithPaths->hasPages())
                    <div class="visits-pagination">
                        {{ $visitsWithPaths->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">🔄</div>
                    <div>لا توجد بيانات للزيارات</div>
                </div>
            @endif
                </div>
            </div>
        </div>
        
        <!-- BROWSERS -->
        <div class="row">
            <div class="col-lg-4 mb-4">
                @if($topBrowsers->count() > 0)
                <div class="section-card">
                    <h2 class="section-title">المتصفحات</h2>
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="browsersChart"></canvas>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="/js/chartjs.min.js"></script>
<script>
// Active Users Chart (Hero - Last 30 minutes)
@if(isset($activeUsersData) && count($activeUsersData) > 0)
const activeUsersCtx = document.getElementById('activeUsersChart');
if (activeUsersCtx) {
    new Chart(activeUsersCtx, {
        type: 'line',
        data: {
            labels: [
                @foreach($activeUsersData as $point)
                "{{ $point['time'] }}",
                @endforeach
            ],
            datasets: [{
                label: 'مستخدمون نشطون',
                data: [
                    @foreach($activeUsersData as $point)
                    {{ $point['count'] }},
                    @endforeach
                ],
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderColor: '#10b981',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
}
@endif

// Visitors Last 7 Days Chart (Hero - Line Chart)
@if(isset($visitorsLast7Days) && count($visitorsLast7Days) > 0)
const visitorsCtx = document.getElementById('visitorsChart');
if (visitorsCtx) {
    new Chart(visitorsCtx, {
        type: 'line',
        data: {
            labels: [
                @foreach($visitorsLast7Days as $day)
                "{{ $day['label'] }}",
                @endforeach
            ],
            datasets: [{
                label: 'زوار',
                data: [
                    @foreach($visitorsLast7Days as $day)
                    {{ $day['count'] }},
                    @endforeach
                ],
                backgroundColor: 'rgba(123, 96, 251, 0.1)',
                borderColor: '#7b60fb',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#7b60fb',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
}
@endif

// Browsers Chart (Doughnut Chart with Percentage)
@if(isset($topBrowsers) && $topBrowsers->count() > 0)
const browsersCtx = document.getElementById('browsersChart');
if (browsersCtx) {
    @php
        $browserColors = [
            'Chrome' => 'rgba(66, 133, 244, 0.8)',
            'Safari' => 'rgba(0, 122, 255, 0.8)',
            'Firefox' => 'rgba(255, 102, 0, 0.8)',
            'Edge' => 'rgba(0, 120, 212, 0.8)',
            'Opera' => 'rgba(255, 0, 0, 0.8)',
        ];
        $browserBorderColors = [
            'Chrome' => 'rgba(66, 133, 244, 1)',
            'Safari' => 'rgba(0, 122, 255, 1)',
            'Firefox' => 'rgba(255, 102, 0, 1)',
            'Edge' => 'rgba(0, 120, 212, 1)',
            'Opera' => 'rgba(255, 0, 0, 1)',
        ];
        $totalBrowsers = $topBrowsers->sum('count');
    @endphp
    new Chart(browsersCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($topBrowsers as $browser)
                "{{ $browser->browser }}",
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($topBrowsers as $browser)
                    {{ $browser->count }},
                    @endforeach
                ],
                backgroundColor: [
                    @foreach($topBrowsers as $browser)
                    '{{ $browserColors[$browser->browser] ?? 'rgba(123, 96, 251, 0.8)' }}',
                    @endforeach
                ],
                borderColor: [
                    @foreach($topBrowsers as $browser)
                    '{{ $browserBorderColors[$browser->browser] ?? '#7b60fb' }}',
                    @endforeach
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                const dataset = data.datasets[0];
                                const total = dataset.data.reduce((a, b) => a + b, 0);
                                return data.labels.map((label, i) => {
                                    const value = dataset.data[i];
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return {
                                        text: label + ' (' + percentage + '%)',
                                        fillStyle: dataset.backgroundColor[i],
                                        strokeStyle: dataset.borderColor[i],
                                        lineWidth: dataset.borderWidth,
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}
@endif

// Toggle paths expansion
function togglePaths(sessionId) {
    const pathsDiv = document.getElementById('paths-' + sessionId);
    const arrow = document.getElementById('arrow-' + sessionId);
    
    if (pathsDiv.classList.contains('expanded')) {
        pathsDiv.classList.remove('expanded');
        arrow.textContent = '▼';
    } else {
        pathsDiv.classList.add('expanded');
        arrow.textContent = '▲';
    }
}
</script>
@endsection
