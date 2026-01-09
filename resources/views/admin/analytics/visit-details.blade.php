@extends('layouts.admin', ['page_title' => 'تفاصيل الزيارة - ' . $site->title])

@section('content')
<style>
    .visit-details-container {
        background: var(--background-1, #ffffff);
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .visit-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--border-color, #e5e7eb);
    }
    
    .visit-header h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: var(--color-1, #1f2937);
    }
    
    .info-card {
        background: var(--background-0, #f8fafc);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: var(--color-2, #575f66);
        font-size: 14px;
    }
    
    .info-value {
        color: var(--color-1, #1f2937);
        font-size: 14px;
        text-align: left;
    }
    
    .paths-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--background-1, #ffffff);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-top: 24px;
    }
    
    .paths-table thead {
        background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%);
        color: white;
    }
    
    .paths-table thead th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
    }
    
    .paths-table tbody tr {
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        transition: all 0.2s;
    }
    
    .paths-table tbody tr:hover {
        background: var(--background-0, #f8fafc);
    }
    
    .paths-table tbody td {
        padding: 16px;
        font-size: 14px;
        color: var(--color-2, #575f66);
        text-align: left;
    }
    
    .path-url {
        font-family: 'Courier New', monospace;
        color: var(--analytics-primary, #7b60fb);
        word-break: break-all;
    }
    
    .btn-modern {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
    }
    
    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        color: white;
    }
    
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-success {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }
    
    .badge-info {
        background: rgba(99, 102, 241, 0.1);
        color: #6366f1;
    }
</style>

<div class="col-12 p-3">
    <div class="visit-details-container">
        <div class="visit-header">
            <div>
                <h2>تفاصيل الزيارة</h2>
                <p style="margin: 8px 0 0 0; color: var(--color-2, #575f66); font-size: 14px;">
                    {{ $site->title }} ({{ $site->domain }})
                </p>
            </div>
            <div>
                <a href="{{ route($isAdminRoute ? 'admin.analytics.show' : 'user.analytics.show', $site) }}" class="btn-modern">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="info-card">
                    <h3 style="margin-top: 0; margin-bottom: 16px; font-size: 18px; color: var(--color-1, #1f2937);">معلومات الجلسة</h3>
                    <div class="info-row">
                        <span class="info-label">معرف الجلسة:</span>
                        <span class="info-value">{{ $session->session_id }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">عنوان IP:</span>
                        <span class="info-value">{{ $ipAddress }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">البلد:</span>
                        <span class="info-value">{{ $session->country ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">المدينة:</span>
                        <span class="info-value">{{ $session->city ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">مزود الخدمة:</span>
                        <span class="info-value">{{ $session->isp ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-card">
                    <h3 style="margin-top: 0; margin-bottom: 16px; font-size: 18px; color: var(--color-1, #1f2937);">معلومات الجهاز</h3>
                    <div class="info-row">
                        <span class="info-label">نوع الجهاز:</span>
                        <span class="info-value">
                            <span class="badge badge-info">{{ $session->device_type ?? 'N/A' }}</span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">نظام التشغيل:</span>
                        <span class="info-value">{{ $session->os ?? 'N/A' }} {{ $session->os_version ?? '' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">المتصفح:</span>
                        <span class="info-value">{{ $session->browser ?? 'N/A' }} {{ $session->browser_version ?? '' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">دقة الشاشة:</span>
                        <span class="info-value">{{ $session->screen_width ?? 'N/A' }} × {{ $session->screen_height ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">نوع الشبكة:</span>
                        <span class="info-value">{{ $session->network_type ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="info-card">
                    <h3 style="margin-top: 0; margin-bottom: 16px; font-size: 18px; color: var(--color-1, #1f2937);">معلومات الزيارة</h3>
                    <div class="info-row">
                        <span class="info-label">تاريخ البدء:</span>
                        <span class="info-value">{{ $session->first_seen->format('Y-m-d H:i:s') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">تاريخ الانتهاء:</span>
                        <span class="info-value">{{ $session->last_seen->format('Y-m-d H:i:s') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">المدة:</span>
                        <span class="info-value">{{ number_format($duration / 1000, 2) }} ثانية</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">عدد الصفحات:</span>
                        <span class="info-value">
                            <span class="badge badge-success">{{ $session->pages_count ?? 0 }}</span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">المصدر:</span>
                        <span class="info-value">{{ $session->referrer ?? 'Direct' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">مسار الدخول:</span>
                        <span class="info-value">{{ $session->entry_path ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">مسار الخروج:</span>
                        <span class="info-value">{{ $session->exit_path ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-card">
                    <h3 style="margin-top: 0; margin-bottom: 16px; font-size: 18px; color: var(--color-1, #1f2937);">معلومات إضافية</h3>
                    <div class="info-row">
                        <span class="info-label">عائد:</span>
                        <span class="info-value">
                            <span class="badge {{ $session->is_returning ? 'badge-success' : 'badge-info' }}">
                                {{ $session->is_returning ? 'نعم' : 'لا' }}
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">نطاط:</span>
                        <span class="info-value">
                            <span class="badge {{ $session->is_bounce ? 'badge-success' : 'badge-info' }}">
                                {{ $session->is_bounce ? 'نعم' : 'لا' }}
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">بوت:</span>
                        <span class="info-value">
                            <span class="badge {{ $session->is_bot ? 'badge-success' : 'badge-info' }}">
                                {{ $session->is_bot ? 'نعم' : 'لا' }}
                            </span>
                        </span>
                    </div>
                    @if($session->utm_source)
                    <div class="info-row">
                        <span class="info-label">UTM Source:</span>
                        <span class="info-value">{{ $session->utm_source }}</span>
                    </div>
                    @endif
                    @if($session->utm_medium)
                    <div class="info-row">
                        <span class="info-label">UTM Medium:</span>
                        <span class="info-value">{{ $session->utm_medium }}</span>
                    </div>
                    @endif
                    @if($session->utm_campaign)
                    <div class="info-row">
                        <span class="info-label">UTM Campaign:</span>
                        <span class="info-value">{{ $session->utm_campaign }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($paths->count() > 0)
            <h3 style="margin-top: 32px; margin-bottom: 16px; font-size: 20px; color: var(--color-1, #1f2937);">مسار الصفحات</h3>
            <div style="overflow-x: auto; direction: ltr;">
                <table class="paths-table" style="direction: ltr;">
                    <thead>
                        <tr>
                            <th style="width: 5%; text-align: left;">#</th>
                            <th style="width: 50%; text-align: left;">المسار</th>
                            <th style="width: 15%; text-align: left;">الوقت المستغرق</th>
                            <th style="width: 15%; text-align: left;">نسبة التمرير</th>
                            <th style="width: 15%; text-align: left;">الوقت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paths as $path)
                            <tr>
                                <td>{{ $path['position'] }}</td>
                                <td>
                                    <div class="path-url">{{ $path['path'] }}</div>
                                </td>
                                <td>{{ $path['time_spent_ms'] > 0 ? number_format($path['time_spent_ms'] / 1000, 2) . ' ث' : 'N/A' }}</td>
                                <td>
                                    @if($path['scroll_percent'] > 0)
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="flex: 1; height: 8px; background: var(--background-0, #f8fafc); border-radius: 4px; overflow: hidden;">
                                                <div style="height: 100%; width: {{ $path['scroll_percent'] }}%; background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%);"></div>
                                            </div>
                                            <span style="font-size: 12px; color: var(--color-2, #575f66);">{{ $path['scroll_percent'] }}%</span>
                                        </div>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $path['created_at']->format('H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection

