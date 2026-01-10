@extends('layouts.admin', ['page_title' => 'جميع المواقع'])

@section('content')
<style>
    .websites-table {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .websites-table table {
        margin: 0;
    }
    
    .websites-table thead {
        background: #f8f9fa;
    }
    
    .websites-table th {
        padding: 12px;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    
    .websites-table td {
        padding: 12px;
        vertical-align: middle;
    }
    
    .websites-table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .filter-section {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .site-favicon {
        width: 20px;
        height: 20px;
        margin-left: 8px;
        vertical-align: middle;
    }
    
    .owner-badge {
        display: inline-block;
        padding: 4px 8px;
        background: #e3f2fd;
        color: #1976d2;
        border-radius: 4px;
        font-size: 12px;
    }
</style>

<div class="col-12 p-3">
    <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
        <h4>جميع المواقع</h4>
        <a href="{{ route('admin.analytics.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-right"></i> مواقعي
        </a>
    </div>
    
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.analytics.websites') }}" class="row">
            <div class="col-12 col-md-3 mb-2">
                <label>المالك</label>
                <select name="user_id" class="form-control">
                    <option value="">الكل</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @if(request('user_id') == $user->id) selected @endif>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3 mb-2">
                <label>النطاق</label>
                <input type="text" name="domain" class="form-control" value="{{ request('domain') }}" placeholder="البحث في النطاق">
            </div>
            <div class="col-12 col-md-3 mb-2">
                <label>العنوان</label>
                <input type="text" name="title" class="form-control" value="{{ request('title') }}" placeholder="البحث في العنوان">
            </div>
            <div class="col-12 col-md-3 mb-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> بحث
                </button>
            </div>
        </form>
    </div>
    
    <div class="websites-table">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>الموقع</th>
                    <th>المالك</th>
                    <th>الجلسات</th>
                    <th>تاريخ الإنشاء</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sites as $site)
                <tr>
                    <td>
                        <img src="https://icons.duckduckgo.com/ip3/{{ $site->domain }}.ico" 
                             alt="" 
                             class="site-favicon"
                             onerror="this.style.display='none'">
                        <strong>{{ $site->title ?? $site->domain }}</strong>
                        @if($site->title && $site->title !== $site->domain)
                            <br><small class="text-muted">{{ $site->domain }}</small>
                        @endif
                    </td>
                    <td>
                        @if($site->owner)
                            <span class="owner-badge">
                                <i class="fas fa-user"></i> {{ $site->owner->name }}
                            </span>
                            <br><small class="text-muted">{{ $site->owner->email }}</small>
                        @else
                            <span class="text-muted">غير محدد</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-info">{{ number_format($site->sessions_count) }}</span>
                    </td>
                    <td>
                        {{ $site->created_at->format('Y-m-d H:i') }}
                        <br><small class="text-muted">{{ $site->created_at->diffForHumans() }}</small>
                    </td>
                    <td>
                        <a href="{{ route('admin.analytics.show', $site->site_key) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> عرض
                        </a>
                        <a href="{{ route('admin.analytics.tracking-code', $site->site_key) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-code"></i> كود
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد مواقع</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($sites->hasPages())
    <div class="col-12 pt-3">
        {{ $sites->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection

