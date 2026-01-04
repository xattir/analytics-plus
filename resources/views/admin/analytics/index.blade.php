@extends('layouts.admin', ['page_title' => 'مواقع التحليلات'])

@section('content')
<style>
    .analytics-sites-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
        margin-top: 24px;
    }
    
    .site-card {
        background: var(--background-1, #ffffff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 12px;
        padding: 20px;
        transition: all 0.2s;
        cursor: grab;
        position: relative;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .site-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
        border-color: var(--analytics-primary, #7b60fb);
    }
    
    .site-card.sortable-ghost {
        opacity: 0.4;
        cursor: grabbing;
    }
    
    .site-card.sortable-chosen {
        cursor: grabbing;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        transform: scale(1.02);
    }
    
    .site-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }
    
    .site-card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--color-2, #1f2937);
        margin: 0;
        flex: 1;
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
    
    .site-card.sortable-ghost {
        opacity: 0.4;
    }
    
    .site-card.sortable-chosen {
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
        text-align: center;
        padding: 60px 20px;
        color: var(--analytics-text-muted, #6b7280);
    }
    
    .empty-sites-icon {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.4;
    }
</style>

<div class="col-12 p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>مواقع التحليلات @if(isset($isSuperAdmin) && $isSuperAdmin) <small class="text-muted">(جميع المواقع - عرض المشرف)</small> @endif</h4>
        <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.create') : route('user.analytics.create') }}" class="btn btn-primary">إضافة موقع جديد</a>
    </div>
    
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
    <div class="analytics-sites-grid" id="sitesGrid">
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
                    <h3 class="site-card-title">{{ $site->domain }}</h3>
                    <span class="site-card-drag-handle">☰</span>
                </div>
                
                <div class="site-card-stats">
                    <div class="site-card-stat">
                        <span class="site-card-stat-label">المستخدمون النشطون</span>
                        <span class="site-card-stat-value">{{ number_format($activeUsers) }}</span>
                    </div>
                    <div class="site-card-stat">
                        <span class="site-card-stat-label">الجلسات</span>
                        <span class="site-card-stat-value">{{ number_format($site->sessions_count ?? 0) }}</span>
                    </div>
                </div>
                
                @if(count($chartData) > 0)
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js" integrity="sha512-zYXldzJsDrNKV+odAwFYiDXV2Cy37cwizT+NkuiPGsa9X1dOz04eHvUWVuxaJ299GvcJT31ug2zO4itXBjFx4w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="/js/chartjs.min.js"></script>
<script>
(function() {
    'use strict';
    
    const sitesGrid = document.getElementById('sitesGrid');
    if (!sitesGrid) return;
    
    let reorderTimeout = null;
    let isDragging = false;
    
    // Handle card clicks
    sitesGrid.addEventListener('click', function(e) {
        if (isDragging) {
            return;
        }
        
        const card = e.target.closest('.site-card');
        const actions = e.target.closest('.site-card-actions');
        const isLink = e.target.tagName === 'A' || e.target.closest('a');
        
        if (card && !actions && !isLink) {
            const url = card.getAttribute('data-site-url');
            if (url) {
                window.location.href = url;
            }
        }
    });
    
    // Function to save reorder
    function saveReorder() {
        const items = sitesGrid.children;
        const sites = [];
        
        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            const siteId = item.getAttribute('data-site-id');
            if (siteId) {
                sites.push({
                    id: parseInt(siteId),
                    order: i + 1
                });
            }
        }
        
        if (sites.length === 0) {
            console.log('No sites to reorder');
            return;
        }
        
        console.log('Saving reorder:', sites);
        
        const url = '{{ request()->routeIs("admin.*") ? route("admin.analytics.reorder") : route("user.analytics.reorder") }}';
        const token = '{{ csrf_token() }}';
        
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
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(function(data) {
            console.log('Reorder response:', data);
            if (data.success) {
                console.log('Reorder saved successfully');
            } else {
                console.error('Reorder failed:', data);
            }
        })
        .catch(function(error) {
            console.error('Reorder error:', error);
        });
    }
    
    // Initialize Sortable
    const sortable = Sortable.create(sitesGrid, {
        animation: 150,
        filter: '.site-card-actions, .site-card-actions *',
        preventOnFilter: true,
        onStart: function(evt) {
            isDragging = true;
            console.log('Drag started');
        },
        onMove: function(evt) {
            console.log('Moving');
        },
        onUpdate: function(evt) {
            console.log('Update event - new order:', evt.newIndex, 'old order:', evt.oldIndex);
            if (reorderTimeout) {
                clearTimeout(reorderTimeout);
            }
            reorderTimeout = setTimeout(saveReorder, 200);
        },
        onAdd: function(evt) {
            console.log('Add event');
        },
        onRemove: function(evt) {
            console.log('Remove event');
        },
        onEnd: function(evt) {
            console.log('Drag ended');
            isDragging = false;
            if (reorderTimeout) {
                clearTimeout(reorderTimeout);
            }
            saveReorder();
        }
    });
})();

// Initialize charts for each site
@foreach($sites as $site)
    @if(isset($site->active_users_chart_data) && count($site->active_users_chart_data) > 0)
    const ctx{{ $site->id }} = document.getElementById('chart-{{ $site->id }}');
    if (ctx{{ $site->id }}) {
        new Chart(ctx{{ $site->id }}, {
            type: 'line',
            data: {
                labels: [
                    @foreach($site->active_users_chart_data as $point)
                    "{{ $point['time'] }}",
                    @endforeach
                ],
                datasets: [{
                    label: 'مستخدمون نشطون',
                    data: [
                        @foreach($site->active_users_chart_data as $point)
                        {{ $point['count'] }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderColor: '#10b981',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 2,
                    pointHoverRadius: 4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        },
                        ticks: {
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
@endif
@endsection
