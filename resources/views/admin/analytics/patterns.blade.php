@extends('layouts.admin', ['page_title' => 'أنماط URL - ' . $site->title])

@section('content')
<style>
    .patterns-container {
        background: var(--background-1, #ffffff);
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .patterns-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--border-color, #e5e7eb);
    }
    
    .patterns-header h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: var(--color-1, #1f2937);
    }
    
    .patterns-filters {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
        background: var(--background-0, #f8fafc);
        padding: 16px;
        border-radius: 12px;
    }
    
    .patterns-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--background-1, #ffffff);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .patterns-table thead {
        background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%);
        color: white;
    }
    
    .patterns-table thead th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
    }
    
    .patterns-table tbody tr {
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        transition: all 0.2s;
    }
    
    .patterns-table tbody tr:hover {
        background: var(--background-0, #f8fafc);
    }
    
    .patterns-table tbody td {
        padding: 16px;
        font-size: 14px;
        color: var(--color-2, #575f66);
        text-align: left;
    }
    
    .pattern-domain {
        font-weight: 600;
        color: var(--analytics-primary, #7b60fb);
    }
    
    .pattern-path {
        font-family: 'Courier New', monospace;
        color: var(--color-2, #575f66);
        word-break: break-all;
    }
    
    .pattern-path .wildcard {
        color: var(--analytics-primary, #7b60fb);
        font-weight: 700;
        background: rgba(123, 96, 251, 0.1);
        padding: 2px 6px;
        border-radius: 4px;
    }
    
    .pattern-actions {
        display: flex;
        gap: 8px;
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
    }
    
    .btn-modern-primary {
        background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%);
        color: white;
    }
    
    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(123, 96, 251, 0.3);
        color: white;
    }
    
    .btn-modern-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .btn-modern-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        color: white;
    }
    
    .btn-modern-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .btn-modern-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        color: white;
    }
    
    .btn-modern-secondary {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
    }
    
    .btn-modern-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: var(--color-2, #575f66);
    }
    
    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.3;
        color: var(--analytics-primary, #7b60fb);
    }
    
    .pattern-date {
        font-size: 12px;
        color: var(--color-2, #9ca3af);
    }
</style>

<div class="col-12 p-3">
    <div class="patterns-container">
        <div class="patterns-header">
            <div>
                <h2>أنماط URL</h2>
                <p style="margin: 8px 0 0 0; color: var(--color-2, #575f66); font-size: 14px;">
                    {{ $site->title }} ({{ $site->domain }})
                </p>
            </div>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.advertisements' : 'user.analytics.advertisements', $site) }}" class="btn-modern btn-modern-secondary">
                    <i class="fal fa-ad"></i> الإعلانات
                </a>
                <a href="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns.create' : 'user.analytics.patterns.create', $site) }}" class="btn-modern btn-modern-success">
                    <i class="fas fa-plus"></i> إضافة نمط
                </a>
                <form method="POST" action="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns.regenerate' : 'user.analytics.patterns.regenerate', $site) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-modern btn-modern-primary">
                        <i class="fas fa-sync-alt"></i> إعادة توليد
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; margin-bottom: 24px;">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; margin-bottom: 24px;">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="GET" action="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns' : 'user.analytics.patterns', $site) }}" class="patterns-filters">
            <select name="domain" class="form-control" style="min-width: 200px;">
                <option value="">جميع النطاقات</option>
                @foreach($domains as $domain)
                    <option value="{{ $domain }}" {{ request('domain') === $domain ? 'selected' : '' }}>
                        {{ $domain }}
                    </option>
                @endforeach
            </select>
            
            <input type="text" name="pattern" class="form-control" placeholder="بحث في الأنماط..." value="{{ request('pattern') }}" style="min-width: 250px;">
            
            <button type="submit" class="btn btn-primary" style="border-radius: 8px; padding: 10px 20px;">بحث</button>
            
            @if(request()->has('domain') || request()->has('pattern'))
                <a href="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns' : 'user.analytics.patterns', $site) }}" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 20px;">إعادة تعيين</a>
            @endif
        </form>

        @if($patterns->count() > 0)
            <div style="margin-bottom: 16px; color: var(--color-2, #575f66); font-size: 14px; padding: 12px; background: var(--background-0, #f8fafc); border-radius: 8px;">
                عرض {{ $patterns->firstItem() }} - {{ $patterns->lastItem() }} من أصل {{ $patterns->total() }} نمط
            </div>
            
            <div style="overflow-x: auto; direction: ltr;">
                <table class="patterns-table" style="direction: ltr;">
                    <thead>
                        <tr>
                            <th style="width: 25%; text-align: left;">النطاق</th>
                            <th style="width: 40%; text-align: left;">النمط</th>
                            <th style="width: 20%; text-align: left;">تاريخ الإنشاء</th>
                            <th style="width: 15%; text-align: left;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patterns as $pattern)
                            <tr>
                                <td>
                                    <div class="pattern-domain">{{ $pattern->domain }}</div>
                                </td>
                                <td>
                                    <div class="pattern-path">
                                        {!! str_replace('*', '<span class="wildcard">*</span>', htmlspecialchars($pattern->pattern)) !!}
                                    </div>
                                </td>
                                <td>
                                    <div class="pattern-date">
                                        <i class="far fa-clock"></i> {{ $pattern->generated_at ? $pattern->generated_at->format('Y-m-d H:i') : '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="pattern-actions">
                                        <a href="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns.edit' : 'user.analytics.patterns.edit', [$site, $pattern]) }}" class="btn-modern btn-modern-primary">
                                            <i class="fas fa-edit"></i> تعديل
                                        </a>
                                        <form method="POST" action="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns.delete' : 'user.analytics.patterns.delete', [$site, $pattern]) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا النمط؟');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-modern btn-modern-danger">
                                                <i class="fas fa-trash"></i> حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4" style="display: flex; justify-content: center;">
                {{ $patterns->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3 style="margin-bottom: 12px; color: var(--color-1, #1f2937);">لا توجد أنماط</h3>
                <p style="margin-bottom: 24px; color: var(--color-2, #575f66);">لا توجد أنماط URL لهذا الموقع. يمكنك إضافة نمط يدويًا أو إعادة توليد الأنماط من بيانات الجلسات.</p>
                <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns.create' : 'user.analytics.patterns.create', $site) }}" class="btn-modern btn-modern-success">
                        <i class="fas fa-plus"></i> إضافة نمط يدويًا
                    </a>
                    <form method="POST" action="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns.regenerate' : 'user.analytics.patterns.regenerate', $site) }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-modern btn-modern-primary">
                            <i class="fas fa-sync-alt"></i> إعادة توليد الأنماط
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
