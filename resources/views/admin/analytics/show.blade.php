@extends('layouts.admin', ['page_title' => 'Analytics Dashboard - ' . $site->domain])

@section('content')
<div class="col-12 p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Analytics Dashboard: {{ $site->domain }}</h4>
        <div>
            <a href="{{ route('user.analytics.tracking-code', $site->id) }}" class="btn btn-sm btn-success">Get Tracking Code</a>
            @if($site->user_id == auth()->id())
                <a href="{{ route('user.analytics.members', $site->id) }}" class="btn btn-sm btn-primary">Manage Team</a>
            @endif
            <a href="{{ route('user.analytics.index') }}" class="btn btn-sm btn-secondary">Back to Sites</a>
        </div>
    </div>
    
    <!-- Date Range Filter -->
    <form method="GET" action="{{ route('user.analytics.show', $site->id) }}" class="row mb-3">
        <div class="col-md-4">
            <label>Date From</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label>Date To</label>
            <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-primary d-block">Filter</button>
        </div>
    </form>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Sessions</h5>
                    <h2>{{ number_format($stats['total_sessions']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Unique Visitors</h5>
                    <h2>{{ number_format($stats['unique_visitors']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pageviews</h5>
                    <h2>{{ number_format($stats['total_pageviews']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bounce Rate</h5>
                    <h2>{{ number_format($stats['bounce_rate'], 2) }}%</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Avg. Duration</h5>
                    <h2>{{ gmdate('H:i:s', $stats['avg_duration'] / 1000) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pages/Session</h5>
                    <h2>{{ number_format($stats['avg_pages_per_session'], 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">New Visitors</h5>
                    <h2>{{ number_format($stats['new_visitors']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Returning Visitors</h5>
                    <h2>{{ number_format($stats['returning_visitors']) }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Pages -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Top Pages</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Path</th>
                                <th>Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topPages as $page)
                            <tr>
                                <td>{{ $page->path }}</td>
                                <td>{{ number_format($page->views) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Top Browsers</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Browser</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topBrowsers as $browser)
                            <tr>
                                <td>{{ $browser->browser }}</td>
                                <td>{{ number_format($browser->count) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Devices and Countries -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Top Devices</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Device Type</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topDevices as $device)
                            <tr>
                                <td>{{ ucfirst($device->device_type) }}</td>
                                <td>{{ number_format($device->count) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Top Countries</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Country</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCountries as $country)
                            <tr>
                                <td>{{ $country->country }}</td>
                                <td>{{ number_format($country->count) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

