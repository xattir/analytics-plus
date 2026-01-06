@extends('layouts.admin', ['page_title' => 'إضافة موقع تحليلات'])

@section('content')
<style>
    .create-site-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    
    .create-site-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(123, 96, 251, 0.1);
        backdrop-filter: blur(10px);
    }
    
    .create-site-header {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .create-site-header-icon {
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
    
    .create-site-header-icon svg {
        width: 40px;
        height: 40px;
        color: white;
    }
    
    .create-site-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
        background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .create-site-header p {
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
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .form-label-modern .required {
        color: #ef4444;
        font-size: 18px;
    }
    
    .form-label-modern svg {
        width: 18px;
        height: 18px;
        color: #7b60fb;
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
        background: #ffffff;
    }
    
    .form-input-modern::placeholder {
        color: #9ca3af;
    }
    
    .form-help-text {
        font-size: 13px;
        color: #6b7280;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .form-help-text svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
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
    
    .btn-modern svg {
        width: 18px;
        height: 18px;
    }
    
    @media (max-width: 768px) {
        .create-site-container {
            padding: 20px 16px;
        }
        
        .create-site-card {
            padding: 28px 20px;
        }
        
        .create-site-header h1 {
            font-size: 24px;
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

<div class="create-site-container">
    <div class="create-site-card">
        <div class="create-site-header">
            <div class="create-site-header-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20V10M12 20L16 16M12 20L8 16M18 10H6C4.89543 10 4 9.10457 4 8V4C4 2.89543 4.89543 2 6 2H18C19.1046 2 20 2.89543 20 4V8C20 9.10457 19.1046 10 18 10Z"></path>
                </svg>
            </div>
            <h1>إضافة موقع تحليلات جديد</h1>
            <p>أضف موقعاً جديداً لتتبع إحصائياته وزياراته</p>
        </div>
        
        <form method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.analytics.store') : route('user.analytics.store') }}">
            @csrf
            
            <div class="form-group-modern">
                <label for="domain" class="form-label-modern">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                    </svg>
                    <span>النطاق</span>
                    <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    class="form-input-modern" 
                    id="domain" 
                    name="domain" 
                    value="{{ old('domain') }}" 
                    required 
                    placeholder="example.com"
                >
                <div class="form-help-text">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span>أدخل اسم النطاق بدون http:// أو https:// (مثال: example.com)</span>
                </div>
            </div>
            
            <div class="form-group-modern">
                <label for="title" class="form-label-modern">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <span>العنوان (اختياري)</span>
                </label>
                <input 
                    type="text" 
                    class="form-input-modern" 
                    id="title" 
                    name="title" 
                    value="{{ old('title') }}" 
                    placeholder="عنوان مخصص للموقع"
                >
                <div class="form-help-text">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span>عنوان مخصص للموقع (سيتم استخدام النطاق كعنوان افتراضي إذا لم يتم تحديده)</span>
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
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span>إنشاء الموقع</span>
                </button>
                <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.index') : route('user.analytics.index') }}" class="btn-modern btn-modern-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
