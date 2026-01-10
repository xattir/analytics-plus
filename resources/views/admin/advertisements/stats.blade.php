@php
$flat_colors = collect([
'#7b60fb',
'#7b60fbdd',
'#7cc5ffaa',
'#9ed2fb88',
'#0fb8ff66',
'#5aceff44',
'#8eddff22',
'#c5edff00',
'#c5edff00',
'#c5edff00',
'#c5edff00',
]);
@endphp
@extends('layouts.admin')
@section('content')
<div class="col-12 p-0">
    <div class="col-12 row p-0 d-flex">
        <!-- Header -->
        <div class="col-12 p-2">
            <div class="col-12 p-0 main-box">
                <div class="col-12 px-0">
                    <div class="col-12 p-0 row">
                        <div class="col-12 col-lg-6 py-3 px-3">
                            إحصائيات الإعلان: {{$advertisement->name}}
                        </div>
                        <div class="col-12 col-lg-6 p-2 text-lg-end">
                            <a href="{{route('admin.advertisements.index')}}">
                                <span class="btn btn-secondary"><span class="fas fa-arrow-right"></span> العودة</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-12 " style="min-height: 1px;background: var(--border-color);"></div>
                </div>
            </div>
        </div>

        <!-- Clicks Last 30 Minutes - Bar Chart -->
        <div class="col-12 col-lg-4 p-2">
            <div class="col-12 p-0 main-box">
                <div class="col-12 px-0">
                    <div class="col-12 px-3 py-3">
                        <div class="col-12 p-0">
                            <div class="col-12 p-0 row">
                                <div class="col-4">
                                    الضغطات آخر 30 دقيقة
                                </div>
                                <div class="col-8 d-flex justify-content-end align-items-center">
                                    <span style="font-weight: bold;">{{number_format($clicksLast30Minutes->sum('count'))}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 " style="min-height: 1px;background: var(--border-color);"></div>
                </div>
                <div class="col-12 p-3">
                    <canvas id="clicksLast30MinutesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Impressions Chart -->
        <div class="col-12 col-lg-4 p-2">
            <div class="col-12 p-0 main-box">
                <div class="col-12 px-0">
                    <div class="col-12 px-3 py-3">
                        <div class="col-12 p-0">
                            <div class="col-12 p-0 row">
                                <div class="col-4">
                                    العروض (آخر 30 يوم)
                                </div>
                                <div class="col-8 d-flex justify-content-end align-items-center">
                                    <span style="font-weight: bold;">{{number_format($impressionsByDate->sum('count'))}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 " style="min-height: 1px;background: var(--border-color);"></div>
                </div>
                <div class="col-12 p-3">
                    <canvas id="impressions-chart"></canvas>
                </div>
            </div>
        </div>

        <!-- Clicks Chart -->
        <div class="col-12 col-lg-4 p-2">
            <div class="col-12 p-0 main-box">
                <div class="col-12 px-0">
                    <div class="col-12 px-3 py-3">
                        <div class="col-12 p-0">
                            <div class="col-12 p-0 row">
                                <div class="col-4">
                                    الضغطات (آخر 30 يوم)
                                </div>
                                <div class="col-8 d-flex justify-content-end align-items-center">
                                    <span style="font-weight: bold;">{{number_format($clicksByDate->sum('count'))}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 " style="min-height: 1px;background: var(--border-color);"></div>
                </div>
                <div class="col-12 p-3">
                    <canvas id="clicks-chart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Pages by Clicks -->
        <div class="col-12 col-lg-4 p-2">
            <div class="col-12 p-0 main-box" style="min-height:100%">
                <div class="col-12 px-0">
                    <div class="col-12 px-3 py-3">
                        أعلى الصفحات حسب الضغطات
                    </div>
                    <div class="col-12 " style="min-height: 1px;background: var(--border-color);"></div>
                </div>
                <div class="col-12 p-3">
                    @foreach($topPagesByClicks as $page)
                    <div class="col-12 px-2 py-1 row">
                        <div class="col-4 p-0">
                            <span style="width: 30px;height: 17px;font-weight: bold;background: #7b60fb;color: #fff;" class="badge badge-light d-flex align-items-center justify-content-center">
                                {{$page->count}}
                            </span>
                        </div>
                        <div class="col-8 text-truncate p-0" style="direction:ltr;font-size: 12px;">
                            <a href="{{$page->url}}" target="_blank" style="color:inherit">
                                {{ Str::limit(parse_url($page->url, PHP_URL_PATH) ?: $page->url, 40) }}
                            </a>
                        </div>
                    </div>
                    @endforeach
                    @if($topPagesByClicks->isEmpty())
                    <div class="col-12 text-center py-3 text-muted">
                        لا توجد بيانات
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Sites by Clicks -->
        <div class="col-12 col-lg-4 p-2">
            <div class="col-12 p-0 main-box" style="min-height:100%">
                <div class="col-12 px-0">
                    <div class="col-12 px-3 py-3">
                        أعلى المواقع حسب الضغطات
                    </div>
                    <div class="col-12 " style="min-height: 1px;background: var(--border-color);"></div>
                </div>
                <div class="col-12 p-3">
                    @foreach($topSitesByClicks as $site)
                    <div class="col-12 px-2 py-1 row">
                        <div class="col-4 p-0">
                            <span style="width: 30px;height: 17px;font-weight: bold;background: #7b60fb;color: #fff;" class="badge badge-light d-flex align-items-center justify-content-center">
                                {{$site->clicks_count}}
                            </span>
                        </div>
                        <div class="col-8 text-truncate p-0" style="direction:ltr;font-size: 12px;">
                            <a href="//{{$site->domain}}" target="_blank" style="color:inherit">
                                <img src="https://icons.duckduckgo.com/ip3/{{$site->domain}}.ico" style="width:10px;height: 10px;" class="d-inline-block">
                                {{$site->title}}
                            </a>
                        </div>
                    </div>
                    @endforeach
                    @if($topSitesByClicks->isEmpty())
                    <div class="col-12 text-center py-3 text-muted">
                        لا توجد بيانات
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Sites by Impressions -->
        <div class="col-12 col-lg-4 p-2">
            <div class="col-12 p-0 main-box" style="min-height:100%">
                <div class="col-12 px-0">
                    <div class="col-12 px-3 py-3">
                        أعلى المواقع حسب المشاهدات
                    </div>
                    <div class="col-12 " style="min-height: 1px;background: var(--border-color);"></div>
                </div>
                <div class="col-12 p-3">
                    @foreach($topSitesByImpressions as $site)
                    <div class="col-12 px-2 py-1 row">
                        <div class="col-4 p-0">
                            <span style="width: 30px;height: 17px;font-weight: bold;background: #7b60fb;color: #fff;" class="badge badge-light d-flex align-items-center justify-content-center">
                                {{$site->impressions_count}}
                            </span>
                        </div>
                        <div class="col-8 text-truncate p-0" style="direction:ltr;font-size: 12px;">
                            <a href="//{{$site->domain}}" target="_blank" style="color:inherit">
                                <img src="https://icons.duckduckgo.com/ip3/{{$site->domain}}.ico" style="width:10px;height: 10px;" class="d-inline-block">
                                {{$site->title}}
                            </a>
                        </div>
                    </div>
                    @endforeach
                    @if($topSitesByImpressions->isEmpty())
                    <div class="col-12 text-center py-3 text-muted">
                        لا توجد بيانات
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Countries by Clicks -->
        <div class="col-12 col-lg-4 p-2">
            <div class="col-12 p-0 main-box" style="min-height:100%">
                <div class="col-12 px-0">
                    <div class="col-12 px-3 py-3">
                        أعلى الدول حسب الضغطات
                    </div>
                    <div class="col-12 " style="min-height: 1px;background: var(--border-color);"></div>
                </div>
                <div class="col-12 p-3 row">
                    @foreach($topCountriesByClicks as $country)
                    @php
                        $countryName = collect($countries)->firstWhere('iso2', $country->country_code)['name_ar'] ?? 
                                       collect($countries)->firstWhere('iso2', $country->country_code)['name'] ?? 
                                       $country->country_code;
                    @endphp
                    <div class="col-12 px-2 py-1 row">
                        <div class="col-4 p-0">
                            <span style="width: 30px;height: 17px;font-weight: bold;background: #7b60fb;color: #fff;" class="badge badge-light d-flex align-items-center justify-content-center">
                                {{$country->count}}
                            </span>
                        </div>
                        <div class="col-8 text-truncate p-0" style="direction:ltr;font-size: 12px;">
                            <span class="fi fi-{{strtolower($country->country_code)}} mx-1" style="font-size:10px"></span>
                            {{$countryName}}
                        </div>
                    </div>
                    @endforeach
                    @if($topCountriesByClicks->isEmpty())
                    <div class="col-12 text-center py-3 text-muted">
                        لا توجد بيانات
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Countries by Impressions -->
        <div class="col-12 col-lg-4 p-2">
            <div class="col-12 p-0 main-box" style="min-height:100%">
                <div class="col-12 px-0">
                    <div class="col-12 px-3 py-3">
                        أعلى الدول حسب المشاهدات
                    </div>
                    <div class="col-12 " style="min-height: 1px;background: var(--border-color);"></div>
                </div>
                <div class="col-12 p-3 row">
                    @foreach($topCountriesByImpressions as $country)
                    @php
                        $countryName = collect($countries)->firstWhere('iso2', $country->country_code)['name_ar'] ?? 
                                       collect($countries)->firstWhere('iso2', $country->country_code)['name'] ?? 
                                       $country->country_code;
                    @endphp
                    <div class="col-12 px-2 py-1 row">
                        <div class="col-4 p-0">
                            <span style="width: 30px;height: 17px;font-weight: bold;background: #7b60fb;color: #fff;" class="badge badge-light d-flex align-items-center justify-content-center">
                                {{$country->count}}
                            </span>
                        </div>
                        <div class="col-8 text-truncate p-0" style="direction:ltr;font-size: 12px;">
                            <span class="fi fi-{{strtolower($country->country_code)}} mx-1" style="font-size:10px"></span>
                            {{$countryName}}
                        </div>
                    </div>
                    @endforeach
                    @if($topCountriesByImpressions->isEmpty())
                    <div class="col-12 text-center py-3 text-muted">
                        لا توجد بيانات
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Browsers Chart -->
        <div class="col-12 col-lg-4 p-2">
            <div class="col-12 p-0 main-box">
                <div class="col-12 px-0">
                    <div class="col-12 px-3 py-3">
                        المتصفحات
                    </div>
                    <div class="col-12 " style="min-height: 1px;background: var(--border-color);"></div>
                </div>
                <div class="col-12 p-3">
                    <canvas id="ChartBrowsers" style="width:100%;max-height:250px"></canvas>
                </div>
            </div>
        </div>

        <!-- Operating Systems Chart -->
        <div class="col-12 col-lg-4 p-2">
            <div class="col-12 p-0 main-box">
                <div class="col-12 px-0">
                    <div class="col-12 px-3 py-3">
                        انظمة التشغيل
                    </div>
                    <div class="col-12 " style="min-height: 1px;background: var(--border-color);"></div>
                </div>
                <div class="col-12 p-3">
                    <canvas id="ChartOperatingSystems" style="width:100%;max-height:250px"></canvas>
                </div>
            </div>
        </div>

        <!-- Devices Chart -->
        <div class="col-12 col-lg-4 p-2">
            <div class="col-12 p-0 main-box">
                <div class="col-12 px-0">
                    <div class="col-12 px-3 py-3">
                        أعلى الأجهزة
                    </div>
                    <div class="col-12 " style="min-height: 1px;background: var(--border-color);"></div>
                </div>
                <div class="col-12 p-3">
                    <canvas id="ChartDevices" style="width:100%;max-height:250px"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="/js/chartjs.min.js"></script>
<script type="text/javascript">
    // Clicks Last 30 Minutes - Bar Chart
    new Chart(document.getElementById('clicksLast30MinutesChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: [
                @foreach($clicksLast30Minutes as $item)
                "{{ \Carbon\Carbon::parse($item->minute)->format('H:i') }}",
                @endforeach
            ],
            datasets: [{
                label: 'الضغطات',
                data: [
                    @foreach($clicksLast30Minutes as $item)
                    {{ $item->count }},
                    @endforeach
                ],
                backgroundColor: '#7b60fb',
                borderColor: '#7b60fb',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(123, 96, 251, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Impressions Chart - Line Chart (Last 30 days)
    new Chart(document.getElementById('impressions-chart').getContext('2d'), {
        type: 'line',
        data: {
            labels: [
                @foreach($impressionsByDate as $item)
                "{{ \Carbon\Carbon::parse($item->date)->format('Y-m-d') }}",
                @endforeach
            ],
            datasets: [{
                label: '# العروض',
                data: [
                    @foreach($impressionsByDate as $item)
                    "{{$item->count}}",
                    @endforeach
                ],
                backgroundColor: "#7b60fbcc",
                borderColor: '#7b60fb',
                pointStyle: 'rect',
                lineTension: '.15',
                tension: 0.1,
                fill: true,
                pointStyle:"circle",
                pointBorderColor:"#7b60fb",
                pointBackgroundColor:"#fff",
                pointRadius:4,
                borderWidth: 3.5,
            }]
        },
        options: {
            responsive:true,
            plugins: {
                legend: {
                    display:false,
                    labels: {
                        font: {
                            size: 14,
                            family:"kufi-arabic"
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero:false,
                    grid: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        display: true,
                        color:"rgb(3,169,244,0.05)"
                    }
                },
            },
            hover: {
                mode: 'index'
            },
            legend: {
                labels: {
                    fontFamily: 'kufi-arabic',
                    defaultFontFamily: 'kufi-arabic',
                }
            },
            elements: {
                line: {
                    tension: 1
                }
            }
        }
    });

    // Clicks Chart - Line Chart (Last 30 days)
    new Chart(document.getElementById('clicks-chart').getContext('2d'), {
        type: 'line',
        data: {
            labels: [
                @foreach($clicksByDate as $item)
                "{{ \Carbon\Carbon::parse($item->date)->format('Y-m-d') }}",
                @endforeach
            ],
            datasets: [{
                label: '# الضغطات',
                data: [
                    @foreach($clicksByDate as $item)
                    "{{$item->count}}",
                    @endforeach
                ],
                backgroundColor: "#7b60fbcc",
                borderColor: '#7b60fb',
                pointStyle: 'rect',
                lineTension: '.15',
                tension: 0.1,
                fill: true,
                pointStyle:"circle",
                pointBorderColor:"#7b60fb",
                pointBackgroundColor:"#fff",
                pointRadius:4,
                borderWidth: 3.5,
            }]
        },
        options: {
            responsive:true,
            plugins: {
                legend: {
                    display:false,
                    labels: {
                        font: {
                            size: 14,
                            family:"kufi-arabic"
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero:false,
                    grid: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        display: true,
                        color:"rgb(3,169,244,0.05)"
                    }
                },
            },
            hover: {
                mode: 'index'
            },
            legend: {
                labels: {
                    fontFamily: 'kufi-arabic',
                    defaultFontFamily: 'kufi-arabic',
                }
            },
            elements: {
                line: {
                    tension: 1
                }
            }
        }
    });

    // Browsers Chart - Doughnut
    const ChartBrowsers = new Chart(document.getElementById('ChartBrowsers'), {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($topBrowsers as $browser)
                "{{$browser->browser}}",
                @endforeach
            ],
            datasets: [{
                label: 'المتصفحات',
                data: [
                    @foreach($topBrowsers as $browser)
                    "{{$browser->count}}",
                    @endforeach
                ],
                backgroundColor: {!!json_encode($flat_colors) !!},
                borderColor: [
                    'transparent',
                ],
                borderWidth: 0
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Operating Systems Chart - Doughnut
    const ChartOperatingSystems = new Chart(document.getElementById('ChartOperatingSystems'), {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($topOperatingSystems as $os)
                "{{$os->operating_system}}",
                @endforeach
            ],
            datasets: [{
                label: 'أنظمة التشغيل',
                data: [
                    @foreach($topOperatingSystems as $os)
                    "{{$os->count}}",
                    @endforeach
                ],
                backgroundColor: {!!json_encode($flat_colors) !!},
                borderColor: [
                    'transparent',
                ],
                borderWidth: 0
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Devices Chart - Doughnut
    const ChartDevices = new Chart(document.getElementById('ChartDevices'), {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($clicksByDevice as $device)
                @if($device->device_type == 'desktop')
                    "كمبيوتر",
                @elseif($device->device_type == 'mobile')
                    "موبايل",
                @elseif($device->device_type == 'tablet')
                    "تابلت",
                @else
                    "{{ $device->device_type }}",
                @endif
                @endforeach
            ],
            datasets: [{
                label: 'الأجهزة',
                data: [
                    @foreach($clicksByDevice as $device)
                    "{{$device->count}}",
                    @endforeach
                ],
                backgroundColor: {!!json_encode($flat_colors) !!},
                borderColor: [
                    'transparent',
                ],
                borderWidth: 0
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
