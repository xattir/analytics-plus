@extends('layouts.admin')
@section('content')
<div class="col-12 p-3">
    <div class="col-12 col-lg-12 p-0 ">
        <form id="validate-form" class="row" method="POST" action="{{route('admin.advertisements.update',['advertisement'=>$advertisement])}}">
            @csrf
            @method('PUT')
            <div class="col-12 col-lg-8 p-0 main-box">
                <div class="col-12 px-0">
                    <div class="col-12 px-3 py-3">
                        <span class="fas fa-ad"></span> تعديل الإعلان
                    </div>
                    <div class="col-12 divider" style="min-height: 2px;"></div>
                </div>
                <div class="col-12 p-3 row">
                    <div class="col-12 col-lg-6 p-2">
                        <div class="col-12">
                            اسم الإعلان <span class="text-danger">*</span>
                        </div>
                        <div class="col-12 pt-3">
                            <input type="text" name="name" required maxlength="255" class="form-control" value="{{old('name', $advertisement->name)}}">
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 p-2">
                        <div class="col-12">
                            النوع <span class="text-danger">*</span>
                        </div>
                        <div class="col-12 pt-3">
                            <select class="form-control" name="type" required>
                                <option value="html" @if(old('type', $advertisement->type) == 'html') selected @endif>HTML</option>
                                <option value="image" @if(old('type', $advertisement->type) == 'image') selected @endif>صورة</option>
                                <option value="video" @if(old('type', $advertisement->type) == 'video') selected @endif>فيديو</option>
                                <option value="text" @if(old('type', $advertisement->type) == 'text') selected @endif>نص</option>
                                <option value="script" @if(old('type', $advertisement->type) == 'script') selected @endif>Script</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 p-2">
                        <div class="col-12">
                            المحتوى <span class="text-danger">*</span>
                        </div>
                        <div class="col-12 pt-3">
                            <textarea name="content" required class="form-control" style="min-height:200px">{{old('content', $advertisement->content)}}</textarea>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 p-2">
                        <div class="col-12">
                            رابط الإعلان (اختياري)
                        </div>
                        <div class="col-12 pt-3">
                            <input type="url" name="url" maxlength="2048" class="form-control" value="{{old('url', $advertisement->url)}}">
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 p-2">
                        <div class="col-12">
                            الأولوية
                        </div>
                        <div class="col-12 pt-3">
                            <input type="number" name="priority" min="0" class="form-control" value="{{old('priority', $advertisement->priority)}}">
                        </div>
                    </div>
                    <div class="col-12 p-2">
                        <div class="col-12">
                            الحالة
                        </div>
                        <div class="col-12 pt-3">
                            <select class="form-control" name="is_active">
                                <option value="1" @if(old('is_active', $advertisement->is_active ? '1' : '0') == '1') selected @endif>نشط</option>
                                <option value="0" @if(old('is_active', $advertisement->is_active ? '1' : '0') == '0') selected @endif>غير نشط</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 p-0 main-box">
                <div class="col-12 px-0">
                    <div class="col-12 px-3 py-3">
                        <span class="fas fa-cog"></span> الإعدادات
                    </div>
                    <div class="col-12 divider" style="min-height: 2px;"></div>
                </div>
                <div class="col-12 p-3">
                    <div class="col-12 p-2">
                        <div class="col-12">
                            المواقع
                        </div>
                        <div class="col-12 pt-3">
                            <select class="form-control select2-select" name="site_ids[]" multiple size="1" style="height:30px;opacity: 0;">
                                @foreach($sites as $site)
                                <option value="{{$site->id}}" @if(in_array($site->id, $advertisement->sites->pluck('id')->toArray())) selected @endif>{{$site->title}} ({{$site->domain}})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 p-2">
                        <div class="col-12">
                            الدول (اترك فارغاً للكل)
                        </div>
                        <div class="col-12 pt-3">
                            <select class="form-control select2-select" name="country_codes[]" multiple size="1" style="height:30px;opacity: 0;">
                                @foreach($countries as $country)
                                <option value="{{$country['iso2']}}" @if(in_array($country['iso2'], $advertisement->countries->pluck('country_code')->toArray())) selected @endif>{{$country['name_ar'] ?? $country['name']}} ({{$country['iso2']}})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 p-2">
                        <div class="col-12">
                            الأجهزة (اترك فارغاً للكل)
                        </div>
                        <div class="col-12 pt-3">
                            <select class="form-control select2-select" name="device_types[]" multiple size="1" style="height:30px;opacity: 0;">
                                <option value="desktop" @if(in_array('desktop', $advertisement->devices->pluck('device_type')->toArray())) selected @endif>كمبيوتر</option>
                                <option value="mobile" @if(in_array('mobile', $advertisement->devices->pluck('device_type')->toArray())) selected @endif>موبايل</option>
                                <option value="tablet" @if(in_array('tablet', $advertisement->devices->pluck('device_type')->toArray())) selected @endif>تابلت</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 p-2">
                        <div class="col-12">
                            أنماط URL (اترك فارغاً للكل)
                        </div>
                        <div class="col-12 pt-3">
                            <select class="form-control select2-select" name="url_pattern_ids[]" multiple size="1" style="height:30px;opacity: 0;">
                                @foreach($urlPatterns as $pattern)
                                <option value="{{$pattern->id}}" @if(in_array($pattern->id, $advertisement->urlPatterns->pluck('id')->toArray())) selected @endif>{{$pattern->site->title}}: {{$pattern->pattern}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 p-2">
                        <div class="col-12">
                            استثناء أنماط URL
                        </div>
                        <div class="col-12 pt-3">
                            <select class="form-control select2-select" name="excluded_pattern_ids[]" multiple size="1" style="height:30px;opacity: 0;">
                                @foreach($urlPatterns as $pattern)
                                <option value="{{$pattern->id}}" @if(in_array($pattern->id, $advertisement->excludedPatterns->pluck('id')->toArray())) selected @endif>{{$pattern->site->title}}: {{$pattern->pattern}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 p-2">
                        <div class="col-12">
                            Selectors المحددة مسبقاً
                        </div>
                        <div class="col-12 pt-3">
                            <select class="form-control select2-select" name="predefined_selectors[]" multiple size="1" style="height:30px;opacity: 0;">
                                @foreach($predefinedSelectors as $tag => $selector)
                                <option value="{{$tag}}" @if(in_array($tag, $currentPredefinedTags)) selected @endif>{{$tag}} ({{$selector}})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 p-2">
                        <div class="col-12">
                            Selectors مخصصة (سطر واحد لكل selector)
                        </div>
                        <div class="col-12 pt-3">
                            <textarea name="custom_selectors" class="form-control" style="min-height:100px">{{implode("\n", $currentCustomSelectors)}}</textarea>
                        </div>
                    </div>
                    <div class="col-12 p-2">
                        <div class="col-12">
                            Subdomains (مفصولة بفواصل، اترك فارغاً للكل)
                        </div>
                        <div class="col-12 pt-3">
                            <input type="text" name="subdomains" class="form-control" value="{{$advertisement->subdomains->whereNotNull('subdomain')->pluck('subdomain')->implode(',')}}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 p-3">
                <button class="btn btn-success" id="submitEvaluation">حفظ</button>
            </div>
        </form>
    </div>
</div>
@endsection

