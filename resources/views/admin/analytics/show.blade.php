@extends('layouts.admin', ['page_title' => 'لوحة تحكم التحليلات - ' . $site->domain])

@section('styles')
<style>
    .analytics-dashboard {
        --analytics-bg: var(--background-1, #fff);
        --analytics-border: var(--border-color, #e5e7eb);
        --analytics-text: var(--color-2, #1f2937);
        --analytics-text-muted: #6b7280;
        --analytics-primary: #7b60fb;
        --analytics-success: #10b981;
        --analytics-warning: #f59e0b;
        --analytics-danger: #ef4444;
        --analytics-neutral: #94a3b8;
        
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif;
        color: var(--analytics-text);
    }
    
    .metric-card {
        background: var(--analytics-bg);
        border: 1px solid var(--analytics-border);
        border-radius: 8px;
        padding: 20px;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .metric-card:hover {
        border-color: var(--analytics-primary);
        box-shadow: 0 4px 12px rgba(123, 96, 251, 0.1);
    }
    
    .metric-value {
        font-size: 28px;
        font-weight: 600;
        line-height: 1.2;
        margin: 8px 0 4px;
        color: var(--analytics-text);
    }
    
    .metric-label {
        font-size: 13px;
        color: var(--analytics-text-muted);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .metric-trend {
        font-size: 12px;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .trend-up { color: var(--analytics-success); }
    .trend-down { color: var(--analytics-danger); }
    .trend-neutral { color: var(--analytics-neutral); }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--analytics-border);
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--analytics-text);
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--analytics-bg);
        border-radius: 8px;
        overflow: hidden;
    }
    
    .data-table thead {
        background: var(--analytics-bg);
        border-bottom: 2px solid var(--analytics-border);
    }
    
    .data-table th {
        padding: 12px 16px;
        text-align: right;
        font-size: 12px;
        font-weight: 600;
        color: var(--analytics-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .data-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--analytics-border);
        font-size: 14px;
    }
    
    .data-table tbody tr {
        transition: background 0.15s;
    }
    
    .data-table tbody tr:hover {
        background: rgba(123, 96, 251, 0.03);
    }
    
    .data-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .badge-quality {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .badge-high { background: rgba(16, 185, 129, 0.1); color: var(--analytics-success); }
    .badge-medium { background: rgba(245, 158, 11, 0.1); color: var(--analytics-warning); }
    .badge-low { background: rgba(239, 68, 68, 0.1); color: var(--analytics-danger); }
    .badge-bot { background: rgba(148, 163, 184, 0.1); color: var(--analytics-neutral); }
    
    .path-link {
        color: var(--analytics-primary);
        text-decoration: none;
        font-family: 'Monaco', 'Menlo', monospace;
        font-size: 13px;
    }
    
    .path-link:hover {
        text-decoration: underline;
    }
    
    .quality-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-left: 8px;
    }
    
    .quality-good { background: var(--analytics-success); }
    .quality-warning { background: var(--analytics-warning); }
    .quality-bad { background: var(--analytics-danger); }
    
    .flow-entry {
        padding: 12px;
        background: var(--analytics-bg);
        border: 1px solid var(--analytics-border);
        border-radius: 6px;
        margin-bottom: 12px;
    }
    
    .flow-path {
        display: inline-block;
        padding: 6px 12px;
        background: rgba(123, 96, 251, 0.1);
        border-radius: 4px;
        margin: 4px;
        font-size: 12px;
        font-family: 'Monaco', 'Menlo', monospace;
    }
    
    .date-filter {
        display: flex;
        gap: 12px;
        align-items: center;
        padding: 12px;
        background: var(--analytics-bg);
        border: 1px solid var(--analytics-border);
        border-radius: 8px;
        margin-bottom: 24px;
    }
    
    .date-filter input {
        border: 1px solid var(--analytics-border);
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 14px;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--analytics-text-muted);
    }
    
    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
</style>
@endsection

@section('content')
<div class="col-12 p-3 analytics-dashboard">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 style="font-weight: 600; margin: 0;">{{ $site->domain }}</h3>
            <p style="color: var(--analytics-text-muted); margin: 4px 0 0; font-size: 14px;">لوحة تحكم التحليلات</p>
        </div>
        <div>
            @if(isset($isAdminRoute) && $isAdminRoute)
                <a href="{{ route('admin.analytics.tracking-code', ['site' => $site->site_key]) }}" class="btn btn-sm btn-success">كود التتبع</a>
                @if(isset($isSuperAdmin) && $isSuperAdmin || $site->user_id == auth()->id())
                    <a href="{{ route('admin.analytics.members', ['site' => $site->site_key]) }}" class="btn btn-sm btn-primary">إدارة الفريق</a>
                @endif
                <a href="{{ route('admin.analytics.index') }}" class="btn btn-sm btn-secondary">المواقع</a>
            @else
                <a href="{{ route('user.analytics.tracking-code', ['site' => $site->site_key]) }}" class="btn btn-sm btn-success">كود التتبع</a>
                @if($site->user_id == auth()->id())
                    <a href="{{ route('user.analytics.members', ['site' => $site->site_key]) }}" class="btn btn-sm btn-primary">إدارة الفريق</a>
                @endif
                <a href="{{ route('user.analytics.index') }}" class="btn btn-sm btn-secondary">المواقع</a>
            @endif
        </div>
    </div>
    
    <!-- Date Filter -->
    <form method="GET" action="{{ (isset($isAdminRoute) && $isAdminRoute) ? route('admin.analytics.show', ['site' => $site->site_key]) : route('user.analytics.show', ['site' => $site->site_key]) }}" class="date-filter">
        <label style="font-size: 13px; font-weight: 500; color: var(--analytics-text-muted);">من:</label>
        <input type="date" name="date_from" value="{{ $dateFrom }}" style="flex: 1; max-width: 200px;">
        <label style="font-size: 13px; font-weight: 500; color: var(--analytics-text-muted);">إلى:</label>
        <input type="date" name="date_to" value="{{ $dateTo }}" style="flex: 1; max-width: 200px;">
        <button type="submit" class="btn btn-primary" style="padding: 8px 20px;">تطبيق</button>
    </form>
    
    <!-- 1. OVERVIEW METRICS -->
    <div class="section-header">
        <h4 class="section-title">نظرة عامة</h4>
    </div>
    <div class="row mb-5">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="metric-card">
                <div class="metric-label">إجمالي الجلسات</div>
                <div class="metric-value">{{ number_format($stats['total_sessions']) }}</div>
                <div class="metric-trend trend-neutral">
                    <span>جميع الجلسات</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="metric-card">
                <div class="metric-label">متوسط مدة الجلسة</div>
                <div class="metric-value">{{ gmdate('i:s', ($stats['avg_duration'] ?? 0) / 1000) }}</div>
                <div class="metric-trend">
                    <span>{{ number_format($stats['avg_pages_per_session'] ?? 0, 1) }} صفحة/جلسة</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="metric-card">
                <div class="metric-label">متوسط الصفحات</div>
                <div class="metric-value">{{ number_format($stats['avg_pages_per_session'] ?? 0, 1) }}</div>
                <div class="metric-trend">
                    <span>لكل جلسة</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="metric-card">
                <div class="metric-label">معدل الارتداد</div>
                <div class="metric-value">{{ number_format($stats['bounce_rate'], 1) }}%</div>
                <div class="metric-trend {{ $stats['bounce_rate'] < 50 ? 'trend-up' : ($stats['bounce_rate'] > 70 ? 'trend-down' : 'trend-neutral') }}">
                    <span>{{ $stats['bounce_rate'] < 50 ? 'جيد' : ($stats['bounce_rate'] > 70 ? 'ضعيف' : 'متوسط') }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-5">
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="metric-card">
                <div class="metric-label">الجلسات العائدة</div>
                <div class="metric-value">{{ number_format($stats['returning_sessions_pct'] ?? 0, 1) }}%</div>
                <div class="metric-trend">
                    <span>{{ number_format($stats['returning_visitors']) }} جلسة</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="metric-card">
                <div class="metric-label">الزوار الفريدون</div>
                <div class="metric-value">{{ number_format($stats['unique_visitors']) }}</div>
                <div class="metric-trend">
                    <span>من {{ number_format($stats['total_sessions']) }} جلسة</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="metric-card">
                <div class="metric-label">إجمالي المشاهدات</div>
                <div class="metric-value">{{ number_format($stats['total_pageviews']) }}</div>
                <div class="metric-trend">
                    <span>مشاهدات الصفحة</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 2. TRAFFIC QUALITY -->
    <div class="section-header">
        <h4 class="section-title">جودة الزيارات</h4>
    </div>
    <div class="row mb-5">
        <div class="col-md-6 mb-3">
            <div class="metric-card">
                <div class="metric-label">الجلسات الحقيقية</div>
                <div class="metric-value">{{ number_format($trafficQuality['real_sessions']) }}</div>
                <div class="metric-trend trend-up">
                    <span class="quality-indicator quality-good"></span>
                    {{ $trafficQuality['total_sessions'] > 0 ? number_format(($trafficQuality['real_sessions'] / $trafficQuality['total_sessions']) * 100, 1) : 0 }}% من الإجمالي
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="metric-card">
                <div class="metric-label">جلسات البوت</div>
                <div class="metric-value">{{ number_format($trafficQuality['bot_sessions']) }}</div>
                <div class="metric-trend trend-down">
                    <span class="quality-indicator quality-bad"></span>
                    {{ $trafficQuality['total_sessions'] > 0 ? number_format(($trafficQuality['bot_sessions'] / $trafficQuality['total_sessions']) * 100, 1) : 0 }}% من الإجمالي
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-5">
        <div class="col-md-6 mb-3">
            <div class="metric-card">
                <div class="metric-label">جلسات عالية الجودة</div>
                <div class="metric-value">{{ number_format($trafficQuality['high_quality']) }}</div>
                <div class="metric-trend trend-up">
                    <span class="quality-indicator quality-good"></span>
                    صفحات متعددة + وقت طويل + تمرير جيد
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="metric-card">
                <div class="metric-label">جلسات منخفضة الجودة</div>
                <div class="metric-value">{{ number_format($trafficQuality['low_quality']) }}</div>
                <div class="metric-trend trend-down">
                    <span class="quality-indicator quality-bad"></span>
                    صفحة واحدة أو وقت قصير أو تمرير ضعيف
                </div>
            </div>
        </div>
    </div>
    
    <!-- 3. PAGES PERFORMANCE TABLE -->
    <div class="section-header">
        <h4 class="section-title">أداء الصفحات</h4>
    </div>
    <div class="mb-5" style="background: var(--analytics-bg); border: 1px solid var(--analytics-border); border-radius: 8px; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="text-align: right;">المسار</th>
                        <th>الجلسات</th>
                        <th>الدخول</th>
                        <th>الخروج</th>
                        <th>متوسط الوقت (ث)</th>
                        <th>متوسط التمرير %</th>
                        <th>معدل الارتداد %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagePerformance as $page)
                    <tr style="cursor: pointer;" onclick="viewPageDetails('{{ $page->path }}')">
                        <td>
                            <code class="path-link">{{ Str::limit($page->path, 60) }}</code>
                        </td>
                        <td style="text-align: center; font-weight: 600;">{{ number_format($page->sessions) }}</td>
                        <td style="text-align: center;">{{ number_format($page->entrances) }}</td>
                        <td style="text-align: center;">{{ number_format($page->exits) }}</td>
                        <td style="text-align: center;">{{ number_format($page->avg_time_on_page, 1) }}</td>
                        <td style="text-align: center;">
                            <span class="badge-quality {{ $page->avg_scroll_percent > 70 ? 'badge-high' : ($page->avg_scroll_percent > 40 ? 'badge-medium' : 'badge-low') }}">
                                {{ number_format($page->avg_scroll_percent, 0) }}%
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-quality {{ $page->bounce_rate < 50 ? 'badge-high' : ($page->bounce_rate < 70 ? 'badge-medium' : 'badge-low') }}">
                                {{ number_format($page->bounce_rate, 1) }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <div class="empty-state-icon">📄</div>
                            <div>لا توجد بيانات للصفحات في هذه الفترة</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- 4. USER FLOW -->
    <div class="section-header">
        <h4 class="section-title">مسار المستخدم</h4>
    </div>
    <div class="mb-5">
        @forelse($userFlow as $flow)
        <div class="flow-entry">
            <div style="font-weight: 600; margin-bottom: 12px; color: var(--analytics-primary);">
                <code>{{ Str::limit($flow['entry'], 80) }}</code>
                <span style="color: var(--analytics-text-muted); font-weight: 400; margin-right: 8px;">
                    ({{ number_format($flow['entry_count']) }} دخول)
                </span>
            </div>
            @if($flow['next_paths']->count() > 0)
            <div style="margin-top: 8px;">
                <span style="font-size: 12px; color: var(--analytics-text-muted); margin-left: 8px;">الصفحات التالية:</span>
                @foreach($flow['next_paths'] as $next)
                <span class="flow-path">
                    {{ Str::limit($next->path, 40) }} <strong>({{ $next->count }})</strong>
                </span>
                @endforeach
            </div>
            @else
            <div style="color: var(--analytics-text-muted); font-size: 13px; margin-top: 8px;">
                معظم المستخدمين يغادرون من هذه الصفحة
            </div>
            @endif
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-state-icon">🔄</div>
            <div>لا توجد بيانات للمسار في هذه الفترة</div>
        </div>
        @endforelse
    </div>
    
    <!-- 5. DEVICES & BROWSERS -->
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="section-header">
                <h5 class="section-title" style="font-size: 16px;">الأجهزة</h5>
            </div>
            <div style="background: var(--analytics-bg); border: 1px solid var(--analytics-border); border-radius: 8px; overflow: hidden;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>النوع</th>
                            <th>الجلسات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topDevices as $device)
                        <tr>
                            <td>
                                @if($device->device_type == 'desktop')
                                    💻
                                @elseif($device->device_type == 'mobile')
                                    📱
                                @else
                                    📲
                                @endif
                                {{ ucfirst($device->device_type) }}
                            </td>
                            <td style="text-align: center; font-weight: 600;">{{ number_format($device->count) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="empty-state" style="padding: 40px;">
                                <div>لا توجد بيانات</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="section-header">
                <h5 class="section-title" style="font-size: 16px;">المتصفحات</h5>
            </div>
            <div style="background: var(--analytics-bg); border: 1px solid var(--analytics-border); border-radius: 8px; overflow: hidden;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>المتصفح</th>
                            <th>الجلسات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topBrowsers as $browser)
                        <tr>
                            <td>{{ $browser->browser }}</td>
                            <td style="text-align: center; font-weight: 600;">{{ number_format($browser->count) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="empty-state" style="padding: 40px;">
                                <div>لا توجد بيانات</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="section-header">
                <h5 class="section-title" style="font-size: 16px;">أنظمة التشغيل</h5>
            </div>
            <div style="background: var(--analytics-bg); border: 1px solid var(--analytics-border); border-radius: 8px; overflow: hidden;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>النظام</th>
                            <th>الجلسات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topOs as $os)
                        <tr>
                            <td>{{ $os->os }}</td>
                            <td style="text-align: center; font-weight: 600;">{{ number_format($os->count) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="empty-state" style="padding: 40px;">
                                <div>لا توجد بيانات</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- 6. GEO & SOURCE -->
    <div class="row mb-5">
        <div class="col-md-6 mb-4">
            <div class="section-header">
                <h5 class="section-title" style="font-size: 16px;">الدول</h5>
            </div>
            <div style="background: var(--analytics-bg); border: 1px solid var(--analytics-border); border-radius: 8px; overflow: hidden;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>الدولة</th>
                            <th>الجلسات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topCountries as $country)
                        <tr>
                            <td>
                                <span style="font-size: 18px; margin-left: 8px;">{{ $country->country }}</span>
                            </td>
                            <td style="text-align: center; font-weight: 600;">{{ number_format($country->count) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="empty-state" style="padding: 40px;">
                                <div>لا توجد بيانات</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="section-header">
                <h5 class="section-title" style="font-size: 16px;">مصادر الزيارات</h5>
            </div>
            @if($sourceQuality->count() > 0)
            <div style="background: var(--analytics-bg); border: 1px solid var(--analytics-border); border-radius: 8px; overflow: hidden;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>المصدر</th>
                            <th>الجلسات</th>
                            <th>المدة</th>
                            <th>الارتداد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sourceQuality as $source)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $source->utm_source }}</div>
                                @if($source->utm_campaign)
                                <div style="font-size: 11px; color: var(--analytics-text-muted);">{{ $source->utm_campaign }}</div>
                                @endif
                            </td>
                            <td style="text-align: center; font-weight: 600;">{{ number_format($source->sessions) }}</td>
                            <td style="text-align: center;">{{ number_format($source->avg_duration, 0) }}ث</td>
                            <td style="text-align: center;">
                                <span class="badge-quality {{ $source->bounce_rate < 50 ? 'badge-high' : ($source->bounce_rate < 70 ? 'badge-medium' : 'badge-low') }}">
                                    {{ number_format($source->bounce_rate, 0) }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-icon">📊</div>
                <div>لا توجد بيانات للمصادر في هذه الفترة</div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Time Series Chart (Minimal) -->
    @if($timeSeries->count() > 0)
    <div class="section-header">
        <h4 class="section-title">الاتجاه الزمني</h4>
    </div>
    <div class="mb-5" style="background: var(--analytics-bg); border: 1px solid var(--analytics-border); border-radius: 8px; padding: 20px;">
        <canvas id="timeSeriesChart" style="max-height: 200px;"></canvas>
    </div>
    @endif
</div>

@section('scripts')
<script src="/js/chartjs.min.js"></script>
<script>
// Time Series Chart (minimal, data-focused)
@if($timeSeries->count() > 0)
const timeSeriesCtx = document.getElementById('timeSeriesChart');
if (timeSeriesCtx) {
    new Chart(timeSeriesCtx, {
        type: 'line',
        data: {
            labels: [
                @foreach($timeSeries as $data)
                "{{ isset($data->date) ? \Carbon\Carbon::parse($data->date)->format('M d') : 'Week ' . $data->week }}",
                @endforeach
            ],
            datasets: [{
                label: 'الجلسات',
                data: [
                    @foreach($timeSeries as $data)
                    {{ $data->sessions ?? 0 }},
                    @endforeach
                ],
                backgroundColor: 'rgba(123, 96, 251, 0.05)',
                borderColor: '#7b60fb',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 3,
                pointHoverRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
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
                        }
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

function viewPageDetails(path) {
    // TODO: Implement page detail view
    console.log('View details for:', path);
}
</script>
@endsection
