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
                            <select class="form-control" name="type" required>
                                <option value="html" @if(old('type') == 'html') selected @endif>HTML</option>
                                <option value="image" @if(old('type') == 'image') selected @endif>صورة</option>
                                <option value="video" @if(old('type') == 'video') selected @endif>فيديو</option>
                                <option value="text" @if(old('type') == 'text') selected @endif>نص</option>
                                <option value="script" @if(old('type') == 'script') selected @endif>Script</option>
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
                    <div class="col-12 p-2">
                        <div class="col-12">
                            Selectors المحددة مسبقاً
                        </div>
                        <div class="col-12 pt-3">
                            <select class="form-control select2-select" name="predefined_selectors[]" multiple size="1" style="height:30px;opacity: 0;">
                                @foreach($predefinedSelectors as $tag => $selector)
                                <option value="{{$tag}}" @if(old('predefined_selectors') && in_array($tag, old('predefined_selectors'))) selected @endif>{{$tag}} ({{$selector}})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 p-2">
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

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSelect2);
} else {
    initSelect2();
}
</script>
@endsection

