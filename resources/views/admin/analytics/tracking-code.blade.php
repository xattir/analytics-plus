@extends('layouts.admin', ['page_title' => 'كود التتبع'])

@section('content')
<div class="col-12 p-3">
    <h4>كود التتبع لـ {{ $site->domain }}</h4>
    
    <div class="alert alert-info">
        <strong>مفتاح الموقع:</strong> <code>{{ $site->site_key }}</code>
    </div>
    
    <div class="mb-3">
        <label class="form-label">انسخ والصق هذا الكود في HTML لموقعك، قبل إغلاق وسم &lt;/head&gt;:</label>
        <textarea class="form-control" rows="6" readonly onclick="this.select()">{{ $trackingCode }}</textarea>
    </div>
    
    <div class="mb-3">
        <button class="btn btn-primary" onclick="copyToClipboard()">نسخ إلى الحافظة</button>
        <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.show', ['site' => $site->site_key]) : route('user.analytics.show', ['site' => $site->site_key]) }}" class="btn btn-secondary">عرض لوحة التحكم</a>
    </div>
    
    <hr class="my-4">
    
    <h5 class="mb-3">تعديل عنوان الموقع</h5>
    <form id="editTitleForm" method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.analytics.update-title', ['site' => $site->id]) : route('user.analytics.update-title', ['site' => $site->id]) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="siteTitleInput">عنوان الموقع</label>
            <input type="text" class="form-control" id="siteTitleInput" name="title" value="{{ $site->title ?? '' }}" placeholder="{{ $site->domain }}">
            <small class="form-text text-muted">اتركه فارغاً للعودة إلى اسم النطاق الافتراضي</small>
        </div>
        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
    </form>
    
    <script>
    function copyToClipboard() {
        const textarea = document.querySelector('textarea');
        textarea.select();
        document.execCommand('copy');
        alert('تم نسخ كود التتبع إلى الحافظة!');
    }
    </script>
</div>
@endsection
