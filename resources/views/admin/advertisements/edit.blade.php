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
                            <textarea name="content" id="content-editor" required style="display:none;">{{old('content', $advertisement->content)}}</textarea>
                            <div id="content-editor-container" style="direction: ltr; text-align: left;"></div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 p-2">
                        <div class="col-12">
                            رابط الإعلان (اختياري)
                        </div>
                        <div class="col-12 pt-3">
                            <input type="url" name="url" id="url-editor" maxlength="2048" class="form-control" value="{{old('url', $advertisement->url)}}" style="direction: ltr; text-align: left;">
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
                            <textarea name="custom_patterns" id="patterns-editor" style="display:none;">{{old('custom_patterns', $advertisement->custom_patterns ?? '')}}</textarea>
                            <div id="patterns-editor-container" style="direction: ltr; text-align: left;"></div>
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
                            <textarea name="custom_selectors" id="selectors-editor" style="display:none;">{{implode("\n", $currentCustomSelectors)}}</textarea>
                            <div id="selectors-editor-container" style="direction: ltr; text-align: left;"></div>
                        </div>
                    </div>
                    <div class="col-12 p-2">
                        <div class="col-12">
                            Subdomains (مفصولة بفواصل، اترك فارغاً للكل)
                        </div>
                        <div class="col-12 pt-3">
                            <textarea name="subdomains" id="subdomains-editor" style="display:none;">{{$advertisement->subdomains->whereNotNull('subdomain')->pluck('subdomain')->implode(',')}}</textarea>
                            <div id="subdomains-editor-container" style="direction: ltr; text-align: left;"></div>
                        </div>
                    </div>
                    <div class="col-12 p-2" id="padding_fields" style="display: none;">
                        <div class="col-12">
                            Padding (بالـ px) - للـ Pop from Bottom/Top
                        </div>
                        <div class="col-12 pt-3 row">
                            <div class="col-6">
                                <label>Padding X</label>
                                <input type="number" name="padding_x" min="0" max="100" class="form-control" value="{{old('padding_x', $advertisement->padding_x ?? 20)}}" placeholder="20">
                            </div>
                            <div class="col-6">
                                <label>Padding Y</label>
                                <input type="number" name="padding_y" min="0" max="100" class="form-control" value="{{old('padding_y', $advertisement->padding_y ?? 20)}}" placeholder="20">
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
                                <input type="number" name="padding_x" id="interstitial_padding_x" min="0" max="100" class="form-control" value="{{old('padding_x', $advertisement->padding_x ?? 20)}}" placeholder="20">
                            </div>
                            <div class="col-6">
                                <label>Padding Y</label>
                                <input type="number" name="padding_y" id="interstitial_padding_y" min="0" max="100" class="form-control" value="{{old('padding_y', $advertisement->padding_y ?? 20)}}" placeholder="20">
                            </div>
                        </div>
                        <small class="text-muted">Padding للمحتوى داخل الصندوق</small>
                    </div>
                    <div class="col-12 p-2" id="interval_field" style="display: none;">
                        <div class="col-12">
                            Interval Period (بالثواني) - للـ Interstitial
                        </div>
                        <div class="col-12 pt-3">
                            <input type="number" name="interval_period" id="interval_period_input" min="0" class="form-control" value="{{old('interval_period', $advertisement->interval_period ?? '')}}" placeholder="3600">
                            <small class="text-muted">المدة بالثواني قبل إظهار الإعلان مرة أخرى (0 = إظهار دائماً، اترك فارغاً = إخفاء تلقائي بعد 10 ثوانٍ)</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 p-3">
                <button type="submit" class="btn btn-success" id="submitEvaluation">حفظ</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/lib/codemirror.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/theme/monokai.css">
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/lib/codemirror.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/htmlmixed/htmlmixed.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/xml/xml.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/javascript/javascript.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/css/css.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/clike/clike.js"></script>

<style>
.CodeMirror,
.CodeMirror *,
#content-editor-container,
#content-editor-container *,
#patterns-editor-container,
#patterns-editor-container *,
#selectors-editor-container,
#selectors-editor-container *,
#subdomains-editor-container,
#subdomains-editor-container * {
    direction: ltr !important;
    text-align: left !important;
}

