@extends('layouts.admin', ['page_title' => 'مواقع التحليلات'])

@section('content')
<style>
    .analytics-sites-grid {
        display: flex;
        flex-direction: row-reverse; /* Visual RTL: cards appear right-to-left */
        flex-wrap: wrap;
        gap: 24px;
        margin-top: 24px;
        min-height: 100%;
        direction: ltr; /* LTR for drag logic - keeps getBoundingClientRect() measurements correct */
    }
    
    .analytics-sites-grid.sortable-drag {
        gap: 24px;
    }
    
    .site-card {
        flex: 0 0 calc(25% - 18px); /* 4 columns, accounting for gap */
        min-width: 280px; /* Minimum card width */
        max-width: 100%;
        background: var(--background-1, #ffffff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 12px;
        padding: 20px;
        transition: box-shadow 0.2s, border-color 0.2s;
        cursor: pointer; /* Pointer cursor to indicate clickable */
        position: relative;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        will-change: transform;
    }
    
    .site-card .site-card-drag-handle {
        cursor: grab; /* Grab cursor only for drag handle */
    }
    
    .site-card .site-card-drag-handle:active {
        cursor: grabbing;
    }
    
    @media (max-width: 1200px) {
        .site-card {
            flex: 0 0 calc(33.333% - 16px); /* 3 columns on smaller desktop */
        }
    }
    
    @media (max-width: 992px) {
        .site-card {
            flex: 0 0 calc(50% - 12px); /* 2 columns on medium screens */
        }
    }
    
    @media (max-width: 576px) {
        .site-card {
            flex: 0 0 100%; /* 1 column on small screens */
        }
    }
    
    .site-card:hover:not(.sortable-ghost):not(.sortable-chosen) {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: var(--analytics-primary, #7b60fb);
    }
    
    .site-card.sortable-ghost {
        opacity: 0.4;
        cursor: grabbing !important;
        background: var(--background-1, #ffffff);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2) !important;
        z-index: 1000;
        border: 2px dashed var(--analytics-primary, #7b60fb);
        pointer-events: none;
    }
    
    .site-card.sortable-chosen {
        cursor: grabbing !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25) !important;
        z-index: 999;
        opacity: 0.95;
    }
    
    .site-card.sortable-drag {
        opacity: 0;
    }
    
    .sortable-fallback {
        opacity: 0.8 !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3) !important;
        transform: rotate(2deg);
        pointer-events: none;
    }
    
    .site-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
        gap: 12px;
    }
    
    .site-card-title-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }
    
    .site-card-favicon {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        flex-shrink: 0;
        object-fit: contain;
    }
    
    .site-card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--color-2, #1f2937);
        margin: 0;
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        line-height: 1.3;
    }
    
    .site-card-domain {
        display: block;
        font-size: 11px;
        color: var(--analytics-text-muted, #6b7280);
        margin-top: 2px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .site-card-edit-title {
        background: none;
        border: none;
        color: var(--analytics-text-muted, #6b7280);
        cursor: pointer;
        padding: 4px;
        font-size: 14px;
        opacity: 0.6;
        transition: opacity 0.2s;
        flex-shrink: 0;
    }
    
    .site-card-edit-title:hover {
        opacity: 1;
        color: var(--analytics-primary, #7b60fb);
    }
    
    .site-title-edit-input {
        width: 100%;
        padding: 6px 8px;
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        color: var(--color-2, #1f2937);
        background: var(--background-1, #ffffff);
    }
    
    .site-title-edit-input:focus {
        outline: none;
        border-color: var(--analytics-primary, #7b60fb);
        box-shadow: 0 0 0 3px rgba(123, 96, 251, 0.1);
    }
    
    .site-online-indicator {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 12px;
        color: var(--analytics-text-muted, #6b7280);
        flex-shrink: 0;
        position: relative;
        width: 20px;
        height: 20px;
    }
    
    .site-online-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #10b981;
        flex-shrink: 0;
        position: relative;
        z-index: 2;
        animation: dot-pulse 1.5s ease-in-out infinite;
    }
    
    .site-online-indicator::before {
        content: '';
        position: absolute;
        width: 18px;
        height: 18px;
        border: 2px solid #10b981;
        border-top-color: transparent;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-rotate 1s linear infinite;
        z-index: 1;
    }
    
    @keyframes spinner-rotate {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    
    @keyframes dot-pulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.7;
            transform: scale(0.9);
        }
    }
    
    .site-card-drag-handle {
        cursor: grab;
        color: var(--analytics-text-muted, #6b7280);
        font-size: 20px;
        padding: 8px;
        margin-left: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        min-height: 32px;
        user-select: none;
        -webkit-user-select: none;
    }
    
    .site-card-drag-handle:hover {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 6px;
    }
    
    .site-card-drag-handle:active {
        cursor: grabbing;
    }
    
    
    .site-card-stats {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
    }
    
    .site-card-stat {
        display: flex;
        flex-direction: column;
    }
    
    .site-card-stat-label {
        font-size: 12px;
        color: var(--analytics-text-muted, #6b7280);
        margin-bottom: 4px;
    }
    
    .site-card-stat-value {
        font-size: 20px;
        font-weight: 600;
        color: var(--color-2, #1f2937);
    }
    
    .site-card-chart {
        height: 80px;
        margin-top: 12px;
    }
    
    .site-card-actions {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid var(--border-color, #e5e7eb);
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .site-card-action-btn {
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid var(--border-color, #e5e7eb);
        background: var(--background-1, #ffffff);
        color: var(--color-2, #1f2937);
    }
    
    .site-card-action-btn:hover {
        background: var(--analytics-primary, #7b60fb);
        color: white;
        border-color: var(--analytics-primary, #7b60fb);
    }
    
    .empty-sites {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 80px 20px;
        color: var(--analytics-text-muted, #6b7280);
        min-height: 400px;
    }
    
    .empty-sites-icon {
        width: 100px;
        height: 100px;
        margin-bottom: 24px;
        opacity: 0.4;
        animation: float 3s ease-in-out infinite;
        filter: grayscale(100%) opacity(0.5);
        transition: all 0.3s ease;
    }
    
    .empty-sites-icon svg {
        width: 100%;
        height: 100%;
    }
    
    .empty-sites:hover .empty-sites-icon {
        opacity: 0.6;
        filter: grayscale(50%) opacity(0.7);
    }
    
    .empty-sites-text {
        font-size: 16px;
        font-weight: 500;
        color: var(--analytics-text-muted, #6b7280);
        margin: 0;
        text-align: center;
    }
    
    .empty-sites-text a {
        color: var(--analytics-primary, #7b60fb);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s ease;
        border-bottom: 2px solid transparent;
    }
    
    .empty-sites-text a:hover {
        color: #667eea;
        border-bottom-color: #667eea;
    }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-8px);
        }
    }
</style>

<div class="col-12 p-3">
    
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
    
    @if($sites->count() > 0)
    <div class="analytics-sites-grid" id="sitesGrid" >
        @foreach($sites as $site)
            @php
                $routeName = isset($isSuperAdmin) && $isSuperAdmin 
                    ? 'admin.analytics.show' 
                    : 'user.analytics.show';
                $activeUsers = $site->active_users ?? 0;
                $chartData = $site->active_users_chart_data ?? [];
            @endphp
            <div class="site-card" data-site-id="{{ $site->id }}" data-site-url="{{ route($routeName, ['site' => $site->site_key]) }}">
                <div class="site-card-header">
                    <div class="site-card-title-wrapper">
                        <img src="https://icons.duckduckgo.com/ip3/{{ $site->domain }}.ico" 
                             alt="" 
                             class="site-card-favicon"
                             onerror="this.style.display='none'">
                        <h3 class="site-card-title">{{ $site->title ?? $site->domain }}</h3>
                        @if($site->title && $site->title !== $site->domain)
                        <small class="site-card-domain" style="display: block; font-size: 11px; color: var(--analytics-text-muted, #6b7280); margin-top: 2px;">{{ $site->domain }}</small>
                        @endif
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        @if(isset($site->has_traffic_last_5min) && $site->has_traffic_last_5min)
                        <span class="site-online-indicator">
                            <span class="site-online-dot"></span>
                        </span>
                        @endif
                        <span class="site-card-drag-handle">☰</span>
                    </div>
                </div>
                
                <div class="site-card-stats">
                    <div class="site-card-stat">
                        <span class="site-card-stat-label">المستخدمون النشطون</span>
                        <span class="site-card-stat-value">{{ number_format($activeUsers) }}</span>
                    </div>
                    <div class="site-card-stat">
                        <span class="site-card-stat-label">المستخدمون اليوم</span>
                        <span class="site-card-stat-value">{{ number_format($site->today_users_count ?? 0) }}</span>
                    </div>
                </div>
                
                @php
                    $activeUsersChartData = $site->active_users_chart_data ?? [];
                @endphp
                @if(count($activeUsersChartData) > 0)
                <div class="site-card-chart">
                    <canvas id="chart-{{ $site->id }}"></canvas>
                </div>
                @endif
                
                <div class="site-card-actions">
                    <a href="{{ route($routeName, ['site' => $site->site_key]) }}" class="site-card-action-btn">عرض لوحة التحكم</a>
                    @php
                        $trackingCodeRoute = isset($isSuperAdmin) && $isSuperAdmin 
                            ? 'admin.analytics.tracking-code' 
                            : 'user.analytics.tracking-code';
                    @endphp
                    <a href="{{ route($trackingCodeRoute, ['site' => $site->site_key]) }}" class="site-card-action-btn">كود التتبع</a>
                    @if(isset($isSuperAdmin) && $isSuperAdmin || $site->user_id == auth()->id())
                        @php
                            $membersRoute = isset($isSuperAdmin) && $isSuperAdmin 
                                ? 'admin.analytics.members' 
                                : 'user.analytics.members';
                        @endphp
                        <a href="{{ route($membersRoute, ['site' => $site->site_key]) }}" class="site-card-action-btn">إدارة الفريق</a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="empty-sites">
        <div class="empty-sites-icon">📊</div>
        <div>لا توجد مواقع تحليلات. <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.create') : route('user.analytics.create') }}">إنشاء واحد</a></div>
    </div>
    @endif
</div>

@if($sites->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script src="/js/chartjs.min.js"></script>
<script type="text/javascript">
var isDragging = false;

var sitesGrid = document.getElementById('sitesGrid');
if (!sitesGrid) {
    console.error('sitesGrid not found');
}
    
// Handle card clicks
if (sitesGrid) {
    sitesGrid.addEventListener('click', function(e) {
        if (isDragging) {
            return;
        }
        
        var card = e.target.closest('.site-card');
        var actions = e.target.closest('.site-card-actions');
        var isLink = e.target.tagName === 'A' || e.target.closest('a');
        
        if (card && !actions && !isLink) {
            var url = card.getAttribute('data-site-url');
            if (url) {
                window.location.href = url;
            }
        }
    });
    
    // Grid container is LTR for drag logic (CSS handles this via direction: ltr in CSS)
    // Visual layout remains the same - CSS Grid layout is independent of text direction
    // This ensures SortableJS collision detection works correctly
    
    // Initialize Sortable - Using forceFallback for consistent behavior
    // forceFallback: true uses element-based collision (works correctly with LTR container)
    new Sortable(sitesGrid, {
        animation: 200,
        forceFallback: true,
        fallbackTolerance: 5,
        fallbackOnBody: true,
        handle: '.site-card-drag-handle', // Only allow dragging from the handle icon
        filter: '.site-card-actions, .site-card-actions *',
        preventOnFilter: true,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        onStart: function(evt) {
            isDragging = true;
            // Lock dimensions to prevent layout shifts
            var rect = evt.item.getBoundingClientRect();
            evt.item.style.width = rect.width + 'px';
            evt.item.style.height = rect.height + 'px';
        },
        onEnd: function(evt) {
            isDragging = false;
            
            // Reset styles
            if (evt.item) {
                evt.item.style.width = '';
                evt.item.style.height = '';
            }
            
            // Get new order from DOM
            var items = sitesGrid.children;
            var sites = [];
            
            for (var i = 0; i < items.length; i++) {
                var siteId = items[i].getAttribute('data-site-id');
                if (siteId) {
                    sites.push({
                        id: parseInt(siteId),
                        order: i + 1
                    });
                }
            }
            
            if (sites.length === 0) {
                return;
            }
            
            // Save reorder
            var url = '{{ request()->routeIs("admin.*") ? route("admin.analytics.reorder") : route("user.analytics.reorder") }}';
            var token = '{{ csrf_token() }}';
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ sites: sites })
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (!data.success) {
                    console.error('Reorder failed:', data);
                }
            })
            .catch(function(error) {
                console.error('Reorder error:', error);
            });
        }
    });
}

// Initialize charts for each site - Active Users Last 30 Minutes (Bar Chart)
@foreach($sites as $site)
    @php
        $activeUsersChartData = $site->active_users_chart_data ?? [];
    @endphp
    @if(count($activeUsersChartData) > 0)
    const ctx{{ $site->id }} = document.getElementById('chart-{{ $site->id }}');
    if (ctx{{ $site->id }}) {
        new Chart(ctx{{ $site->id }}, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($activeUsersChartData as $point)
                    "{{ $point['time'] }}",
                    @endforeach
                ],
                datasets: [{
                    label: 'المستخدمون النشطون',
                    data: [
                        @foreach($activeUsersChartData as $point)
                        {{ $point['count'] }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(16, 185, 129, 0.6)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            title: function(context) {
                                var index = context[0].dataIndex;
                                var chartData = @json($activeUsersChartData);
                                return chartData[index] ? chartData[index].time : '';
                            },
                            label: function(context) {
                                return 'المستخدمون: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            display: false
                        },
                        grid: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    @endif
@endforeach
</script>

<script>
function editSiteTitle(siteId, currentTitle, domain, updateUrl) {
    var titleElement = document.getElementById('site-title-' + siteId);
    var domainElement = document.getElementById('site-domain-' + siteId);
    var currentText = currentTitle || domain;
    
    // Create input
    var input = document.createElement('input');
    input.type = 'text';
    input.className = 'site-title-edit-input';
    input.value = currentTitle || '';
    input.placeholder = domain;
    input.style.width = '100%';
    input.style.maxWidth = '200px';
    
    // Replace title with input
    var parent = titleElement.parentElement;
    var titleText = titleElement.textContent;
    titleElement.style.display = 'none';
    parent.insertBefore(input, titleElement);
    input.focus();
    input.select();
    
    // Save on Enter or blur
    function saveTitle() {
        var newTitle = input.value.trim();
        if (newTitle === currentTitle) {
            // No change, just cancel
            input.remove();
            titleElement.style.display = '';
            return;
        }
        
        // Save via AJAX
        fetch(updateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ title: newTitle })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                // Update title
                if (data.title && data.title !== data.domain) {
                    titleElement.textContent = data.title;
                    if (domainElement) {
                        domainElement.textContent = data.domain;
                    } else {
                        // Create domain element
                        var domainEl = document.createElement('small');
                        domainEl.className = 'site-card-domain';
                        domainEl.id = 'site-domain-' + siteId;
                        domainEl.textContent = data.domain;
                        titleElement.parentElement.appendChild(domainEl);
                    }
                } else {
                    titleElement.textContent = data.domain;
                    if (domainElement) {
                        domainElement.remove();
                    }
                }
            }
            input.remove();
            titleElement.style.display = '';
        })
        .catch(function(error) {
            console.error('Error updating title:', error);
            input.remove();
            titleElement.style.display = '';
        });
    }
    
    // Cancel on Escape
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveTitle();
        } else if (e.key === 'Escape') {
            e.preventDefault();
            input.remove();
            titleElement.style.display = '';
        }
    });
    
    input.addEventListener('blur', saveTitle);
}
</script>
@endif
@endsection
