@extends('layouts.admin', ['page_title' => 'تعديل نمط URL - ' . $site->title])

@section('content')
<style>
    .pattern-form-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    
    .pattern-form-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(123, 96, 251, 0.1);
        backdrop-filter: blur(10px);
    }
    
    .pattern-form-header {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .pattern-form-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
        background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .pattern-form-header p {
        color: #6b7280;
        font-size: 16px;
        margin: 0;
    }
    
    .form-group-modern {
        margin-bottom: 28px;
    }
    
    .form-label-modern {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 10px;
    }
    
    .form-label-modern .required {
        color: #ef4444;
        font-size: 18px;
    }
    
    .form-input-modern {
        width: 100%;
        padding: 14px 18px;
        font-size: 15px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #1f2937;
    }
    
    .form-input-modern:focus {
        outline: none;
        border-color: #7b60fb;
        box-shadow: 0 0 0 4px rgba(123, 96, 251, 0.1);
    }
    
    .form-select-modern {
        width: 100%;
        padding: 14px 18px;
        font-size: 15px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #1f2937;
    }
    
    .form-select-modern:focus {
        outline: none;
        border-color: #7b60fb;
        box-shadow: 0 0 0 4px rgba(123, 96, 251, 0.1);
    }
    
    .form-help-text {
        font-size: 13px;
        color: #6b7280;
        margin-top: 8px;
    }
    
    .pattern-examples {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        margin-top: 12px;
    }
    
    .pattern-examples h4 {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 12px;
    }
    
    .pattern-examples ul {
        margin: 0;
        padding-right: 20px;
        color: #6b7280;
        font-size: 13px;
    }
    
    .pattern-examples li {
        margin-bottom: 8px;
        font-family: 'Courier New', monospace;
    }
    
    .pattern-examples .wildcard {
        color: #7b60fb;
        font-weight: 600;
    }
    
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 40px;
        padding-top: 32px;
        border-top: 1px solid #e5e7eb;
    }
    
    .btn-modern {
        padding: 14px 32px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-modern-primary {
        background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(123, 96, 251, 0.3);
    }
    
    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(123, 96, 251, 0.4);
        color: white;
    }
    
    .btn-modern-secondary {
        background: #f3f4f6;
        color: #374151;
    }
    
    .btn-modern-secondary:hover {
        background: #e5e7eb;
        color: #1f2937;
    }
    
    @media (max-width: 768px) {
        .pattern-form-container {
            padding: 20px 16px;
        }
        
        .pattern-form-card {
            padding: 28px 20px;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn-modern {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="pattern-form-container">
    <div class="pattern-form-card">
        <div class="pattern-form-header">
            <h1>تعديل نمط URL</h1>
            <p>{{ $site->title }} ({{ $site->domain }})</p>
        </div>
        
        <form method="POST" action="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns.update' : 'user.analytics.patterns.update', [$site, $patternModel]) }}">
            @csrf
            @method('PUT')
            
            <div class="form-group-modern">
                <label for="domain" class="form-label-modern">
                    النطاق <span class="required">*</span>
                </label>
                @if($domains->count() > 0)
                    <select name="domain" id="domain" class="form-select-modern" required>
                        <option value="">اختر أو اكتب نطاق</option>
                        @foreach($domains as $domain)
                            <option value="{{ $domain }}" {{ old('domain', $patternModel->domain) === $domain ? 'selected' : '' }}>
                                {{ $domain }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input 
                        type="text" 
                        class="form-input-modern" 
                        id="domain" 
                        name="domain" 
                        value="{{ old('domain', $patternModel->domain) }}" 
                        required 
                        placeholder="example.com"
                    >
                @endif
                <div class="form-help-text">
                    النطاق الكامل (مثال: subdomain.example.com أو example.com)
                </div>
            </div>
            
            <div class="form-group-modern">
                <label for="pattern" class="form-label-modern">
                    النمط <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    class="form-input-modern" 
                    id="pattern" 
                    name="pattern" 
                    value="{{ old('pattern', $patternModel->pattern) }}" 
                    required 
                    placeholder="/article/*"
                >
                <div class="form-help-text">
                    نمط URL باستخدام * كبديل للقيم المتغيرة (يجب أن يبدأ بـ /)
                </div>
                <div class="pattern-examples">
                    <h4>أمثلة على الأنماط:</h4>
                    <ul>
                        <li>/article/<span class="wildcard">*</span> - لجميع المقالات</li>
                        <li>/article/<span class="wildcard">*</span>/download/<span class="wildcard">*</span> - لصفحات التحميل</li>
                        <li>/category/<span class="wildcard">*</span> - لجميع الفئات</li>
                        <li>/ - للصفحة الرئيسية</li>
                    </ul>
                </div>
            </div>
            
            @if($errors->any())
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
                    <div style="color: #dc2626; font-weight: 600; margin-bottom: 8px;">يرجى تصحيح الأخطاء التالية:</div>
                    <ul style="margin: 0; padding-right: 20px; color: #991b1b;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="form-actions">
                <button type="submit" class="btn-modern btn-modern-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span>تحديث النمط</span>
                </button>
                <a href="{{ route(request()->routeIs('admin.*') ? 'admin.analytics.patterns' : 'user.analytics.patterns', $site) }}" class="btn-modern btn-modern-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    <span>إلغاء</span>
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