.CodeMirror {
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', 'source-code-pro', monospace;
}

.CodeMirror-focused {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.CodeMirror-scroll {
    direction: ltr !important;
}

.CodeMirror-lines {
    direction: ltr !important;
    text-align: left !important;
}

.CodeMirror-line {
    direction: ltr !important;
    text-align: left !important;
}

.CodeMirror-linenumber {
    direction: ltr !important;
    text-align: left !important;
}

.CodeMirror-gutters {
    direction: ltr !important;
    left: 0 !important;
    right: auto !important;
}

.CodeMirror-gutter {
    direction: ltr !important;
}

.CodeMirror-cursor {
    direction: ltr !important;
}

.CodeMirror-code {
    direction: ltr !important;
    text-align: left !important;
}

#content-editor-container .CodeMirror,
#patterns-editor-container .CodeMirror,
#selectors-editor-container .CodeMirror,
#subdomains-editor-container .CodeMirror {
    border: none;
    height: auto;
    direction: ltr !important;
    text-align: left !important;
}
</style>

<script type="module">
// Initialize CodeMirror editors
let contentEditor, patternsEditor, selectorsEditor, subdomainsEditor;

function initCodeEditors() {
    // Check if CodeMirror is loaded
    if (typeof CodeMirror === 'undefined') {
        setTimeout(initCodeEditors, 100);
        return;
    }
    
    // Content Editor
    const contentTextarea = document.getElementById('content-editor');
    const contentContainer = document.getElementById('content-editor-container');
    if (contentTextarea && contentContainer) {
        // Force LTR on container
        contentContainer.style.direction = 'ltr';
        contentContainer.style.textAlign = 'left';
        
        contentEditor = CodeMirror(contentContainer, {
            value: contentTextarea.value || '',
            mode: 'htmlmixed',
            theme: 'default',
            lineNumbers: true,
            lineWrapping: true,
            direction: 'ltr',
            indentUnit: 2,
            autoCloseTags: true,
            matchBrackets: true,
            rtlMoveVisually: false,
        });
        contentEditor.setSize('100%', '200px');
        
        // Force LTR on all CodeMirror elements
        const cmWrapper = contentEditor.getWrapperElement();
        cmWrapper.style.direction = 'ltr';
        cmWrapper.style.textAlign = 'left';
        cmWrapper.setAttribute('dir', 'ltr');
        
        // Force LTR on scroll element
        const cmScroll = cmWrapper.querySelector('.CodeMirror-scroll');
        if (cmScroll) {
            cmScroll.style.direction = 'ltr';
            cmScroll.style.textAlign = 'left';
        }
        
        // Force LTR on lines
        const cmLines = cmWrapper.querySelector('.CodeMirror-lines');
        if (cmLines) {
            cmLines.style.direction = 'ltr';
            cmLines.style.textAlign = 'left';
        }
        
        // Force LTR on gutters
        const cmGutters = cmWrapper.querySelector('.CodeMirror-gutters');
        if (cmGutters) {
            cmGutters.style.direction = 'ltr';
            cmGutters.style.left = '0';
            cmGutters.style.right = 'auto';
        }
        
        contentEditor.on('change', function(cm) {
            contentTextarea.value = cm.getValue();
        });
        
        // Force refresh to apply changes
        setTimeout(function() {
            contentEditor.refresh();
        }, 100);
    }

    // Patterns Editor
    const patternsTextarea = document.getElementById('patterns-editor');
    const patternsContainer = document.getElementById('patterns-editor-container');
    if (patternsTextarea && patternsContainer) {
        patternsContainer.style.direction = 'ltr';
        patternsContainer.style.textAlign = 'left';
        
        patternsEditor = CodeMirror(patternsContainer, {
            value: patternsTextarea.value || '',
            mode: 'text/plain',
            theme: 'default',
            lineNumbers: true,
            lineWrapping: true,
            direction: 'ltr',
            indentUnit: 2,
            rtlMoveVisually: false,
        });
        patternsEditor.setSize('100%', '100px');
        
        const cmWrapper = patternsEditor.getWrapperElement();
        cmWrapper.style.direction = 'ltr';
        cmWrapper.style.textAlign = 'left';
        cmWrapper.setAttribute('dir', 'ltr');
        
        patternsEditor.on('change', function(cm) {
            patternsTextarea.value = cm.getValue();
        });
        
        setTimeout(function() {
            patternsEditor.refresh();
        }, 100);
    }

    // Selectors Editor
    const selectorsTextarea = document.getElementById('selectors-editor');
    const selectorsContainer = document.getElementById('selectors-editor-container');
    if (selectorsTextarea && selectorsContainer) {
        selectorsContainer.style.direction = 'ltr';
        selectorsContainer.style.textAlign = 'left';
        
        selectorsEditor = CodeMirror(selectorsContainer, {
            value: selectorsTextarea.value || '',
            mode: 'css',
            theme: 'default',
            lineNumbers: true,
            lineWrapping: true,
            direction: 'ltr',
            indentUnit: 2,
            rtlMoveVisually: false,
        });
        selectorsEditor.setSize('100%', '100px');
        
        const cmWrapper = selectorsEditor.getWrapperElement();
        cmWrapper.style.direction = 'ltr';
        cmWrapper.style.textAlign = 'left';
        cmWrapper.setAttribute('dir', 'ltr');
        
        selectorsEditor.on('change', function(cm) {
            selectorsTextarea.value = cm.getValue();
        });
        
        setTimeout(function() {
            selectorsEditor.refresh();
        }, 100);
    }

    // Subdomains Editor
    const subdomainsTextarea = document.getElementById('subdomains-editor');
    const subdomainsContainer = document.getElementById('subdomains-editor-container');
    if (subdomainsTextarea && subdomainsContainer) {
        subdomainsContainer.style.direction = 'ltr';
        subdomainsContainer.style.textAlign = 'left';
        
        // Convert comma-separated to line-separated for better editor display
        const subdomainsValue = (subdomainsTextarea.value || '').split(',').map(s => s.trim()).filter(s => s).join('\n');
        subdomainsEditor = CodeMirror(subdomainsContainer, {
            value: subdomainsValue,
            mode: 'text/plain',
            theme: 'default',
            lineNumbers: false,
            lineWrapping: true,
            direction: 'ltr',
            indentUnit: 2,
            rtlMoveVisually: false,
        });
        subdomainsEditor.setSize('100%', '80px');
        
        const cmWrapper = subdomainsEditor.getWrapperElement();
        cmWrapper.style.direction = 'ltr';
        cmWrapper.style.textAlign = 'left';
        cmWrapper.setAttribute('dir', 'ltr');
        
        subdomainsEditor.on('change', function(cm) {
            // Convert newlines back to comma-separated for form submission
            const value = cm.getValue().split('\n').map(s => s.trim()).filter(s => s).join(',');
            subdomainsTextarea.value = value;
        });
        // Set initial value
        subdomainsTextarea.value = subdomainsValue.split('\n').map(s => s.trim()).filter(s => s).join(',');
        
        setTimeout(function() {
            subdomainsEditor.refresh();
        }, 100);
    }
}

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
    initCodeEditors();
    initSelect2();
    // Wait a bit for select2 to initialize, then toggle fields
    setTimeout(function() {
        if (window.toggleSelectorFields) {
            window.toggleSelectorFields();
        }
    }, 100);
    
    // Sync CodeMirror values before form submission
    const form = document.getElementById('validate-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (contentEditor) {
                document.getElementById('content-editor').value = contentEditor.getValue();
            }
            if (patternsEditor) {
                document.getElementById('patterns-editor').value = patternsEditor.getValue();
            }
            if (selectorsEditor) {
                document.getElementById('selectors-editor').value = selectorsEditor.getValue();
            }
            if (subdomainsEditor) {
                // Convert newlines to comma-separated for subdomains
                const value = subdomainsEditor.getValue().split('\n').map(s => s.trim()).filter(s => s).join(',');
                document.getElementById('subdomains-editor').value = value;
            }
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeForm);
} else {
    initializeForm();
}
</script>
@endsection

