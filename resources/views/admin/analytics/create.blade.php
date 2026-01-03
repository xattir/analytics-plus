@extends('layouts.admin', ['page_title' => 'إضافة موقع تحليلات'])

@section('content')
<div class="col-12 p-3">
    <h4>إضافة موقع تحليلات جديد</h4>
    
    <form method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.analytics.store') : route('user.analytics.store') }}" class="col-12 col-lg-6">
        @csrf
        
        <div class="mb-3">
            <label for="domain" class="form-label">النطاق</label>
            <input type="text" class="form-control" id="domain" name="domain" value="{{ old('domain') }}" required placeholder="example.com">
            <small class="form-text text-muted">أدخل اسم النطاق لموقع التحليلات هذا</small>
        </div>
        
        <button type="submit" class="btn btn-primary">إنشاء الموقع</button>
        <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.index') : route('user.analytics.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
