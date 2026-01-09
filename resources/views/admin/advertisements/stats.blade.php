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

		<div class="col-12 p-3 row">
			<div class="col-12 col-lg-6 p-2">
				<div class="col-12 p-0 main-box">
					<div class="col-12 px-3 py-3">
						<span class="fas fa-chart-line"></span> العروض حسب التاريخ (آخر 30 يوم)
					</div>
					<div class="col-12 divider" style="min-height: 2px;"></div>
					<div class="col-12 p-3">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>التاريخ</th>
									<th>عدد العروض</th>
								</tr>
							</thead>
							<tbody>
								@foreach($impressionsByDate as $item)
								<tr>
									<td>{{$item->date}}</td>
									<td>{{number_format($item->count)}}</td>
								</tr>
								@endforeach
								@if($impressionsByDate->isEmpty())
								<tr>
									<td colspan="2" class="text-center">لا توجد بيانات</td>
								</tr>
								@endif
							</tbody>
						</table>
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
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>التاريخ</th>
									<th>عدد الضغطات</th>
								</tr>
							</thead>
							<tbody>
								@foreach($clicksByDate as $item)
								<tr>
									<td>{{$item->date}}</td>
									<td>{{number_format($item->count)}}</td>
								</tr>
								@endforeach
								@if($clicksByDate->isEmpty())
								<tr>
									<td colspan="2" class="text-center">لا توجد بيانات</td>
								</tr>
								@endif
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="col-12 p-3 row">
			<div class="col-12 col-lg-6 p-2">
				<div class="col-12 p-0 main-box">
					<div class="col-12 px-3 py-3">
						<span class="fas fa-globe"></span> العروض حسب الدولة (أعلى 10)
					</div>
					<div class="col-12 divider" style="min-height: 2px;"></div>
					<div class="col-12 p-3">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>الدولة</th>
									<th>عدد العروض</th>
								</tr>
							</thead>
							<tbody>
								@foreach($impressionsByCountry as $item)
								<tr>
									<td>{{$item->country_code}}</td>
									<td>{{number_format($item->count)}}</td>
								</tr>
								@endforeach
								@if($impressionsByCountry->isEmpty())
								<tr>
									<td colspan="2" class="text-center">لا توجد بيانات</td>
								</tr>
								@endif
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-6 p-2">
				<div class="col-12 p-0 main-box">
					<div class="col-12 px-3 py-3">
						<span class="fas fa-mobile-alt"></span> العروض حسب نوع الجهاز
					</div>
					<div class="col-12 divider" style="min-height: 2px;"></div>
					<div class="col-12 p-3">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>نوع الجهاز</th>
									<th>عدد العروض</th>
								</tr>
							</thead>
							<tbody>
								@foreach($impressionsByDevice as $item)
								<tr>
									<td>
										@if($item->device_type == 'desktop')
											كمبيوتر
										@elseif($item->device_type == 'mobile')
											موبايل
										@elseif($item->device_type == 'tablet')
											تابلت
										@else
											{{$item->device_type}}
										@endif
									</td>
									<td>{{number_format($item->count)}}</td>
								</tr>
								@endforeach
								@if($impressionsByDevice->isEmpty())
								<tr>
									<td colspan="2" class="text-center">لا توجد بيانات</td>
								</tr>
								@endif
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

