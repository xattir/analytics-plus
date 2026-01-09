@extends('layouts.admin', ['page_title' => 'إعلانات الموقع - ' . $site->title])

@section('content')
<div class="col-12 p-3">
	<div class="col-12 col-lg-12 p-0 main-box">
		<div class="col-12 px-0">
			<div class="col-12 p-0 row">
				<div class="col-12 col-lg-4 py-3 px-3">
					<span class="fas fa-ad"></span> إعلانات الموقع: {{ $site->title }}
				</div>
				<div class="col-12 col-lg-4 p-0">
				</div>
				<div class="col-12 col-lg-4 p-2 text-lg-end">
					<a href="{{ route('admin.analytics.patterns', $site) }}" class="btn btn-secondary">
						<span class="fas fa-arrow-right"></span> العودة للأنماط
					</a>
					@can('advertisements-create')
					<a href="{{ route('admin.advertisements.create') }}" class="btn btn-primary">
						<span class="fas fa-plus"></span> إضافة إعلان جديد
					</a>
					@endcan
				</div>
			</div>
			<div class="col-12 divider" style="min-height: 2px;"></div>
		</div>

		<div class="col-12 p-3" style="overflow:auto">
			<div class="col-12 p-0" style="min-width:1100px;min-height:50dvh">
			
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th>#</th>
						<th>الاسم</th>
						<th>النوع</th>
						<th>الأولوية</th>
						<th>الحالة</th>
						<th>عروض</th>
						<th>ضغطات</th>
						<th>CTR</th>
						<th>تحكم</th>
					</tr>
				</thead>
				<tbody>
					@foreach($advertisements as $advertisement)
					<tr>
						<td>{{$advertisement->id}}</td>
						<td>{{$advertisement->name}}</td>
						<td>
							@if($advertisement->type == 'html')
								<span class="badge badge-info">HTML</span>
							@elseif($advertisement->type == 'image')
								<span class="badge badge-primary">صورة</span>
							@elseif($advertisement->type == 'video')
								<span class="badge badge-warning">فيديو</span>
							@elseif($advertisement->type == 'text')
								<span class="badge badge-secondary">نص</span>
							@elseif($advertisement->type == 'script')
								<span class="badge badge-dark">Script</span>
							@endif
						</td>
						<td>{{$advertisement->priority}}</td>
						<td>
							@if($advertisement->is_active)
							<span class="fas fa-check-circle text-success"></span>
							@else
							<span class="fas fa-times-circle text-danger"></span>
							@endif
						</td>
						<td>{{number_format($advertisement->impressions_count)}}</td>
						<td>{{number_format($advertisement->clicks_count)}}</td>
						<td>
							@if($advertisement->impressions_count > 0)
								{{number_format(($advertisement->clicks_count / $advertisement->impressions_count) * 100, 2)}}%
							@else
								0%
							@endif
						</td>
						<td style="width: 1%;text-wrap: nowrap;">
							@include('components.control',[
			            		'links'=>[
			            			[
                                        'text'=>"إحصائيات",
                                        'icon'=>"fal fa-chart-bar",
                                        'can'=>"advertisements-read",
                                        'url'=>route('admin.advertisements.stats',['advertisement'=>$advertisement])
                                    ],
			            			[
			            				'text'=>"تعديل",
			            				'icon'=>"fal fa-edit",
			            				'can'=>"advertisements-update",
			            				'url'=>route('admin.advertisements.edit',['advertisement'=>$advertisement])
			            			],
			            			[
			            				'text'=>"حذف",
			            				'icon'=>"fal fa-trash",
			            				'can'=>"advertisements-delete",
			            				'url'=>route('admin.advertisements.destroy',['advertisement'=>$advertisement]),
			            				'class'=>'delete-btn'
			            			]
			            		]
			            	])
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			</div>
		</div>
		<div class="col-12 p-3">
			{{$advertisements->links()}}
		</div>
	</div>
</div>
@endsection

