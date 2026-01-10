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
<div class="col-12 p-3">
	<div class="col-12 col-lg-12 p-0 main-box">
		<div class="col-12 px-0">
			<div class="col-12 p-0 row">
				<div class="col-12 col-lg-4 py-3 px-3">
					<span class="fas fa-chart-bar"></span> إحصائيات الإعلان: {{$advertisement->name}}
				</div>
				<div class="col-12 col-lg-4 p-0">
				</div>
				<div class="col-12 col-lg-4 p-2 text-lg-end">
					<a href="{{route('admin.advertisements.index')}}">
						<span class="btn btn-secondary"><span class="fas fa-arrow-right"></span> العودة</span>
					</a>
				</div>
			</div>
			<div class="col-12 divider" style="min-height: 2px;"></div>
		</div>

		<div class="col-12 p-3 row">
			<div class="col-12 col-lg-3 p-2">
				<div class="col-12 p-3 main-box text-center">
					<div class="col-12">
						<span class="fas fa-eye fa-2x text-primary"></span>
					</div>
					<div class="col-12 pt-2">
						<h3>{{number_format($advertisement->impressions_count)}}</h3>
						<p>إجمالي العروض</p>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-3 p-2">
				<div class="col-12 p-3 main-box text-center">
					<div class="col-12">
						<span class="fas fa-mouse-pointer fa-2x text-success"></span>
					</div>
					<div class="col-12 pt-2">
						<h3>{{number_format($advertisement->clicks_count)}}</h3>
						<p>إجمالي الضغطات</p>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-3 p-2">
				<div class="col-12 p-3 main-box text-center">
					<div class="col-12">
						<span class="fas fa-percentage fa-2x text-warning"></span>
					</div>
					<div class="col-12 pt-2">
						<h3>
							@if($advertisement->impressions_count > 0)
								{{number_format(($advertisement->clicks_count / $advertisement->impressions_count) * 100, 2)}}%
							@else
								0%
							@endif
						</h3>
						<p>CTR (نسبة الضغط)</p>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-3 p-2">
				<div class="col-12 p-3 main-box text-center">
					<div class="col-12">
						<span class="fas fa-info-circle fa-2x text-info"></span>
					</div>
					<div class="col-12 pt-2">
						<h3>
							@if($advertisement->is_active)
								<span class="text-success">نشط</span>
							@else
								<span class="text-danger">غير نشط</span>
							@endif
						</h3>
						<p>الحالة</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Clicks Last 30 Minutes - Bar Chart -->
		<div class="col-12 p-3 row">
			<div class="col-12 col-lg-12 p-2">
				<div class="col-12 p-0 main-box">
					<div class="col-12 px-3 py-3">
						<span class="fas fa-chart-bar"></span> الضغطات آخر 30 دقيقة
					</div>
					<div class="col-12 divider" style="min-height: 2px;"></div>
					<div class="col-12 p-3">
						<canvas id="clicksLast30MinutesChart" style="max-height: 300px;"></canvas>
					</div>
				</div>
			</div>
		</div>

		<!-- Impressions and Clicks by Date - Line Charts -->
		<div class="col-12 p-3 row">
			<div class="col-12 col-lg-6 p-2">
				<div class="col-12 p-0 main-box">
					<div class="col-12 px-3 py-3">
						<span class="fas fa-chart-line"></span> العروض حسب التاريخ (آخر 30 يوم)
					</div>
					<div class="col-12 divider" style="min-height: 2px;"></div>
					<div class="col-12 p-3">
						<canvas id="impressionsByDateChart" style="max-height: 250px;"></canvas>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-6 p-2">
				<div class="col-12 p-0 main-box">
					<div class="col-12 px-3 py-3">
						<span class="fas fa-chart-line"></span> الضغطات حسب التاريخ (آخر 30 يوم)
					</div>
					<div class="col-12 divider" style="min-height: 2px;"></div>
					<div class="col-12 p-3">
						<canvas id="clicksByDateChart" style="max-height: 250px;"></canvas>
					</div>
				</div>
			</div>
		</div>

		<!-- Top Sites Charts -->
		<div class="col-12 p-3 row">
			<div class="col-12 col-lg-6 p-2">
				<div class="col-12 p-0 main-box">
					<div class="col-12 px-3 py-3">
						<span class="fas fa-globe"></span> أعلى المواقع حسب الضغطات
					</div>
					<div class="col-12 divider" style="min-height: 2px;"></div>
					<div class="col-12 p-3">
						<canvas id="topSitesByClicksChart" style="max-height: 250px;"></canvas>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-6 p-2">
				<div class="col-12 p-0 main-box">
					<div class="col-12 px-3 py-3">
						<span class="fas fa-globe"></span> أعلى المواقع حسب المشاهدات
					</div>
					<div class="col-12 divider" style="min-height: 2px;"></div>
					<div class="col-12 p-3">
						<canvas id="topSitesByImpressionsChart" style="max-height: 250px;"></canvas>
					</div>
				</div>
			</div>
		</div>

		<!-- Top Countries Charts -->
		<div class="col-12 p-3 row">
			<div class="col-12 col-lg-6 p-2">
				<div class="col-12 p-0 main-box">
					<div class="col-12 px-3 py-3">
						<span class="fas fa-flag"></span> أعلى الدول حسب الضغطات
					</div>
					<div class="col-12 divider" style="min-height: 2px;"></div>
					<div class="col-12 p-3">
						<canvas id="topCountriesByClicksChart" style="max-height: 250px;"></canvas>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-6 p-2">
				<div class="col-12 p-0 main-box">
					<div class="col-12 px-3 py-3">
						<span class="fas fa-flag"></span> أعلى الدول حسب المشاهدات
					</div>
					<div class="col-12 divider" style="min-height: 2px;"></div>
					<div class="col-12 p-3">
						<canvas id="topCountriesByImpressionsChart" style="max-height: 250px;"></canvas>
					</div>
				</div>
			</div>
		</div>

		<!-- Top Pages Charts -->
		<div class="col-12 p-3 row">
			<div class="col-12 col-lg-6 p-2">
				<div class="col-12 p-0 main-box">
					<div class="col-12 px-3 py-3">
						<span class="fas fa-link"></span> أعلى الصفحات حسب الضغطات
					</div>
					<div class="col-12 divider" style="min-height: 2px;"></div>
					<div class="col-12 p-3">
						<canvas id="topPagesByClicksChart" style="max-height: 250px;"></canvas>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-6 p-2">
				<div class="col-12 p-0 main-box">
					<div class="col-12 px-3 py-3">
						<span class="fas fa-link"></span> أعلى الصفحات حسب المشاهدات
					</div>
					<div class="col-12 divider" style="min-height: 2px;"></div>
					<div class="col-12 p-3">
						<canvas id="topPagesByImpressionsChart" style="max-height: 250px;"></canvas>
					</div>
				</div>
			</div>
		</div>

		<!-- Devices Charts -->
		<div class="col-12 p-3 row">
			<div class="col-12 col-lg-6 p-2">
				<div class="col-12 p-0 main-box">
					<div class="col-12 px-3 py-3">
						<span class="fas fa-mobile-alt"></span> الأجهزة حسب الضغطات
					</div>
					<div class="col-12 divider" style="min-height: 2px;"></div>
					<div class="col-12 p-3">
						<canvas id="clicksByDeviceChart" style="max-height: 250px;"></canvas>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-6 p-2">
				<div class="col-12 p-0 main-box">
					<div class="col-12 px-3 py-3">
						<span class="fas fa-mobile-alt"></span> الأجهزة حسب المشاهدات
					</div>
					<div class="col-12 divider" style="min-height: 2px;"></div>
					<div class="col-12 p-3">
						<canvas id="impressionsByDeviceChart" style="max-height: 250px;"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="/js/chartjs.min.js"></script>
