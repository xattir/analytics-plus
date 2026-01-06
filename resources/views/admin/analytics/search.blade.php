@extends('layouts.admin', ['page_title' => 'بحث في التحليلات'])

@section('content')
<style>
    .search-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    
    .search-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(123, 96, 251, 0.1);
        backdrop-filter: blur(10px);
    }
    
    .search-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 24px;
        border-bottom: 2px solid rgba(123, 96, 251, 0.1);
    }
    
    .search-header-icon {
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
    
    .search-header-icon svg {
        width: 40px;
        height: 40px;
        color: white;
    }
    
    .search-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 8px 0;
        background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .search-header p {
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
    
    .match-type-group {
        display: flex;
        gap: 12px;
        margin-top: 12px;
        flex-wrap: wrap;
    }
    
    .match-type-option {
        flex: 1;
        min-width: 150px;
    }
    
    .match-type-radio {
        display: none;
    }
    
    .match-type-label {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 16px 20px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 500;
        color: #374151;
    }
    
    .match-type-label:hover {
        border-color: #7b60fb;
        background: rgba(123, 96, 251, 0.03);
    }
    
    .match-type-radio:checked + .match-type-label {
        border-color: #7b60fb;
        background: linear-gradient(135deg, rgba(123, 96, 251, 0.1) 0%, rgba(123, 96, 251, 0.05) 100%);
        color: #7b60fb;
        box-shadow: 0 4px 12px rgba(123, 96, 251, 0.2);
    }
    
    .match-type-label svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }
    
    .date-range-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-top: 12px;
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
    
    .btn-primary-modern {
        background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(123, 96, 251, 0.3);
    }
    
    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(123, 96, 251, 0.4);
        color: white;
    }
    
    .btn-secondary-modern {
        background: #f3f4f6;
        color: #374151;
    }
    
    .btn-secondary-modern:hover {
        background: #e5e7eb;
        color: #1f2937;
    }
    
    .btn-modern svg {
        width: 18px;
        height: 18px;
    }
    
    @media (max-width: 768px) {
        .search-container {
            padding: 20px 16px;
        }
        
        .search-card {
            padding: 24px 20px;
        }
        
        .search-header h1 {
            font-size: 24px;
        }
        
        .match-type-group {
            flex-direction: column;
        }
        
        .match-type-option {
            min-width: 100%;
        }
        
        .date-range-group {
            grid-template-columns: 1fr;
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

<div class="search-container">
    <div class="search-card">
        <div class="search-header">
            <div class="search-header-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
            </div>
            <h1>بحث في التحليلات</h1>
            <p>{{ $site->title ?? $site->domain }}</p>
        </div>
        
        <form method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.analytics.search-results', ['site' => $site->site_key]) : route('user.analytics.search-results', ['site' => $site->site_key]) }}">
            @csrf
            
            <div class="form-group-modern">
                <label for="query" class="form-label-modern">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <span>البحث</span>
                    <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    class="form-input-modern" 
                    id="query" 
                    name="query" 
                    value="{{ old('query') }}" 
                    required 
                    placeholder="https://example.com/path أو عنوان IP"
                >
                <div class="form-help-text">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span>أدخل URL كامل أو مسار أو عنوان IP للبحث</span>
                </div>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                    <span>نوع المطابقة</span>
                    <span class="required">*</span>
                </label>
                <div class="match-type-group">
                    <div class="match-type-option">
                        <input type="radio" name="match_type" id="match_prefix" value="prefix" class="match-type-radio" {{ old('match_type', 'prefix') == 'prefix' ? 'checked' : '' }} required>
                        <label for="match_prefix" class="match-type-label">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <span>Prefix Match</span>
                        </label>
                    </div>
                    <div class="match-type-option">
                        <input type="radio" name="match_type" id="match_exact" value="exact" class="match-type-radio" {{ old('match_type') == 'exact' ? 'checked' : '' }} required>
                        <label for="match_exact" class="match-type-label">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Exact Match</span>
                        </label>
                    </div>
                    <div class="match-type-option">
                        <input type="radio" name="match_type" id="match_ip" value="ip" class="match-type-radio" {{ old('match_type') == 'ip' ? 'checked' : '' }} required>
                        <label for="match_ip" class="match-type-label">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                                <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                                <line x1="6" y1="6" x2="6.01" y2="6"></line>
                                <line x1="6" y1="18" x2="6.01" y2="18"></line>
                            </svg>
                            <span>بحث بالـ IP</span>
                        </label>
                    </div>
                </div>
                <div class="form-help-text">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span>Prefix: يبحث عن جميع المسارات التي تبدأ بهذا المسار | Exact: يبحث عن المسار المطابق تماماً | IP: يبحث عن جميع الزيارات من عنوان IP محدد</span>
                </div>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>نطاق التاريخ (اختياري)</span>
                </label>
                <div class="date-range-group">
                    <div>
                        <label for="date_from" style="display: block; font-size: 13px; color: #6b7280; margin-bottom: 6px;">من تاريخ</label>
                        <input 
                            type="date" 
                            class="form-input-modern" 
                            id="date_from" 
                            name="date_from" 
                            value="{{ old('date_from', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d')) }}"
                        >
                    </div>
                    <div>
                        <label for="date_to" style="display: block; font-size: 13px; color: #6b7280; margin-bottom: 6px;">إلى تاريخ</label>
                        <input 
                            type="date" 
                            class="form-input-modern" 
                            id="date_to" 
                            name="date_to" 
                            value="{{ old('date_to', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                        >
                    </div>
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
                <button type="submit" class="btn-modern btn-primary-modern">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <span>بحث</span>
                </button>
                <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.show', ['site' => $site->site_key]) : route('user.analytics.show', ['site' => $site->site_key]) }}" class="btn-modern btn-secondary-modern">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>العودة</span>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

