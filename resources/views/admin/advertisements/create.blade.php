@extends('layouts.admin')
@section('content')
<div class="col-12 p-3">
    <div class="col-12 col-lg-12 p-0 ">
        <form id="validate-form" class="row" method="POST" action="{{route('admin.advertisements.store')}}">
            @csrf
            <div class="col-12 col-lg-8 p-0 main-box">
                <div class="col-12 px-0">
                    <div class="col-12 px-3 py-3">
                        <span class="fas fa-ad"></span> إضافة إعلان جديد
                    </div>
                    <div class="col-12 divider" style="min-height: 2px;"></div>
                </div>
                <div class="col-12 p-3 row">
                    <div class="col-12 col-lg-6 p-2">
                        <div class="col-12">
                            اسم الإعلان <span class="text-danger">*</span>
                        </div>
                        <div class="col-12 pt-3">
                            <input type="text" name="name" required maxlength="255" class="form-control" value="{{old('name')}}">
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 p-2">
                        <div class="col-12">
                            النوع <span class="text-danger">*</span>
                        </div>
                        <div class="col-12 pt-3">
                            <select class="form-control" name="type" id="ad_type" required onchange="toggleSelectorFields()">
                                <option value="in_content" @if(old('type') == 'in_content') selected @endif>In Content</option>
                                <option value="pop_from_bottom" @if(old('type') == 'pop_from_bottom') selected @endif>Pop from Bottom</option>
                                <option value="pop_from_top" @if(old('type') == 'pop_from_top') selected @endif>Pop from Top</option>
                                <option value="Interstitial" @if(old('type') == 'Interstitial') selected @endif>Interstitial</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 p-2">
                        <div class="col-12">
                            المحتوى <span class="text-danger">*</span>
                        </div>
                        <div class="col-12 pt-3">
                            <textarea name="content" required class="form-control" style="min-height:200px" placeholder="لصورة: أدخل رابط الصورة. لـ HTML/Script: أدخل الكود. لـ نص: أدخل النص.">{{old('content')}}</textarea>
                            <small class="text-muted">لصورة: أدخل رابط الصورة فقط. لـ HTML/Script: أدخل الكود. لـ نص: أدخل النص.</small>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 p-2">
                        <div class="col-12">
                            رابط الإعلان (اختياري)
                        </div>
                        <div class="col-12 pt-3">
                            <input type="url" name="url" maxlength="2048" class="form-control" value="{{old('url')}}" placeholder="https://example.com">
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 p-2">
                        <div class="col-12">
                            الأولوية
                        </div>
                        <div class="col-12 pt-3">
                            <input type="number" name="priority" min="0" class="form-control" value="{{old('priority', 0)}}">
                            <small class="text-muted">كلما زاد الرقم، زادت الأولوية</small>
                        </div>
                    </div>
                    <div class="col-12 p-2">
                        <div class="col-12">
                            الحالة
                        </div>
                        <div class="col-12 pt-3">
                            <select class="form-control" name="is_active">
                                <option value="1" @if(old('is_active', '1') == '1') selected @endif>نشط</option>
                                <option value="0" @if(old('is_active') == '0') selected @endif>غير نشط</option>
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
                                <option value="{{$site->id}}" @if(old('site_ids') && in_array($site->id, old('site_ids'))) selected @endif>{{$site->title}} ({{$site->domain}})</option>
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
                                <option value="{{$country['iso2']}}" @if(old('country_codes') && in_array($country['iso2'], old('country_codes'))) selected @endif>{{$country['name_ar'] ?? $country['name']}} ({{$country['iso2']}})</option>
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
                                <option value="desktop" @if(old('device_types') && in_array('desktop', old('device_types'))) selected @endif>كمبيوتر</option>
                                <option value="mobile" @if(old('device_types') && in_array('mobile', old('device_types'))) selected @endif>موبايل</option>
                                <option value="tablet" @if(old('device_types') && in_array('tablet', old('device_types'))) selected @endif>تابلت</option>
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
                                <option value="{{$pattern->id}}" @if(old('url_pattern_ids') && in_array($pattern->id, old('url_pattern_ids'))) selected @endif>{{$pattern->site->title}}: {{$pattern->pattern}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 p-2">
                        <div class="col-12">
                            Custom URL Patterns (سطر واحد لكل pattern)
                        </div>
                        <div class="col-12 pt-3">
                            <textarea name="custom_patterns" class="form-control" style="min-height:100px" placeholder="/products/*&#10;/blog/*&#10;/category/*">{{old('custom_patterns')}}</textarea>
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
                                <option value="{{$pattern->id}}" @if(old('excluded_pattern_ids') && in_array($pattern->id, old('excluded_pattern_ids'))) selected @endif>{{$pattern->site->title}}: {{$pattern->pattern}}</option>
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
                                <option value="{{$tag}}" @if(old('predefined_selectors') && in_array($tag, old('predefined_selectors'))) selected @endif>{{$tag}} ({{$selector}})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 p-2" id="custom_selector_field">
                        <div class="col-12">
                            Selectors مخصصة (سطر واحد لكل selector)
                        </div>
                        <div class="col-12 pt-3">
                            <textarea name="custom_selectors" class="form-control" style="min-height:100px" placeholder="#my-id&#10;.my-class&#10;[data-ad-zone]">{{old('custom_selectors')}}</textarea>
                        </div>
                    </div>
                    <div class="col-12 p-2">
                        <div class="col-12">
                            Subdomains (مفصولة بفواصل، اترك فارغاً للكل)
                        </div>
                        <div class="col-12 pt-3">
                            <input type="text" name="subdomains" class="form-control" value="{{old('subdomains')}}" placeholder="blog, shop, admin">
                        </div>
                    </div>
                    <div class="col-12 p-2" id="padding_fields" style="display: none;">
                        <div class="col-12">
                            Padding (بالـ px) - للـ Pop from Bottom/Top
                        </div>
                        <div class="col-12 pt-3 row">
                            <div class="col-6">
                                <label>Padding X</label>
                                <input type="number" name="padding_x" min="0" max="100" class="form-control" value="{{old('padding_x', 20)}}" placeholder="20">
                            </div>
                            <div class="col-6">
                                <label>Padding Y</label>
                                <input type="number" name="padding_y" min="0" max="100" class="form-control" value="{{old('padding_y', 20)}}" placeholder="20">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 p-2" id="interstitial_padding_field" style="display: none;">
                        <div class="col-12">
                            Padding (بالـ px) - للـ Interstitial
                        </div>
                        <div class="col-12 pt-3 row">
                            <div class="col-6">
                                <label>Padding X</label>
                                <input type="number" name="padding_x" id="interstitial_padding_x" min="0" max="100" class="form-control" value="{{old('padding_x', 20)}}" placeholder="20">
                            </div>
                            <div class="col-6">
                                <label>Padding Y</label>
                                <input type="number" name="padding_y" id="interstitial_padding_y" min="0" max="100" class="form-control" value="{{old('padding_y', 20)}}" placeholder="20">
                            </div>
                        </div>
                        <small class="text-muted">Padding للمحتوى داخل الصندوق</small>
                    </div>
                    <div class="col-12 p-2" id="interval_field" style="display: none;">
                        <div class="col-12">
                            Interval Period (بالثواني) - للـ Interstitial
                        </div>
                        <div class="col-12 pt-3">
                            <input type="number" name="interval_period" id="interval_period_input" min="0" class="form-control" value="{{old('interval_period')}}" placeholder="3600">
                            <small class="text-muted">المدة بالثواني قبل إظهار الإعلان مرة أخرى (0 = إظهار دائماً، اترك فارغاً = إخفاء تلقائي بعد 10 ثوانٍ)</small>
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

// Toggle selector fields based on ad type - make it global for inline handlers
window.toggleSelectorFields = function() {
    const adTypeSelect = document.getElementById('ad_type');
    if (!adTypeSelect) return;
    
    const adType = adTypeSelect.value;
    const selectorFields = document.getElementById('selector_fields');
    const customSelectorField = document.getElementById('custom_selector_field');
    const paddingFields = document.getElementById('padding_fields');
    const interstitialPaddingField = document.getElementById('interstitial_padding_field');
    const intervalField = document.getElementById('interval_field');
    
    // Special ad types don't need CSS selectors
    if (adType === 'pop_from_bottom' || adType === 'pop_from_top') {
        if (selectorFields) selectorFields.style.display = 'none';
        if (customSelectorField) customSelectorField.style.display = 'none';
        if (paddingFields) {
            paddingFields.style.setProperty('display', 'block', 'important');
        }
        if (interstitialPaddingField) interstitialPaddingField.style.display = 'none';
        if (intervalField) intervalField.style.display = 'none';
    } else if (adType === 'Interstitial') {
        if (selectorFields) selectorFields.style.display = 'none';
        if (customSelectorField) customSelectorField.style.display = 'none';
        if (paddingFields) paddingFields.style.display = 'none';
        if (interstitialPaddingField) {
            interstitialPaddingField.style.setProperty('display', 'block', 'important');
        }
        if (intervalField) {
            intervalField.style.setProperty('display', 'block', 'important');
        }
    } else {
        if (selectorFields) selectorFields.style.display = 'block';
        if (customSelectorField) customSelectorField.style.display = 'block';
        if (paddingFields) paddingFields.style.display = 'none';
        if (interstitialPaddingField) interstitialPaddingField.style.display = 'none';
        if (intervalField) intervalField.style.display = 'none';
    }
}

// Initialize when DOM is ready
function initializeForm() {
    initSelect2();
    // Wait a bit for select2 to initialize, then toggle fields
    setTimeout(function() {
        if (window.toggleSelectorFields) {
            window.toggleSelectorFields();
        }
    }, 100);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeForm);
} else {
    initializeForm();
}
</script>
@endsection

