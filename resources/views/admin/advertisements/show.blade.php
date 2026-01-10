@extends('layouts.admin', ['page_title' => 'تفاصيل الإعلان - ' . $advertisement->name])

@section('content')
<div class="col-12 p-3">
    <div class="col-12 col-lg-12 p-0 main-box">
        <div class="col-12 px-0">
            <div class="col-12 px-3 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fas fa-ad"></span> تفاصيل الإعلان
                    </div>
                    <div>
                        <a href="{{ route('admin.advertisements.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-12 divider" style="min-height: 2px;"></div>
        </div>

        <div class="col-12 p-3 row">
            <div class="col-12 col-lg-6 p-2">
                <div class="col-12">
                    <strong>اسم الإعلان:</strong>
                </div>
                <div class="col-12 pt-2">
                    {{ $advertisement->name }}
                </div>
            </div>

            <div class="col-12 col-lg-6 p-2">
                <div class="col-12">
                    <strong>النوع:</strong>
                </div>
                <div class="col-12 pt-2">
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
                </div>
            </div>

            <div class="col-12 p-2">
                <div class="col-12">
                    <strong>المحتوى:</strong>
                </div>
                <div class="col-12 pt-2">
                    @if($advertisement->type == 'image')
                        <img src="{{ $advertisement->content }}" alt="Advertisement" style="max-width: 100%; height: auto;">
                    @else
                        <pre style="background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto;">{{ $advertisement->content }}</pre>
                    @endif
                </div>
            </div>

            @if($advertisement->url)
            <div class="col-12 col-lg-6 p-2">
                <div class="col-12">
                    <strong>رابط الإعلان:</strong>
                </div>
                <div class="col-12 pt-2">
                    <a href="{{ $advertisement->url }}" target="_blank">{{ $advertisement->url }}</a>
                </div>
            </div>
            @endif

            <div class="col-12 col-lg-6 p-2">
                <div class="col-12">
                    <strong>الأولوية:</strong>
                </div>
                <div class="col-12 pt-2">
                    {{ $advertisement->priority }}
                </div>
            </div>

            <div class="col-12 col-lg-6 p-2">
                <div class="col-12">
                    <strong>الحالة:</strong>
                </div>
                <div class="col-12 pt-2">
                    @if($advertisement->is_active)
                        <span class="fas fa-check-circle text-success"></span> نشط
                    @else
                        <span class="fas fa-times-circle text-danger"></span> غير نشط
                    @endif
                </div>
            </div>

            <div class="col-12 col-lg-6 p-2">
                <div class="col-12">
                    <strong>الإحصائيات:</strong>
                </div>
                <div class="col-12 pt-2">
                    <div>عروض: {{ number_format($advertisement->impressions_count) }}</div>
                    <div>ضغطات: {{ number_format($advertisement->clicks_count) }}</div>
                    <div>CTR: 
                        @if($advertisement->impressions_count > 0)
                            {{ number_format(($advertisement->clicks_count / $advertisement->impressions_count) * 100, 2) }}%
                        @else
                            0%
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 p-2">
                <div class="col-12">
                    <strong>المواقع:</strong>
                </div>
                <div class="col-12 pt-2">
                    @if($advertisement->sites->count() > 0)
                        @foreach($advertisement->sites as $site)
                            <span class="badge badge-primary">{{ $site->title }} ({{ $site->domain }})</span>
                        @endforeach
                    @else
                        <span class="text-muted">جميع المواقع</span>
                    @endif
                </div>
            </div>

            <div class="col-12 col-lg-6 p-2">
                <div class="col-12">
                    <strong>الدول:</strong>
                </div>
                <div class="col-12 pt-2">
                    @if($advertisement->countries->count() > 0)
                        @foreach($advertisement->countries as $country)
                            <span class="badge badge-info">{{ $country->country_code }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">جميع الدول</span>
                    @endif
                </div>
            </div>

            <div class="col-12 col-lg-6 p-2">
                <div class="col-12">
                    <strong>الأجهزة:</strong>
                </div>
                <div class="col-12 pt-2">
                    @if($advertisement->devices->count() > 0)
                        @foreach($advertisement->devices as $device)
                            <span class="badge badge-info">{{ $device->device_type }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">جميع الأجهزة</span>
                    @endif
                </div>
            </div>

            <div class="col-12 p-2">
                <div class="col-12">
                    <strong>أنماط URL:</strong>
                </div>
                <div class="col-12 pt-2">
                    @if($advertisement->urlPatterns->count() > 0)
                        @foreach($advertisement->urlPatterns as $pattern)
                            <span class="badge badge-secondary">{{ $pattern->pattern }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">جميع الصفحات</span>
                    @endif
                </div>
            </div>

            <div class="col-12 p-2">
                <div class="col-12">
                    <strong>أنماط URL المستثناة:</strong>
                </div>
                <div class="col-12 pt-2">
                    @if($advertisement->excludedPatterns->count() > 0)
                        @foreach($advertisement->excludedPatterns as $pattern)
                            <span class="badge badge-danger">{{ $pattern->pattern }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">لا يوجد</span>
                    @endif
                </div>
            </div>

            <div class="col-12 p-2">
                <div class="col-12">
                    <strong>Selectors:</strong>
                </div>
                <div class="col-12 pt-2">
                    @if($advertisement->selectors->count() > 0)
                        @foreach($advertisement->selectors as $selector)
                            <code class="badge badge-info">{{ $selector->selector }}</code>
                        @endforeach
                    @else
                        <span class="text-muted">لا يوجد</span>
                    @endif
                </div>
            </div>

            <div class="col-12 p-2">
                <div class="col-12">
                    <strong>Subdomains:</strong>
                </div>
                <div class="col-12 pt-2">
                    @if($advertisement->subdomains->count() > 0)
                        @foreach($advertisement->subdomains as $subdomain)
                            <span class="badge badge-info">{{ $subdomain->subdomain ?? 'All' }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">جميع Subdomains</span>
                    @endif
                </div>
            </div>

            <div class="col-12 p-3">
                <div class="d-flex gap-2">
                    @can('advertisements-update')
                    <a href="{{ route('admin.advertisements.edit', $advertisement) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                    @endcan
                    @can('advertisements-read')
                    <a href="{{ route('admin.advertisements.stats', $advertisement) }}" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> إحصائيات
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

