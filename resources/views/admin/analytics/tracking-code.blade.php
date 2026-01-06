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
    
    @if(session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
    @endif
    
    @if($errors->any())
    <div class="alert alert-danger mt-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <script>
    function copyToClipboard() {
        const textarea = document.querySelector('textarea');
        textarea.select();
        document.execCommand('copy');
        alert('تم نسخ كود التتبع إلى الحافظة!');
    }
    
    // Handle form submission
    document.getElementById('editTitleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        var form = this;
        var formData = new FormData(form);
        var submitBtn = form.querySelector('button[type="submit"]');
        var originalText = submitBtn.textContent;
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'جاري الحفظ...';
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                alert('تم تحديث عنوان الموقع بنجاح!');
                // Optionally reload the page to show updated title
                window.location.reload();
            } else {
                alert('حدث خطأ أثناء تحديث العنوان');
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            alert('حدث خطأ أثناء تحديث العنوان');
        })
        .finally(function() {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    });
    </script>
</div>
@endsection
