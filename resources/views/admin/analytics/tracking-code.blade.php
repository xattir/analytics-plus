@extends('layouts.admin', ['page_title' => 'كود التتبع'])

@section('content')
<style>
    .tracking-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    
    .tracking-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(123, 96, 251, 0.1);
        backdrop-filter: blur(10px);
        margin-bottom: 24px;
    }
    
    .tracking-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 24px;
        border-bottom: 2px solid rgba(123, 96, 251, 0.1);
    }
    
    .tracking-header-icon {
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
    
    .tracking-header-icon svg {
        width: 40px;
        height: 40px;
        color: white;
    }
    
    .tracking-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 8px 0;
        background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .tracking-header p {
        color: #6b7280;
        font-size: 16px;
        margin: 0;
    }
    
    .site-key-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 16px 24px;
        background: linear-gradient(135deg, rgba(123, 96, 251, 0.1) 0%, rgba(123, 96, 251, 0.05) 100%);
        border-radius: 12px;
        border: 1px solid rgba(123, 96, 251, 0.2);
        margin-bottom: 32px;
        width: 100%;
        justify-content: space-between;
    }
    
    .site-key-badge-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #1f2937;
        font-size: 15px;
    }
    
    .site-key-badge-label svg {
        width: 20px;
        height: 20px;
        color: #7b60fb;
    }
    
    .site-key-code {
        font-family: 'Courier New', monospace;
        font-size: 14px;
        color: #7b60fb;
        background: white;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid rgba(123, 96, 251, 0.2);
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .code-section {
        margin-bottom: 32px;
    }
    
    .code-label {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 16px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 12px;
    }
    
    .code-label svg {
        width: 20px;
        height: 20px;
        color: #7b60fb;
    }
    
    .code-textarea {
        width: 100%;
        padding: 20px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
        line-height: 1.6;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        background: #1f2937;
        color: #10b981;
        resize: vertical;
        min-height: 180px;
        transition: all 0.3s ease;
        direction: ltr;
        text-align: left;
    }
    
    .code-textarea:focus {
        outline: none;
        border-color: #7b60fb;
        box-shadow: 0 0 0 4px rgba(123, 96, 251, 0.1);
    }
    
    .code-textarea::selection {
        background: rgba(123, 96, 251, 0.3);
        color: #10b981;
    }
    
    .code-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        flex-wrap: wrap;
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
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .btn-modern svg {
        width: 18px;
        height: 18px;
    }
    
    .info-box {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 12px;
        padding: 20px;
        margin-top: 24px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    
    .info-box-icon {
        width: 24px;
        height: 24px;
        color: #3b82f6;
        flex-shrink: 0;
        margin-top: 2px;
    }
    
    .info-box-content {
        flex: 1;
    }
    
    .info-box-content strong {
        display: block;
        color: #1f2937;
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 15px;
    }
    
    .info-box-content p {
        color: #6b7280;
        font-size: 14px;
        margin: 0;
        line-height: 1.6;
    }
    
    .copy-success {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 16px 32px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
        z-index: 10000;
        display: none;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }
    
    @media (max-width: 768px) {
        .tracking-container {
            padding: 20px 16px;
        }
        
        .tracking-card {
            padding: 24px 20px;
        }
        
        .tracking-header h1 {
            font-size: 24px;
        }
        
        .site-key-badge {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .site-key-code {
            width: 100%;
            word-break: break-all;
        }
        
        .code-actions {
            flex-direction: column;
        }
        
        .btn-modern {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="tracking-container">
    <div class="tracking-card">
        <div class="tracking-header">
            <div class="tracking-header-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="16 18 22 12 16 6"></polyline>
                    <polyline points="8 6 2 12 8 18"></polyline>
                </svg>
            </div>
            <h1>كود التتبع</h1>
            <p>{{ $site->title ?? $site->domain }}</p>
        </div>
        
        <div class="site-key-badge">
            <div class="site-key-badge-label">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <span>مفتاح الموقع:</span>
            </div>
            <code class="site-key-code">{{ $site->site_key }}</code>
        </div>
        
        <div class="code-section">
            <label class="code-label">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="16 18 22 12 16 6"></polyline>
                    <polyline points="8 6 2 12 8 18"></polyline>
                </svg>
                <span>انسخ والصق هذا الكود في HTML لموقعك، قبل إغلاق وسم &lt;/head&gt;:</span>
            </label>
            <textarea class="code-textarea" id="trackingCode" readonly onclick="this.select()">{{ $trackingCode }}</textarea>
        </div>
        
        <div class="code-actions">
            <button class="btn-modern btn-primary-modern" onclick="copyToClipboard()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
                <span>نسخ إلى الحافظة</span>
            </button>
            <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.show', ['site' => $site->site_key]) : route('user.analytics.show', ['site' => $site->site_key]) }}" class="btn-modern btn-secondary-modern">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>عرض لوحة التحكم</span>
            </a>
        </div>
        
        <div class="info-box">
            <svg class="info-box-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <div class="info-box-content">
                <strong>ملاحظة مهمة:</strong>
                <p>تأكد من وضع كود التتبع في جميع صفحات موقعك قبل إغلاق وسم &lt;/head&gt;. هذا الكود سيتتبع زيارات المستخدمين وإحصائيات موقعك تلقائياً.</p>
            </div>
        </div>
    </div>
</div>

<div class="copy-success" id="copySuccess">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="20 6 9 17 4 12"></polyline>
    </svg>
    <span>تم نسخ كود التتبع إلى الحافظة!</span>
</div>

<script>
function copyToClipboard() {
    const textarea = document.getElementById('trackingCode');
    textarea.select();
    textarea.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        
        // Show success message
        const successMsg = document.getElementById('copySuccess');
        successMsg.style.display = 'flex';
        
        setTimeout(() => {
            successMsg.style.display = 'none';
        }, 3000);
    } catch (err) {
        // Fallback for modern browsers
        navigator.clipboard.writeText(textarea.value).then(() => {
            const successMsg = document.getElementById('copySuccess');
            successMsg.style.display = 'flex';
            
            setTimeout(() => {
                successMsg.style.display = 'none';
            }, 3000);
        }).catch(() => {
            alert('فشل نسخ الكود. يرجى نسخه يدوياً.');
        });
    }
}
</script>
@endsection
