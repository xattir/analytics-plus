@extends('layouts.admin')
@section('content')
<style>
    .ad-form-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    
    .ad-form-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(123, 96, 251, 0.1);
        backdrop-filter: blur(10px);
        margin-bottom: 24px;
    }
    
    .ad-form-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 24px;
        border-bottom: 2px solid rgba(123, 96, 251, 0.1);
    }
    
    .ad-form-header-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 24px rgba(123, 96, 251, 0.3);
    }
    
    .ad-form-header-icon i {
        font-size: 36px;
        color: white;
    }
    
    .ad-form-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 8px 0;
        background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .form-section {
        margin-bottom: 32px;
    }
    
    .form-section-title {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(123, 96, 251, 0.1);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-section-title i {
        color: #7b60fb;
        font-size: 20px;
    }
    
    .form-group-modern {
        margin-bottom: 24px;
    }
    
    .form-label-modern {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }
    
    .form-label-modern .required {
        color: #ef4444;
        margin-right: 4px;
    }
    
    .form-control-modern {
        width: 100%;
        padding: 12px 16px;
        font-size: 14px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: #fff;
    }
    
    .form-control-modern:focus {
        outline: none;
        border-color: #7b60fb;
        box-shadow: 0 0 0 4px rgba(123, 96, 251, 0.1);
    }
    
    .form-text-modern {
        display: block;
        margin-top: 6px;
        font-size: 13px;
        color: #6b7280;
    }
    
    .btn-submit-modern {
        padding: 16px 48px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(123, 96, 251, 0.3);
    }
    
    .btn-submit-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(123, 96, 251, 0.4);
        color: white;
    }
    
    .select2-container--default .select2-selection--multiple {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 4px;
        min-height: 48px;
    }
    
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #7b60fb;
    }
    
    @media (max-width: 768px) {
        .ad-form-container {
            padding: 20px 16px;
        }
        
        .ad-form-card {
            padding: 24px 20px;
        }
        
        .ad-form-header h1 {
            font-size: 24px;
        }
    }
</style>

