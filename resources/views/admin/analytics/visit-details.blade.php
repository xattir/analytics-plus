@extends('layouts.admin')
@section('content')
<style>
    .visit-details-page {
        --analytics-bg: var(--background-1, #ffffff);
        --analytics-border: var(--border-color, #e5e7eb);
        --analytics-text: var(--color-2, #1f2937);
        --analytics-text-muted: #6b7280;
        --analytics-primary: #7b60fb;
        --analytics-success: #10b981;
        --analytics-warning: #f59e0b;
        
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif;
        color: var(--analytics-text);
        background: var(--background-0, #f9fafb);
        min-height: 100vh;
        padding: 0;
    }
    
    .visit-details-header {
        background: var(--analytics-bg);
        border-bottom: 1px solid var(--analytics-border);
        padding: 24px 32px;
        margin-bottom: 32px;
    }
    
    .visit-details-header h1 {
        font-size: 24px;
        font-weight: 600;
        margin: 0 0 8px 0;
        color: var(--analytics-text);
    }
    
    .visit-details-header .back-link {
        color: var(--analytics-primary);
        text-decoration: none;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
    }
    
    .visit-details-header .back-link:hover {
        text-decoration: underline;
    }
    
    .visit-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
        padding: 0 32px;
    }
    
    .summary-card {
        background: var(--analytics-bg);
        border: 1px solid var(--analytics-border);
        border-radius: 8px;
        padding: 16px;
    }
    
    .summary-label {
        font-size: 12px;
        color: var(--analytics-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        font-weight: 600;
    }
    
    .summary-value {
        font-size: 16px;
        font-weight: 600;
        color: var(--analytics-text);
        word-break: break-all;
    }
    
    .summary-value code {
        font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
        font-size: 13px;
        background: rgba(0, 0, 0, 0.05);
        padding: 2px 6px;
        border-radius: 4px;
    }
    
    .path-timeline {
        background: var(--analytics-bg);
        border: 1px solid var(--analytics-border);
        border-radius: 12px;
        padding: 24px 32px;
        margin: 0 32px 32px;
    }
    
    .path-timeline-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 24px;
        color: var(--analytics-text);
    }
    
    .path-timeline-item {
        display: flex;
        align-items: flex-start;
        padding: 16px 0;
        border-bottom: 1px solid var(--analytics-border);
        position: relative;
        padding-right: 60px;
    }
    
    .path-timeline-item:last-child {
        border-bottom: none;
    }
    
    .path-timeline-item::before {
        content: '';
        position: absolute;
        right: 0;
        top: 20px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--analytics-primary);
        border: 2px solid var(--analytics-bg);
        box-shadow: 0 0 0 2px var(--analytics-border);
    }
    
    .path-timeline-item::after {
        content: '';
        position: absolute;
        right: 5px;
        top: 32px;
        width: 2px;
        height: calc(100% - 12px);
        background: var(--analytics-border);
    }
    
    .path-timeline-item:last-child::after {
        display: none;
    }
    
    .path-timeline-number {
        position: absolute;
        right: -8px;
        top: 16px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--analytics-primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
        z-index: 1;
    }
    
    .path-timeline-content {
        flex: 1;
    }
    
    .path-timeline-path {
        font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
        font-size: 14px;
        color: var(--analytics-text);
        margin-bottom: 8px;
        word-break: break-all;
    }
    
    .path-timeline-meta {
        display: flex;
        gap: 16px;
        font-size: 12px;
        color: var(--analytics-text-muted);
    }
    
    .path-timeline-meta-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .path-quality-indicator {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        margin-right: 8px;
    }
    
    .path-quality-short {
        background: rgba(245, 158, 11, 0.1);
        color: var(--analytics-warning);
    }
    
    .path-quality-exit {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
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

<div class="visit-details-page">
    <div class="visit-details-header">
        @php
            $backRoute = $isAdminRoute 
                ? route('admin.analytics.show', ['site' => $site->site_key])
                : route('user.analytics.show', ['site' => $site->site_key]);
        @endphp
        <a href="{{ $backRoute }}" class="back-link">
            ← العودة إلى لوحة التحكم
        </a>
        <h1>تفاصيل المسار</h1>
        <p style="color: var(--analytics-text-muted); margin: 0; font-size: 14px;">
            {{ $site->domain }} • Session ID: <code>{{ $session->session_id }}</code>
        </p>
    </div>
    
    <!-- Visit Summary -->
    <div class="visit-summary">
        <div class="summary-card">
            <div class="summary-label">صفحة الدخول</div>
            <div class="summary-value">
                <code>{{ urldecode($session->entry_path) }}</code>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-label">صفحة الخروج</div>
            <div class="summary-value">
                <code>{{ urldecode($session->exit_path) }}</code>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-label">المدة الإجمالية</div>
            <div class="summary-value">
                @php
                    $minutes = floor($duration / 60000);
                    $seconds = floor(($duration % 60000) / 1000);
                @endphp
                @if($minutes > 0)
                    {{ $minutes }} دقيقة {{ $seconds }} ثانية
                @else
                    {{ $seconds }} ثانية
                @endif
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-label">الدولة</div>
            <div class="summary-value">
                {{ $session->country ?? '—' }}
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-label">عنوان IP</div>
            <div class="summary-value">
                <code>{{ $ipAddress }}</code>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-label">الجهاز</div>
            <div class="summary-value">
                @if($session->device_type == 'desktop')
                    🖥️ Desktop
                @elseif($session->device_type == 'mobile')
                    📱 Mobile
                @elseif($session->device_type == 'tablet')
                    📱 Tablet
                @else
                    —
                @endif
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-label">المتصفح</div>
            <div class="summary-value">
                {{ $session->browser ?? '—' }}
                @if($session->browser_version)
                    <span style="color: var(--analytics-text-muted);">({{ $session->browser_version }})</span>
                @endif
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-label">المصدر</div>
            <div class="summary-value">
                {{ $session->referrer_source ?? 'Direct' }}
            </div>
        </div>
    </div>
    
    <!-- Path Timeline -->
    <div class="path-timeline">
        <h2 class="path-timeline-title">المسار الكامل</h2>
        
        @if($paths->count() > 0)
            @foreach($paths as $path)
                @php
                    $isShortTime = $path['time_spent_ms'] && $path['time_spent_ms'] < 5000;
                    $isLastPath = $loop->last;
                @endphp
                <div class="path-timeline-item">
                    <div class="path-timeline-number">{{ $path['position'] }}</div>
                    <div class="path-timeline-content">
                        @if($isShortTime && !$isLastPath)
                            <span class="path-quality-indicator path-quality-short">وقت قصير</span>
                        @endif
                        @if($isLastPath)
                            <span class="path-quality-indicator path-quality-exit">صفحة خروج</span>
                        @endif
                        <div class="path-timeline-path">{{ urldecode($path['path']) }}</div>
                        <div class="path-timeline-meta">
                            @if($path['time_spent_ms'])
                                <div class="path-timeline-meta-item">
                                    <span>⏱️</span>
                                    <span>
                                        @php
                                            $seconds = floor($path['time_spent_ms'] / 1000);
                                        @endphp
                                        @if($seconds < 60)
                                            {{ $seconds }} ثانية
                                        @else
                                            {{ floor($seconds / 60) }} دقيقة {{ $seconds % 60 }} ثانية
                                        @endif
                                    </span>
                                </div>
                            @endif
                            @if($path['scroll_percent'] !== null)
                                <div class="path-timeline-meta-item">
                                    <span>📜</span>
                                    <span>تمرير: {{ $path['scroll_percent'] }}%</span>
                                </div>
                            @endif
                            @if($path['created_at'])
                                <div class="path-timeline-meta-item">
                                    <span>🕐</span>
                                    <span>{{ \Carbon\Carbon::parse($path['created_at'])->format('H:i:s') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📄</div>
                <div>لا توجد مسارات مسجلة</div>
            </div>
        @endif
    </div>
</div>
@endsection

