@extends('layouts.admin')
@section('content')
@php
    // Make sure currentPredefinedTags and currentCustomSelectors are set
    if(!isset($currentPredefinedTags)) {
        $currentPredefinedTags = [];
    }
    if(!isset($currentCustomSelectors)) {
        $currentCustomSelectors = [];
    }
@endphp
@include('admin.advertisements.create')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update header title
    const headerTitle = document.querySelector('.ad-form-header h1');
    if (headerTitle) headerTitle.textContent = 'تعديل الإعلان';
    
    // Update button text
    const submitBtn = document.getElementById('submitEvaluation');
    if (submitBtn) {
        const span = submitBtn.querySelector('span');
        if (span) span.textContent = 'تحديث الإعلان';
    }
    
    // Set content value if it exists
    const contentTextarea = document.getElementById('content');
    if (contentTextarea && !contentTextarea.value) {
        const contentValue = {!! json_encode(old('content', isset($advertisement) ? $advertisement->content : '')) !!};
        if (contentValue) {
            contentTextarea.value = contentValue;
        }
    }
});
</script>
@endpush
@endsection
