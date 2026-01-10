@extends('layouts.admin')
@section('content')
<style>
    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #ffffff !important;
    }
    .badge-info {
        background-color: #3b82f6 !important;
        color: #ffffff !important;
    }
    .badge-primary {
        background-color: #6366f1 !important;
        color: #ffffff !important;
    }
    .badge-warning {
        background-color: #f59e0b !important;
        color: #ffffff !important;
    }
    .badge-secondary {
        background-color: #6b7280 !important;
        color: #ffffff !important;
    }
    .badge-dark {
        background-color: #1f2937 !important;
        color: #ffffff !important;
    }
    .table td {
        color: #111827 !important;
    }
    .table th {
        color: #1f2937 !important;
        font-weight: 600;
    }
</style>
<div class="col-12 p-3">
	<div class="col-12 col-lg-12 p-0 main-box">
	 
		<div class="col-12 px-0">
			<div class="col-12 p-0 row">
				<div class="col-12 col-lg-4 py-3 px-3">
					<span class="fal fa-bullhorn"></span> الإعلانات
				</div>
				<div class="col-12 col-lg-4 p-0">
				</div>
				<div class="col-12 col-lg-4 p-2 text-lg-end">
					@can('advertisements-create')
					<a href="{{route('admin.advertisements.create')}}">
						<span class="btn btn-primary"><span class="fas fa-plus"></span> إضافة جديد</span>
					</a>
					@endcan
				</div>
			</div>
			<div class="col-12 divider" style="min-height: 2px;"></div>
		</div>

		<div class="col-12 py-2 px-2 row">
			<div class="col-12 col-lg-4 p-2">
				<form method="GET">
					<input type="text" name="q" class="form-control" placeholder="بحث ... " value="{{request()->get('q')}}">
				</form>
			</div>
			<div class="col-12 col-lg-4 p-2">
				<form method="GET">
					<select name="is_active" class="form-control" onchange="this.form.submit()">
						<option value="">جميع الحالات</option>
						<option value="1" @if(request()->get('is_active') == '1') selected @endif>نشط</option>
						<option value="0" @if(request()->get('is_active') == '0') selected @endif>غير نشط</option>
					</select>
				</form>
			</div>
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
							@elseif($advertisement->type == 'pop-bottom' || $advertisement->type == 'pop_from_bottom')
								<span class="badge badge-info">منبثق من الأسفل</span>
							@elseif($advertisement->type == 'pop-top' || $advertisement->type == 'pop_from_top')
								<span class="badge badge-info">منبثق من الأعلى</span>
							@elseif($advertisement->type == 'interstitial' || $advertisement->type == 'Interstitial')
								<span class="badge badge-info">شاشة كاملة</span>
							@elseif($advertisement->type == 'in_content')
								<span class="badge badge-info">داخل المحتوى</span>
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
			            				'method'=>'DELETE',
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

