@extends('layouts.admin', ['page_title' => 'أنماط URL - ' . $site->title])

@section('content')
<style>
    .patterns-container {
        background: var(--background-1, #ffffff);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .patterns-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
    }
    
    .patterns-header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
    }
    
    .patterns-filters {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    
    .patterns-filters .form-control {
        min-width: 200px;
    }
    
    .pattern-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        margin-bottom: 12px;
        background: var(--background-0, #f8fafc);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 8px;
        transition: all 0.2s;
    }
    
    .pattern-item:hover {
        background: var(--background-1, #ffffff);
        border-color: var(--analytics-primary, #7b60fb);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .pattern-info {
        flex: 1;
    }
    
    .pattern-domain {
        font-weight: 600;
        color: var(--analytics-primary, #7b60fb);
        margin-bottom: 4px;
        font-size: 14px;
    }
    
    .pattern-path {
        font-family: 'Courier New', monospace;
        color: var(--color-2, #575f66);
        font-size: 13px;
        word-break: break-all;
    }
    
    .pattern-path .wildcard {
        color: var(--analytics-primary, #7b60fb);
        font-weight: 600;
    }
    
    .pattern-actions {
        display: flex;
        gap: 8px;
    }
    
    .btn-delete {
        padding: 6px 12px;
        font-size: 12px;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--color-2, #575f66);
    }
    
    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
    
    .btn-regenerate {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
    }
    
    .btn-regenerate:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
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
            <div style="display: flex; gap: 12px;">
                <a href="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns.create' : 'user.analytics.patterns.create', $site) }}" class="btn btn-success" style="padding: 10px 20px; border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-plus"></i> إضافة نمط
                </a>
                <form method="POST" action="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns.regenerate' : 'user.analytics.patterns.regenerate', $site) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-regenerate">
                        <i class="fas fa-sync-alt"></i> إعادة توليد الأنماط
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="GET" action="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns' : 'user.analytics.patterns', $site) }}" class="patterns-filters">
            <select name="domain" class="form-control">
                <option value="">جميع النطاقات</option>
                @foreach($domains as $domain)
                    <option value="{{ $domain }}" {{ request('domain') === $domain ? 'selected' : '' }}>
                        {{ $domain }}
                    </option>
                @endforeach
            </select>
            
            <input type="text" name="pattern" class="form-control" placeholder="بحث في الأنماط..." value="{{ request('pattern') }}">
            
            <button type="submit" class="btn btn-primary">بحث</button>
            
            @if(request()->has('domain') || request()->has('pattern'))
                <a href="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns' : 'user.analytics.patterns', $site) }}" class="btn btn-secondary">إعادة تعيين</a>
            @endif
        </form>

        @if($patterns->count() > 0)
            <div class="col-12 p-0">
                <div style="margin-bottom: 16px; color: var(--color-2, #575f66); font-size: 14px;">
                    عرض {{ $patterns->firstItem() }} - {{ $patterns->lastItem() }} من أصل {{ $patterns->total() }} نمط
                </div>
                
                @foreach($patterns as $pattern)
                    <div class="pattern-item">
                        <div class="pattern-info">
                            <div class="pattern-domain">{{ $pattern->domain }}</div>
                            <div class="pattern-path">
                                {!! str_replace('*', '<span class="wildcard">*</span>', htmlspecialchars($pattern->pattern)) !!}
                            </div>
                            <div style="font-size: 12px; color: var(--color-2, #575f66); margin-top: 8px;">
                                <i class="far fa-clock"></i> تم الإنشاء: {{ $pattern->generated_at ? $pattern->generated_at->format('Y-m-d H:i') : '-' }}
                            </div>
                        </div>
                        <div class="pattern-actions">
                            <a href="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns.edit' : 'user.analytics.patterns.edit', [$site, $pattern]) }}" class="btn btn-primary btn-delete" style="padding: 6px 12px; font-size: 12px;">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form method="POST" action="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns.delete' : 'user.analytics.patterns.delete', [$site, $pattern]) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا النمط؟');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-delete">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach

                <div class="mt-4">
                    {{ $patterns->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>لا توجد أنماط</h3>
                <p>لا توجد أنماط URL لهذا الموقع. يمكنك إضافة نمط يدويًا أو إعادة توليد الأنماط من بيانات الجلسات.</p>
                <div style="display: flex; gap: 12px; justify-content: center; margin-top: 24px; flex-wrap: wrap;">
                    <a href="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns.create' : 'user.analytics.patterns.create', $site) }}" class="btn btn-success" style="padding: 10px 20px; border-radius: 8px; font-weight: 600;">
                        <i class="fas fa-plus"></i> إضافة نمط يدويًا
                    </a>
                    <form method="POST" action="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns.regenerate' : 'user.analytics.patterns.regenerate', $site) }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-regenerate">
                            <i class="fas fa-sync-alt"></i> إعادة توليد الأنماط
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection

