@extends('layouts.admin', ['page_title' => 'Create Analytics Site'])

@section('content')
<div class="col-12 p-3">
    <h4>Create New Analytics Site</h4>
    
    <form method="POST" action="{{ route('user.analytics.store') }}" class="col-12 col-lg-6">
        @csrf
        
        <div class="mb-3">
            <label for="domain" class="form-label">Domain</label>
            <input type="text" class="form-control" id="domain" name="domain" value="{{ old('domain') }}" required placeholder="example.com">
            <small class="form-text text-muted">Enter the domain name for this analytics site</small>
        </div>
        
        <button type="submit" class="btn btn-primary">Create Site</button>
        <a href="{{ route('user.analytics.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

