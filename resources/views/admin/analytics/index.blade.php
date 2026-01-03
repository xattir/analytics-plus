@extends('layouts.admin', ['page_title' => 'مواقع التحليلات'])

@section('content')
<div class="col-12 p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>مواقع التحليلات @if(isset($isSuperAdmin) && $isSuperAdmin) <small class="text-muted">(جميع المواقع - عرض المشرف)</small> @endif</h4>
        <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.create') : route('user.analytics.create') }}" class="btn btn-primary">إضافة موقع جديد</a>
    </div>
    
    @if(isset($pendingInvitations) && $pendingInvitations->count() > 0)
    <div class="alert alert-info mb-3">
        <h5>الدعوات المعلقة</h5>
        <ul class="mb-0">
            @foreach($pendingInvitations as $invitation)
            <li>
                تمت دعوتك لإدارة <strong>{{ $invitation->site->domain }}</strong>
                <a href="{{ route('user.analytics.accept-invitation', $invitation->token) }}" class="btn btn-sm btn-success ml-2">قبول</a>
                <a href="{{ route('user.analytics.reject-invitation', $invitation->token) }}" class="btn btn-sm btn-danger ml-2">رفض</a>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <div class="col-12 row p-4" style="padding: 30px 0px;position: relative;background: #fff;overflow-x: auto;">
        <table class="table table-striped table-bordered col-12">
            <thead>
                <tr>
                    <th>المعرف</th>
                    <th>النطاق</th>
                    @if(isset($isSuperAdmin) && $isSuperAdmin)
                    <th>المالك</th>
                    @endif
                    <th>مفتاح الموقع</th>
                    <th>الجلسات</th>
                    <th>تاريخ الإنشاء</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sites as $site)
                <tr>
                    <td>{{ $site->id }}</td>
                    <td>{{ $site->domain }}</td>
                    @if(isset($isSuperAdmin) && $isSuperAdmin)
                    <td>
                        @if($site->owner)
                            {{ $site->owner->name }} ({{ $site->owner->email }})
                        @else
                            <span class="text-muted">لا يوجد مالك</span>
                        @endif
                    </td>
                    @endif
                    <td><code>{{ $site->site_key }}</code></td>
                    <td>{{ $site->sessions_count }}</td>
                    <td>{{ $site->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        @if(isset($isSuperAdmin) && $isSuperAdmin)
                            <a href="{{ route('admin.analytics.show', ['site' => $site->site_key]) }}" class="btn btn-sm btn-info">عرض لوحة التحكم</a>
                            <a href="{{ route('admin.analytics.tracking-code', ['site' => $site->site_key]) }}" class="btn btn-sm btn-success">الحصول على الكود</a>
                            <a href="{{ route('admin.analytics.members', ['site' => $site->site_key]) }}" class="btn btn-sm btn-secondary">إدارة الفريق</a>
                        @else
                            <a href="{{ route('user.analytics.show', ['site' => $site->site_key]) }}" class="btn btn-sm btn-info">عرض لوحة التحكم</a>
                            <a href="{{ route('user.analytics.tracking-code', ['site' => $site->site_key]) }}" class="btn btn-sm btn-success">الحصول على الكود</a>
                            @if($site->user_id == auth()->id())
                                <a href="{{ route('user.analytics.members', ['site' => $site->site_key]) }}" class="btn btn-sm btn-secondary">إدارة الفريق</a>
                            @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ isset($isSuperAdmin) && $isSuperAdmin ? '7' : '6' }}" class="text-center">لا توجد مواقع تحليلات. <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.create') : route('user.analytics.create') }}">إنشاء واحد</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="d-flex justify-content-center">
        {{ $sites->links() }}
    </div>
</div>
@endsection
