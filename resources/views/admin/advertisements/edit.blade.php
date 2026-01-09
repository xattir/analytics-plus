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
                            <select class="form-control" name="type" id="ad_type" required onchange="toggleSelectorFields()">
                                <option value="in_content" @if(old('type', $advertisement->type) == 'in_content') selected @endif>In Content</option>
                                <option value="pop_from_bottom" @if(old('type', $advertisement->type) == 'pop_from_bottom') selected @endif>Pop from Bottom</option>
                                <option value="pop_from_top" @if(old('type', $advertisement->type) == 'pop_from_top') selected @endif>Pop from Top</option>
                                <option value="Interstitial" @if(old('type', $advertisement->type) == 'Interstitial') selected @endif>Interstitial</option>
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
                            Custom URL Patterns (سطر واحد لكل pattern)
                        </div>
                        <div class="col-12 pt-3">
                            <textarea name="custom_patterns" class="form-control" style="min-height:100px" placeholder="/products/*&#10;/blog/*&#10;/category/*">{{old('custom_patterns', $advertisement->custom_patterns ?? '')}}</textarea>
                            <small class="text-muted">أدخل patterns مخصصة (مثل /products/* أو /blog/*). يمكنك استخدام * كـ wildcard.</small>
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
                    <div class="col-12 p-2" id="selector_fields">
                        <div class="col-12">
                            Selectors المحددة مسبقاً
                        </div>
                        <div class="col-12 pt-3">
                            <select class="form-control select2-select" name="predefined_selectors[]" id="predefined_selectors" multiple size="1" style="height:30px;opacity: 0;">
                                @foreach($predefinedSelectors as $tag => $selector)
                                <option value="{{$tag}}" @if(in_array($tag, $currentPredefinedTags)) selected @endif>{{$tag}} ({{$selector}})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 p-2" id="custom_selector_field">
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

@section('scripts')
<script type="module">
// Wait for jQuery and select2 to be available
function initSelect2() {
    if (typeof window.$ !== 'undefined' && typeof window.$.fn.select2 !== 'undefined') {
        $('.select2-select').select2({
            dir: 'rtl',
            language: 'ar',
            width: '100%'
        });
    } else {
        setTimeout(initSelect2, 100);
    }
}

// Toggle selector fields based on ad type
function toggleSelectorFields() {
    const adType = document.getElementById('ad_type').value;
    const selectorFields = document.getElementById('selector_fields');
    const customSelectorField = document.getElementById('custom_selector_field');
    
    // Special ad types don't need CSS selectors
    if (adType === 'pop_from_bottom' || adType === 'pop_from_top' || adType === 'Interstitial') {
        if (selectorFields) selectorFields.style.display = 'none';
        if (customSelectorField) customSelectorField.style.display = 'none';
        const paddingFields = document.getElementById('padding_fields');
        const paddingYField = document.getElementById('padding_y_field');
        const intervalField = document.getElementById('interval_field');
        if (paddingFields) paddingFields.style.display = 'block';
        if (paddingYField) paddingYField.style.display = 'block';
        if (intervalField) intervalField.style.display = adType === 'Interstitial' ? 'block' : 'none';
    } else {
        if (selectorFields) selectorFields.style.display = 'block';
        if (customSelectorField) customSelectorField.style.display = 'block';
        const paddingFields = document.getElementById('padding_fields');
        const paddingYField = document.getElementById('padding_y_field');
        const intervalField = document.getElementById('interval_field');
        if (paddingFields) paddingFields.style.display = 'none';
        if (paddingYField) paddingYField.style.display = 'none';
        if (intervalField) intervalField.style.display = 'none';
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        initSelect2();
        toggleSelectorFields();
    });
} else {
    initSelect2();
    toggleSelectorFields();
}
</script>
@endsection

