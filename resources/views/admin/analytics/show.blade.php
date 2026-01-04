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
        margin: 0;
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
        padding: 16px;
        margin-bottom: 8px;
        border-radius: 8px;
        background: var(--analytics-bg);
        border: 1px solid var(--analytics-border);
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
    }
    
    .page-row:hover {
        border-color: var(--analytics-primary);
        box-shadow: 0 2px 8px rgba(123, 96, 251, 0.1);
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
    }
    
    .page-path {
        font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
        font-size: 14px;
        color: var(--analytics-text);
        flex: 1;
        margin-left: 16px;
    }
    
    .page-visits {
        font-size: 16px;
        font-weight: 600;
        color: var(--analytics-text);
    }
    
    .visit-item {
        background: var(--analytics-bg);
        border: 1px solid var(--analytics-border);
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
        transition: all 0.2s;
    }
    
    .visit-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }
    
    .visit-paths {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--analytics-border);
        display: none;
    }
    
    .visit-paths.expanded {
        display: block;
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
    
    .source-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--analytics-border);
    }
    
    .source-item:last-child {
        border-bottom: none;
    }
    
    .source-name {
        font-weight: 500;
        color: var(--analytics-text);
    }
    
    .source-count {
        font-weight: 600;
        color: var(--analytics-text);
    }
    
    .browser-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 16px;
    }
    
    .browser-item {
        text-align: center;
        padding: 16px;
        background: var(--analytics-bg);
        border: 1px solid var(--analytics-border);
        border-radius: 8px;
        transition: all 0.2s;
    }
    
    .browser-item:hover {
        border-color: var(--analytics-primary);
        box-shadow: 0 2px 8px rgba(123, 96, 251, 0.1);
    }
    
    .browser-icon {
        font-size: 32px;
        margin-bottom: 8px;
    }
    
    .browser-name {
        font-size: 13px;
        color: var(--analytics-text-muted);
        margin-bottom: 4px;
    }
    
    .browser-percent {
        font-size: 18px;
        font-weight: 600;
        color: var(--analytics-text);
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
            <!-- Total Visitors Today -->
            <div class="col-md-4 mb-4">
                <div class="hero-card">
                    <div class="metric-icon">👥</div>
                    <div class="metric-label">الزوار اليوم</div>
                    <div class="metric-value">{{ number_format($todayStats['visitors'] ?? 0) }}</div>
                </div>
            </div>
            
            <!-- Total Page Views Today -->
            <div class="col-md-4 mb-4">
                <div class="hero-card">
                    <div class="metric-icon">📄</div>
                    <div class="metric-label">مشاهدات الصفحة اليوم</div>
                    <div class="metric-value">{{ number_format($todayStats['pageviews'] ?? 0) }}</div>
                </div>
            </div>
            
            <!-- ACTIVE USERS (HERO) -->
            <div class="col-md-4 mb-4">
                <div class="hero-card hero-card-active">
                    <div class="metric-icon">⚡</div>
                    <div class="metric-label">المستخدمون النشطون (آخر 30 دقيقة)</div>
                    <div class="metric-value">{{ number_format($activeUsersCount ?? 0) }}</div>
                    <div class="chart-container">
                        <canvas id="activeUsersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- TOP PAGES -->
        <div class="section-card">
            <h2 class="section-title">أفضل الصفحات</h2>
            @if($topPages->count() > 0)
                @php
                    $maxVisits = $topPages->first()->views ?? 1;
                @endphp
                @foreach($topPages as $page)
                    <div class="page-row" style="--progress-width: {{ ($page->views / $maxVisits) * 100 }}%;">
                        <div class="page-row-content">
                            <code class="page-path">{{ Str::limit($page->path, 80) }}</code>
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
        
        <!-- VISITS & PATHS -->
        <div class="section-card">
            <h2 class="section-title">الزيارات والمسارات</h2>
            @if(isset($visitsWithPaths) && $visitsWithPaths->count() > 0)
                @foreach($visitsWithPaths as $visit)
                    <div class="visit-item">
                        <div class="visit-header" onclick="togglePaths('{{ $visit['session_id'] }}')">
                            <div>
                                <div style="font-weight: 600; margin-bottom: 4px;">
                                    <code style="color: var(--analytics-primary);">{{ Str::limit($visit['entry_path'], 60) }}</code>
                                </div>
                                <div style="font-size: 13px; color: var(--analytics-text-muted);">
                                    خروج: <code>{{ Str::limit($visit['exit_path'], 60) }}</code>
                                </div>
                            </div>
                            <div style="text-align: left;">
                                <span style="font-weight: 600; color: var(--analytics-primary); cursor: pointer;">
                                    {{ $visit['paths_count'] }} مسار
                                    <span id="arrow-{{ $visit['session_id'] }}">▼</span>
                                </span>
                            </div>
                        </div>
                        <div class="visit-paths" id="paths-{{ $visit['session_id'] }}">
                            <div class="path-sequence">
                                @foreach($visit['paths'] as $index => $path)
                                    <span class="path-badge">{{ Str::limit($path, 50) }}</span>
                                    @if(!$loop->last)
                                        <span class="path-arrow">→</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">🔄</div>
                    <div>لا توجد بيانات للزيارات</div>
                </div>
            @endif
        </div>
        
        <div class="row">
            <!-- VISITORS OVER TIME -->
            <div class="col-md-8 mb-4">
                <div class="section-card">
                    <h2 class="section-title">الزوار - آخر 7 أيام</h2>
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="visitorsChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- TOP TRAFFIC SOURCES -->
            <div class="col-md-4 mb-4">
                <div class="section-card">
                    <h2 class="section-title">أفضل المصادر</h2>
                    @if(isset($topTrafficSources) && $topTrafficSources->count() > 0)
                        @foreach($topTrafficSources as $source)
                            <div class="source-item">
                                <span class="source-name">
                                    @php
                                        $sourceName = strtolower($source['name'] ?? '');
                                    @endphp
                                    @if($source['type'] == 'direct' || $sourceName == 'direct')
                                        🔗 مباشر
                                    @elseif($sourceName == 'google')
                                        🔍 Google
                                    @elseif($sourceName == 'facebook' || $sourceName == 'fb.com')
                                        📘 Facebook
                                    @elseif($sourceName == 'instagram')
                                        📷 Instagram
                                    @elseif($sourceName == 'twitter' || $sourceName == 'x.com')
                                        🐦 Twitter
                                    @elseif($sourceName == 'youtube')
                                        📺 YouTube
                                    @elseif($sourceName == 'linkedin')
                                        💼 LinkedIn
                                    @elseif($sourceName == 'pinterest')
                                        📌 Pinterest
                                    @elseif($sourceName == 'reddit')
                                        🤖 Reddit
                                    @elseif($sourceName == 'tiktok')
                                        🎵 TikTok
                                    @elseif($sourceName == 'bing')
                                        🔎 Bing
                                    @elseif($sourceName == 'yahoo')
                                        📧 Yahoo
                                    @elseif($sourceName == 'duckduckgo')
                                        🦆 DuckDuckGo
                                    @else
                                        🔗 {{ $source['name'] }}
                                    @endif
                                </span>
                                <span class="source-count">{{ number_format($source['count']) }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state" style="padding: 40px 20px;">
                            <div>لا توجد بيانات</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- BROWSERS -->
        @if($topBrowsers->count() > 0)
        <div class="section-card">
            <h2 class="section-title">المتصفحات</h2>
            <div class="browser-grid">
                @foreach($topBrowsers as $browser)
                    @php
                        $browserIcons = [
                            'Chrome' => '🌐',
                            'Safari' => '🧭',
                            'Firefox' => '🦊',
                            'Edge' => '🔷',
                            'Opera' => '🎭',
                        ];
                        $icon = $browserIcons[$browser->browser] ?? '🌐';
                        $totalBrowsers = $topBrowsers->sum('count');
                        $percent = $totalBrowsers > 0 ? round(($browser->count / $totalBrowsers) * 100, 1) : 0;
                    @endphp
                    <div class="browser-item" title="{{ $browser->browser }}: {{ number_format($browser->count) }} جلسة">
                        <div class="browser-icon">{{ $icon }}</div>
                        <div class="browser-name">{{ $browser->browser }}</div>
                        <div class="browser-percent">{{ $percent }}%</div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
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

// Visitors Last 7 Days Chart
@if(isset($visitorsLast7Days) && count($visitorsLast7Days) > 0)
const visitorsCtx = document.getElementById('visitorsChart');
if (visitorsCtx) {
    new Chart(visitorsCtx, {
        type: 'bar',
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
                borderRadius: 6,
                borderSkipped: false,
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
