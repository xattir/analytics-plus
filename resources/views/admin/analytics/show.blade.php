@extends('layouts.admin', ['page_title' => 'Analytics Dashboard - ' . $site->domain])

@section('content')
<div class="col-12 p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Analytics Dashboard: {{ $site->domain }}</h4>
        <div>
            @if(isset($isAdminRoute) && $isAdminRoute)
                <a href="{{ route('admin.analytics.tracking-code', $site->site_key) }}" class="btn btn-sm btn-success">Get Tracking Code</a>
                @if(isset($isSuperAdmin) && $isSuperAdmin)
                    <a href="{{ route('admin.analytics.members', $site->site_key) }}" class="btn btn-sm btn-primary">Manage Team</a>
                @elseif($site->user_id == auth()->id())
                    <a href="{{ route('admin.analytics.members', $site->site_key) }}" class="btn btn-sm btn-primary">Manage Team</a>
                @endif
                <a href="{{ route('admin.analytics.index') }}" class="btn btn-sm btn-secondary">Back to Sites</a>
            @else
                <a href="{{ route('user.analytics.tracking-code', $site->site_key) }}" class="btn btn-sm btn-success">Get Tracking Code</a>
                @if($site->user_id == auth()->id())
                    <a href="{{ route('user.analytics.members', $site->site_key) }}" class="btn btn-sm btn-primary">Manage Team</a>
                @endif
                <a href="{{ route('user.analytics.index') }}" class="btn btn-sm btn-secondary">Back to Sites</a>
            @endif
        </div>
    </div>
    
    <!-- Date Range Filter -->
    <form method="GET" action="{{ (isset($isAdminRoute) && $isAdminRoute) ? route('admin.analytics.show', $site->site_key) : route('user.analytics.show', $site->site_key) }}" class="row mb-3">
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
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Total Sessions</h6>
                    <h2 class="mb-0">{{ number_format($stats['total_sessions']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Unique Visitors</h6>
                    <h2 class="mb-0">{{ number_format($stats['unique_visitors']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Pageviews</h6>
                    <h2 class="mb-0">{{ number_format($stats['total_pageviews']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Bounce Rate</h6>
                    <h2 class="mb-0">{{ number_format($stats['bounce_rate'], 2) }}%</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Avg. Duration</h6>
                    <h2 class="mb-0">{{ gmdate('H:i:s', ($stats['avg_duration'] ?? 0) / 1000) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Pages/Session</h6>
                    <h2 class="mb-0">{{ number_format($stats['avg_pages_per_session'] ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">New Visitors</h6>
                    <h2 class="mb-0">{{ number_format($stats['new_visitors']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Returning Visitors</h6>
                    <h2 class="mb-0">{{ number_format($stats['returning_visitors']) }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Time Series Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Sessions & Pageviews Over Time</h5>
                </div>
                <div class="card-body">
                    <canvas id="timeSeriesChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Top Browsers</h5>
                </div>
                <div class="card-body">
                    <canvas id="browsersChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Top Devices</h5>
                </div>
                <div class="card-body">
                    <canvas id="devicesChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Top Operating Systems</h5>
                </div>
                <div class="card-body">
                    <canvas id="osChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Pages and Countries -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Top Pages</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Path</th>
                                    <th class="text-end">Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPages as $page)
                                <tr>
                                    <td><code class="small">{{ Str::limit($page->path, 50) }}</code></td>
                                    <td class="text-end"><strong>{{ number_format($page->views) }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Top Countries</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th class="text-end">Visitors</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCountries as $country)
                                <tr>
                                    <td>
                                        @if($country->country)
                                            <span class="badge bg-primary">{{ $country->country }}</span>
                                        @else
                                            <span class="text-muted">Unknown</span>
                                        @endif
                                    </td>
                                    <td class="text-end"><strong>{{ number_format($country->count) }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sessions List -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Sessions</h5>
                    <span class="badge bg-secondary">{{ $sessions->total() }} total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Session ID</th>
                                    <th>Entry Path</th>
                                    <th>Pages</th>
                                    <th>Duration</th>
                                    <th>Device</th>
                                    <th>Browser</th>
                                    <th>Country</th>
                                    <th>First Seen</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sessions as $session)
                                <tr>
                                    <td><code class="small">{{ Str::limit($session->session_id, 20) }}</code></td>
                                    <td><code class="small">{{ Str::limit($session->entry_path, 30) }}</code></td>
                                    <td>
                                        <span class="badge bg-info">{{ $session->pages_count }}</span>
                                        <small class="text-muted">({{ $session->paths_count }} paths)</small>
                                    </td>
                                    <td>{{ gmdate('H:i:s', ($session->duration_ms ?? 0) / 1000) }}</td>
                                    <td>
                                        @if($session->device_type)
                                            <span class="badge bg-secondary">{{ ucfirst($session->device_type) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $session->browser ?? '-' }}</td>
                                    <td>
                                        @if($session->country)
                                            <span class="badge bg-primary">{{ $session->country }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $session->first_seen->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" onclick="viewSessionDetails('{{ $session->session_id }}')">View</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">No sessions found for this period</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($sessions->hasPages())
                    <div class="card-footer bg-white">
                        {{ $sessions->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="/js/chartjs.min.js"></script>
<script>
// Time Series Chart
const timeSeriesCtx = document.getElementById('timeSeriesChart');
if (timeSeriesCtx) {
    new Chart(timeSeriesCtx, {
        type: 'line',
        data: {
            labels: [
                @foreach($timeSeries as $data)
                "{{ isset($data->date) ? \Carbon\Carbon::parse($data->date)->format('M d') : 'Week ' . $data->week }}",
                @endforeach
            ],
            datasets: [{
                label: 'Sessions',
                data: [
                    @foreach($timeSeries as $data)
                    {{ $data->sessions ?? 0 }},
                    @endforeach
                ],
                backgroundColor: 'rgba(123, 96, 251, 0.1)',
                borderColor: '#7b60fb',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }, {
                label: 'Pageviews',
                data: [
                    @foreach($timeSeries as $data)
                    {{ $data->pageviews ?? 0 }},
                    @endforeach
                ],
                backgroundColor: 'rgba(0, 184, 255, 0.1)',
                borderColor: '#00b8ff',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Browsers Chart
const browsersCtx = document.getElementById('browsersChart');
if (browsersCtx) {
    new Chart(browsersCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($topBrowsers as $browser)
                "{{ $browser->browser }}",
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($topBrowsers as $browser)
                    {{ $browser->count }},
                    @endforeach
                ],
                backgroundColor: [
                    '#7b60fb', '#0fb8ff', '#7cc5ff', '#9ed2fb', '#5aceff',
                    '#8eddff', '#c5edff', '#e8f6ff', '#f0f9ff', '#f8fcff'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Devices Chart
const devicesCtx = document.getElementById('devicesChart');
if (devicesCtx) {
    new Chart(devicesCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($topDevices as $device)
                "{{ ucfirst($device->device_type) }}",
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($topDevices as $device)
                    {{ $device->count }},
                    @endforeach
                ],
                backgroundColor: ['#7b60fb', '#0fb8ff', '#7cc5ff']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// OS Chart
const osCtx = document.getElementById('osChart');
if (osCtx) {
    new Chart(osCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($topOs as $os)
                "{{ $os->os }}",
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($topOs as $os)
                    {{ $os->count }},
                    @endforeach
                ],
                backgroundColor: [
                    '#7b60fb', '#0fb8ff', '#7cc5ff', '#9ed2fb', '#5aceff',
                    '#8eddff', '#c5edff', '#e8f6ff', '#f0f9ff', '#f8fcff'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function viewSessionDetails(sessionId) {
    // You can implement a modal or redirect to session details page
    alert('Session ID: ' + sessionId);
}
</script>
@endsection