<script type="text/javascript">
	// Prepare data for clicks last 30 minutes
	const clicksLast30MinutesData = {
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
	};

	// Clicks Last 30 Minutes - Bar Chart
	new Chart(document.getElementById('clicksLast30MinutesChart').getContext('2d'), {
		type: 'bar',
		data: clicksLast30MinutesData,
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

	// Impressions by Date - Line Chart
	new Chart(document.getElementById('impressionsByDateChart').getContext('2d'), {
		type: 'line',
		data: {
			labels: [
				@foreach($impressionsByDate as $item)
				"{{ \Carbon\Carbon::parse($item->date)->format('Y-m-d') }}",
				@endforeach
			],
			datasets: [{
				label: 'العروض',
				data: [
					@foreach($impressionsByDate as $item)
					{{ $item->count }},
					@endforeach
				],
				backgroundColor: '#7b60fbcc',
				borderColor: '#7b60fb',
				tension: 0.1,
				fill: true,
				pointRadius: 4,
				borderWidth: 3.5
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

	// Clicks by Date - Line Chart
	new Chart(document.getElementById('clicksByDateChart').getContext('2d'), {
		type: 'line',
		data: {
			labels: [
				@foreach($clicksByDate as $item)
				"{{ \Carbon\Carbon::parse($item->date)->format('Y-m-d') }}",
				@endforeach
			],
			datasets: [{
				label: 'الضغطات',
				data: [
					@foreach($clicksByDate as $item)
					{{ $item->count }},
					@endforeach
				],
				backgroundColor: '#10b981cc',
				borderColor: '#10b981',
				tension: 0.1,
				fill: true,
				pointRadius: 4,
				borderWidth: 3.5
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
						color: 'rgba(16, 185, 129, 0.05)'
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

	// Top Sites by Clicks - Doughnut Chart
	new Chart(document.getElementById('topSitesByClicksChart'), {
		type: 'doughnut',
		data: {
			labels: [
				@foreach($topSitesByClicks as $site)
				"{{ $site->title }}",
				@endforeach
			],
			datasets: [{
				label: 'الضغطات',
				data: [
					@foreach($topSitesByClicks as $site)
					{{ $site->clicks_count }},
					@endforeach
				],
				backgroundColor: {!! json_encode($flat_colors->toArray()) !!},
				borderColor: 'transparent',
				borderWidth: 0
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

	// Top Sites by Impressions - Doughnut Chart
	new Chart(document.getElementById('topSitesByImpressionsChart'), {
		type: 'doughnut',
		data: {
			labels: [
				@foreach($topSitesByImpressions as $site)
				"{{ $site->title }}",
				@endforeach
			],
			datasets: [{
				label: 'المشاهدات',
				data: [
					@foreach($topSitesByImpressions as $site)
					{{ $site->impressions_count }},
					@endforeach
				],
				backgroundColor: {!! json_encode($flat_colors->toArray()) !!},
				borderColor: 'transparent',
				borderWidth: 0
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

	// Top Countries by Clicks - Doughnut Chart
	new Chart(document.getElementById('topCountriesByClicksChart'), {
		type: 'doughnut',
		data: {
			labels: [
				@foreach($topCountriesByClicks as $country)
				@php
					$countryName = collect($countries)->firstWhere('iso2', $country->country_code)['name_ar'] ?? 
								   collect($countries)->firstWhere('iso2', $country->country_code)['name'] ?? 
								   $country->country_code;
				@endphp
				"{{ $countryName }}",
				@endforeach
			],
			datasets: [{
				label: 'الضغطات',
				data: [
					@foreach($topCountriesByClicks as $country)
					{{ $country->count }},
					@endforeach
				],
				backgroundColor: {!! json_encode($flat_colors->toArray()) !!},
				borderColor: 'transparent',
				borderWidth: 0
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

	// Top Countries by Impressions - Doughnut Chart
	new Chart(document.getElementById('topCountriesByImpressionsChart'), {
		type: 'doughnut',
		data: {
			labels: [
				@foreach($topCountriesByImpressions as $country)
				@php
					$countryName = collect($countries)->firstWhere('iso2', $country->country_code)['name_ar'] ?? 
								   collect($countries)->firstWhere('iso2', $country->country_code)['name'] ?? 
								   $country->country_code;
				@endphp
				"{{ $countryName }}",
				@endforeach
			],
			datasets: [{
				label: 'المشاهدات',
				data: [
					@foreach($topCountriesByImpressions as $country)
					{{ $country->count }},
					@endforeach
				],
				backgroundColor: {!! json_encode($flat_colors->toArray()) !!},
				borderColor: 'transparent',
				borderWidth: 0
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

	// Top Pages by Clicks - Bar Chart
	new Chart(document.getElementById('topPagesByClicksChart').getContext('2d'), {
		type: 'bar',
		data: {
			labels: [
				@foreach($topPagesByClicks as $page)
				"{{ Str::limit(parse_url($page->url, PHP_URL_PATH) ?: $page->url, 30) }}",
				@endforeach
			],
			datasets: [{
				label: 'الضغطات',
				data: [
					@foreach($topPagesByClicks as $page)
					{{ $page->count }},
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
			indexAxis: 'y',
			plugins: {
				legend: {
					display: false
				}
			},
			scales: {
				x: {
					beginAtZero: true,
					grid: {
						color: 'rgba(123, 96, 251, 0.05)'
					}
				},
				y: {
					grid: {
						display: false
					}
				}
			}
		}
	});

	// Top Pages by Impressions - Bar Chart
	new Chart(document.getElementById('topPagesByImpressionsChart').getContext('2d'), {
		type: 'bar',
		data: {
			labels: [
				@foreach($topPagesByImpressions as $page)
				"{{ Str::limit(parse_url($page->url, PHP_URL_PATH) ?: $page->url, 30) }}",
				@endforeach
			],
			datasets: [{
				label: 'المشاهدات',
				data: [
					@foreach($topPagesByImpressions as $page)
					{{ $page->count }},
					@endforeach
				],
				backgroundColor: '#3b82f6',
				borderColor: '#3b82f6',
				borderWidth: 2
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: true,
			indexAxis: 'y',
			plugins: {
				legend: {
					display: false
				}
			},
			scales: {
				x: {
					beginAtZero: true,
					grid: {
						color: 'rgba(59, 130, 246, 0.05)'
					}
				},
				y: {
					grid: {
						display: false
					}
				}
			}
		}
	});

	// Clicks by Device - Doughnut Chart
	new Chart(document.getElementById('clicksByDeviceChart'), {
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
				label: 'الضغطات',
				data: [
					@foreach($clicksByDevice as $device)
					{{ $device->count }},
					@endforeach
				],
				backgroundColor: {!! json_encode($flat_colors->toArray()) !!},
				borderColor: 'transparent',
				borderWidth: 0
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

	// Impressions by Device - Doughnut Chart
	new Chart(document.getElementById('impressionsByDeviceChart'), {
		type: 'doughnut',
		data: {
			labels: [
				@foreach($impressionsByDevice as $device)
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
				label: 'المشاهدات',
				data: [
					@foreach($impressionsByDevice as $device)
					{{ $device->count }},
					@endforeach
				],
				backgroundColor: {!! json_encode($flat_colors->toArray()) !!},
				borderColor: 'transparent',
				borderWidth: 0
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
</script>
@endsection
