@extends('layouts.admin', ['page_title' => 'Tracking Code'])

@section('content')
<div class="col-12 p-3">
    <h4>Tracking Code for {{ $site->domain }}</h4>
    
    <div class="alert alert-info">
        <strong>Site Key:</strong> <code>{{ $site->site_key }}</code>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Copy and paste this code into your website's HTML, just before the closing &lt;/head&gt; tag:</label>
        <textarea class="form-control" rows="10" readonly onclick="this.select()">{{ $trackingCode }}</textarea>
    </div>
    
    <div class="mb-3">
        <button class="btn btn-primary" onclick="copyToClipboard()">Copy to Clipboard</button>
        <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.show', $site->site_key) : route('user.analytics.show', $site->site_key) }}" class="btn btn-secondary">View Dashboard</a>
    </div>
    
    <script>
    function copyToClipboard() {
        const textarea = document.querySelector('textarea');
        textarea.select();
        document.execCommand('copy');
        alert('Tracking code copied to clipboard!');
    }
    </script>
</div>
@endsection

