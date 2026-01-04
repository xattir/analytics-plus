@extends('layouts.admin')
@section('content')
<div class="col-12 p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>إدارة الفريق: {{ $site->domain }}</h4>
        <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.show', ['site' => $site->site_key]) : route('user.analytics.show', ['site' => $site->site_key]) }}" class="btn btn-secondary">العودة إلى لوحة التحكم</a>
    </div>
    
    <!-- Current Members -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>الأعضاء الحاليون</h5>
        </div>
        <div class="card-body">
            <div class="mb-2">
                <strong>المالك:</strong> {{ $site->owner->name }} ({{ $site->owner->email }})
            </div>
            
            @if($members->count() > 0)
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>تاريخ الإضافة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                    <tr>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->email }}</td>
                        <td>{{ $member->pivot->created_at->format('Y-m-d') }}</td>
                        <td>
                            <form method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.analytics.remove-member', ['site' => $site->site_key]) : route('user.analytics.remove-member', ['site' => $site->site_key]) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $member->id }}">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">إزالة</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-muted">لا يوجد أعضاء إضافيون بعد.</p>
            @endif
        </div>
    </div>
    
    <!-- Send Invitation -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>دعوة عضو جديد</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.analytics.invite', ['site' => $site->site_key]) : route('user.analytics.invite', ['site' => $site->site_key]) }}">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <input type="email" name="email" class="form-control" placeholder="أدخل عنوان البريد الإلكتروني" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">إرسال الدعوة</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Pending Invitations -->
    @if($invitations->count() > 0)
    <div class="card">
        <div class="card-header">
            <h5>الدعوات المعلقة</h5>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>البريد الإلكتروني</th>
                        <th>تاريخ الإرسال</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invitations as $invitation)
                    <tr>
                        <td>{{ $invitation->email }}</td>
                        <td>{{ $invitation->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $invitation->expires_at ? $invitation->expires_at->format('Y-m-d H:i') : 'أبداً' }}</td>
                        <td>
                            <form method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.analytics.cancel-invitation', $invitation->id) : route('user.analytics.cancel-invitation', $invitation->id) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">إلغاء</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