<div class="ad-form-container">
    <form id="validate-form" method="POST" action="@if(isset($advertisement)){{route('admin.advertisements.update',['advertisement'=>$advertisement])}}@else{{route('admin.advertisements.store')}}@endif">
        @csrf
        @if(isset($advertisement))
            @method('PUT')
        @endif
        <div class="ad-form-card">
            <div class="ad-form-header">
                <div class="ad-form-header-icon">
                    <i class="fal fa-bullhorn"></i>
                </div>
                <h1>@if(isset($advertisement)) تعديل الإعلان @else إضافة إعلان جديد @endif</h1>
            </div>
            
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-info-circle"></i>
                            <span>معلومات الإعلان الأساسية</span>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <span class="required">*</span> اسم الإعلان
                                    </label>
                                    <input type="text" name="name" required maxlength="255" class="form-control-modern" value="{{old('name', isset($advertisement) ? $advertisement->name : '')}}" placeholder="أدخل اسم الإعلان">
                                </div>
                            </div>
                            
                            <div class="col-12 col-lg-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <span class="required">*</span> نوع الإعلان
                                    </label>
                                    <select class="form-control-modern" name="type" id="ad_type" required onchange="toggleSelectorFields()">
                                        <option value="in_content" @if(old('type', isset($advertisement) ? $advertisement->type : '') == 'in_content') selected @endif>In Content</option>
                                        <option value="pop_from_bottom" @if(old('type', isset($advertisement) ? $advertisement->type : '') == 'pop_from_bottom') selected @endif>Pop from Bottom</option>
                                        <option value="pop_from_top" @if(old('type', isset($advertisement) ? $advertisement->type : '') == 'pop_from_top') selected @endif>Pop from Top</option>
                                        <option value="Interstitial" @if(old('type', isset($advertisement) ? $advertisement->type : '') == 'Interstitial') selected @endif>Interstitial</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <span class="required">*</span> المحتوى
                            </label>
                            <textarea name="content" id="content" required class="form-control-modern" rows="10" style="direction: ltr; text-align: left; font-family: 'Courier New', monospace; resize: vertical;">{{old('content', isset($advertisement) ? $advertisement->content : '')}}</textarea>
                            <span class="form-text-modern">لصورة: أدخل رابط الصورة فقط. لـ HTML/Script: أدخل الكود. لـ نص: أدخل النص.</span>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">رابط الإعلان (اختياري)</label>
                                    <input type="url" name="url" id="url-editor" maxlength="2048" class="form-control-modern" value="{{old('url', isset($advertisement) ? $advertisement->url : '')}}" placeholder="https://example.com" style="direction: ltr; text-align: left;">
                                </div>
                            </div>
                            
                            <div class="col-12 col-lg-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">فتح الرابط في تبويب جديد</label>
                                    <select class="form-control-modern" name="open_in_new_tab">
                                        @php
                                            $openInNewTab = old('open_in_new_tab', isset($advertisement) ? ($advertisement->open_in_new_tab ? '1' : '0') : '1');
                                        @endphp
                                        <option value="1" @if($openInNewTab == '1') selected @endif>نعم (تبويب جديد)</option>
                                        <option value="0" @if($openInNewTab == '0') selected @endif>لا (نفس الصفحة)</option>
                                    </select>
                                    <span class="form-text-modern">اختر إذا كان الرابط يفتح في تبويب جديد أم في نفس الصفحة</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">الأولوية</label>
                                    <input type="number" name="priority" min="0" class="form-control-modern" value="{{old('priority', isset($advertisement) ? $advertisement->priority : 0)}}" placeholder="0">
                                    <span class="form-text-modern">كلما زاد الرقم، زادت الأولوية</span>
                                </div>
                            </div>
                            
                            <div class="col-12 col-lg-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">الحالة</label>
                                    <select class="form-control-modern" name="is_active">
                                        @php
                                            $isActive = old('is_active', isset($advertisement) ? ($advertisement->is_active ? '1' : '0') : '1');
                                        @endphp
                                        <option value="1" @if($isActive == '1') selected @endif>نشط</option>
                                        <option value="0" @if($isActive == '0') selected @endif>غير نشط</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-lg-4">
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-cog"></i>
                            <span>الإعدادات المتقدمة</span>
                        </div>
                        
                        <div class="form-group-modern">
                            <label class="form-label-modern">المواقع</label>
                            <select class="form-control-modern select2-select" name="site_ids[]" multiple size="1" style="height:30px;opacity: 0;">
                                @foreach($sites as $site)
                                <option value="{{$site->id}}" @if((old('site_ids') && in_array($site->id, old('site_ids'))) || (isset($advertisement) && $advertisement->sites->contains('id', $site->id))) selected @endif>{{$site->title}} ({{$site->domain}})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group-modern">
                            <label class="form-label-modern">الدول (اترك فارغاً للكل)</label>
                            <select class="form-control-modern select2-select" name="country_codes[]" multiple size="1" style="height:30px;opacity: 0;">
                                @foreach($countries as $country)
                                <option value="{{$country['iso2']}}" @if((old('country_codes') && in_array($country['iso2'], old('country_codes'))) || (isset($advertisement) && $advertisement->countries->contains('country_code', $country['iso2']))) selected @endif>{{$country['name_ar'] ?? $country['name']}} ({{$country['iso2']}})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group-modern">
                            <label class="form-label-modern">الأجهزة (اترك فارغاً للكل)</label>
                            <select class="form-control-modern select2-select" name="device_types[]" multiple size="1" style="height:30px;opacity: 0;">
                                <option value="desktop" @if((old('device_types') && in_array('desktop', old('device_types'))) || (isset($advertisement) && $advertisement->devices->contains('device_type', 'desktop'))) selected @endif>كمبيوتر</option>
                                <option value="mobile" @if((old('device_types') && in_array('mobile', old('device_types'))) || (isset($advertisement) && $advertisement->devices->contains('device_type', 'mobile'))) selected @endif>موبايل</option>
                                <option value="tablet" @if((old('device_types') && in_array('tablet', old('device_types'))) || (isset($advertisement) && $advertisement->devices->contains('device_type', 'tablet'))) selected @endif>تابلت</option>
                            </select>
                        </div>
                        
                        <div class="form-group-modern">
                            <label class="form-label-modern">أنماط URL (اترك فارغاً للكل)</label>
                            <select class="form-control-modern select2-select url-patterns-select" name="url_pattern_ids[]" multiple size="1" style="height:30px;opacity: 0;" data-placeholder="اترك فارغاً للكل">
                                <option></option>
                                @foreach($urlPatterns as $pattern)
                                <option value="{{$pattern->id}}" @if((old('url_pattern_ids') && in_array($pattern->id, old('url_pattern_ids'))) || (isset($advertisement) && $advertisement->urlPatterns->contains('id', $pattern->id))) selected @endif>{{$pattern->site->title}}: {{$pattern->pattern}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group-modern">
                            <label class="form-label-modern">Custom URL Patterns</label>
                            <textarea name="custom_patterns" id="patterns-editor" style="display:none;">@php
                                $patternsValue = '';
                                if (old('custom_patterns')) {
                                    $patternsValue = is_array(old('custom_patterns')) ? implode("\n", old('custom_patterns')) : old('custom_patterns');
                                } elseif (isset($advertisement) && method_exists($advertisement, 'getCustomPatterns')) {
                                    $patterns = $advertisement->getCustomPatterns();
                                    $patternsValue = is_array($patterns) ? implode("\n", $patterns) : (string)$patterns;
                                } elseif (isset($currentCustomPatterns)) {
                                    $patternsValue = is_array($currentCustomPatterns) ? implode("\n", $currentCustomPatterns) : (string)$currentCustomPatterns;
                                }
                            @endphp{{ $patternsValue }}</textarea>
                            <div id="patterns-editor-container" style="direction: ltr; text-align: left; border: 2px solid #e5e7eb; border-radius: 12px; overflow: hidden;"></div>
                            <span class="form-text-modern">أدخل patterns مخصصة (مثل /products/* أو /blog/*). يمكنك استخدام * كـ wildcard. سطر واحد لكل pattern.</span>
                        </div>
                        
                        <div class="form-group-modern">
                            <label class="form-label-modern">استثناء أنماط URL</label>
                            <select class="form-control-modern select2-select" name="excluded_pattern_ids[]" multiple size="1" style="height:30px;opacity: 0;">
                                @foreach($urlPatterns as $pattern)
                                <option value="{{$pattern->id}}" @if((old('excluded_pattern_ids') && in_array($pattern->id, old('excluded_pattern_ids'))) || (isset($advertisement) && $advertisement->excludedPatterns->contains('id', $pattern->id))) selected @endif>{{$pattern->site->title}}: {{$pattern->pattern}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group-modern" id="selector_fields">
                            <label class="form-label-modern">Selectors المحددة مسبقاً</label>
                            <select class="form-control-modern select2-select" name="predefined_selectors[]" id="predefined_selectors" multiple size="1" style="height:30px;opacity: 0;">
                                @foreach($predefinedSelectors as $tag => $selector)
                                @php
                                    $isSelected = false;
                                    if(old('predefined_selectors') && in_array($tag, old('predefined_selectors'))) {
                                        $isSelected = true;
                                    } elseif(isset($advertisement) && isset($currentPredefinedTags) && is_array($currentPredefinedTags) && in_array($tag, $currentPredefinedTags)) {
                                        $isSelected = true;
                                    }
                                @endphp
                                <option value="{{$tag}}" @if($isSelected) selected @endif>{{$tag}} ({{$selector}})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group-modern" id="custom_selector_field">
                            <label class="form-label-modern">Selectors مخصصة</label>
                            <textarea name="custom_selectors" id="selectors-editor" style="display:none;">@php
                                $selectorsValue = '';
                                if (old('custom_selectors')) {
                                    $selectorsValue = is_array(old('custom_selectors')) ? implode("\n", old('custom_selectors')) : old('custom_selectors');
                                } elseif (isset($advertisement) && isset($currentCustomSelectors) && is_array($currentCustomSelectors)) {
                                    $selectorsValue = implode("\n", $currentCustomSelectors);
                                }
                            @endphp{{ $selectorsValue }}</textarea>
                            <div id="selectors-editor-container" style="direction: ltr; text-align: left; border: 2px solid #e5e7eb; border-radius: 12px; overflow: hidden;"></div>
                            <span class="form-text-modern">سطر واحد لكل selector</span>
                        </div>
                        
                        <div class="form-group-modern">
                            <label class="form-label-modern">Subdomains (مفصولة بفواصل، اترك فارغاً للكل)</label>
                            <textarea name="subdomains" id="subdomains-editor" style="display:none;">{{old('subdomains', isset($advertisement) ? $advertisement->subdomains->whereNotNull('subdomain')->pluck('subdomain')->implode(',') : '')}}</textarea>
                            <div id="subdomains-editor-container" style="direction: ltr; text-align: left; border: 2px solid #e5e7eb; border-radius: 12px; overflow: hidden;"></div>
                        </div>
                        
                        <div class="form-group-modern" id="padding_fields" style="display: none;">
                            <label class="form-label-modern">Padding (بالـ px) - للـ Pop from Bottom/Top</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" name="padding_x" min="0" max="100" class="form-control-modern" value="{{old('padding_x', isset($advertisement) ? ($advertisement->padding_x ?? 20) : 20)}}" placeholder="20">
                                    <span class="form-text-modern">Padding X</span>
                                </div>
                                <div class="col-6">
                                    <input type="number" name="padding_y" min="0" max="100" class="form-control-modern" value="{{old('padding_y', isset($advertisement) ? ($advertisement->padding_y ?? 20) : 20)}}" placeholder="20">
                                    <span class="form-text-modern">Padding Y</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group-modern" id="interstitial_padding_field" style="display: none;">
                            <label class="form-label-modern">Padding (بالـ px) - للـ Interstitial</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" name="padding_x" id="interstitial_padding_x" min="0" max="100" class="form-control-modern" value="{{old('padding_x', isset($advertisement) ? ($advertisement->padding_x ?? 20) : 20)}}" placeholder="20">
                                    <span class="form-text-modern">Padding X</span>
                                </div>
                                <div class="col-6">
                                    <input type="number" name="padding_y" id="interstitial_padding_y" min="0" max="100" class="form-control-modern" value="{{old('padding_y', isset($advertisement) ? ($advertisement->padding_y ?? 20) : 20)}}" placeholder="20">
                                    <span class="form-text-modern">Padding Y</span>
                                </div>
                            </div>
                            <span class="form-text-modern">Padding للمحتوى داخل الصندوق</span>
                        </div>
                        
                        <div class="form-group-modern" id="interval_field" style="display: none;">
                            <label class="form-label-modern">Interval Period (بالثواني) - للـ Interstitial</label>
                            <input type="number" name="interval_period" id="interval_period_input" min="0" class="form-control-modern" value="{{old('interval_period', isset($advertisement) ? ($advertisement->interval_period ?? '') : '')}}" placeholder="3600">
                            <span class="form-text-modern">المدة بالثواني قبل إظهار الإعلان مرة أخرى (0 = إظهار دائماً، اترك فارغاً = إخفاء تلقائي بعد 10 ثوانٍ)</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center" style="margin-top: 40px; padding-top: 32px; border-top: 2px solid rgba(123, 96, 251, 0.1);">
                <button type="submit" class="btn-submit-modern" id="submitEvaluation">
                    <i class="fas fa-save"></i>
                    <span>حفظ الإعلان</span>
                </button>
            </div>
        </div>
    </form>
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
    border: none !important;
    border-radius: 12px;
    font-size: 14px;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', 'source-code-pro', monospace;
}

.CodeMirror-focused {
    border: none !important;
    box-shadow: 0 0 0 4px rgba(123, 96, 251, 0.1) !important;
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
// Initialize CodeMirror editors (only for patterns, selectors, and subdomains)
let patternsEditor, selectorsEditor, subdomainsEditor;

function initCodeEditors() {
    // Check if CodeMirror is loaded
    if (typeof CodeMirror === 'undefined') {
        setTimeout(initCodeEditors, 100);
        return;
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
        $('.select2-select').each(function() {
            const $select = $(this);
            const placeholder = $select.data('placeholder') || $select.attr('data-placeholder') || 'اختر...';
            const isMultiple = $select.prop('multiple');
            
            const select2Options = {
                dir: 'rtl',
                language: {
                    inputTooShort: function() { return ''; },
                    noResults: function() { return 'لا توجد نتائج'; },
                    searching: function() { return 'جاري البحث...'; }
                },
                width: '100%',
                allowClear: true
            };
            
            // إضافة placeholder للحقول المتعددة (multiple)
            if (isMultiple && placeholder) {
                select2Options.placeholder = placeholder;
                // التأكد من وجود option فارغة للـ placeholder
                if ($select.find('option[value=""]').length === 0) {
                    $select.prepend('<option value=""></option>');
                }
            } else if (!isMultiple && placeholder) {
                select2Options.placeholder = placeholder;
            }
            
            $select.select2(select2Options);
            
            // إزالة الخيار الفارغ بعد التهيئة للحقول المتعددة (إذا لم يكن هناك خيارات محددة)
            if (isMultiple && $select.find('option:selected').length === 0) {
                // الحقل فارغ - placeholder سيظهر
            }
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

