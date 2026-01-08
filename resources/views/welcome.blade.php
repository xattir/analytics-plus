<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام التحليلات المتقدم - Analytics Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --accent: #ec4899;
            --text: #1f2937;
            --text-light: #6b7280;
            --bg: #ffffff;
            --bg-light: #f9fafb;
            --border: #e5e7eb;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Hero Section - 100dvh */
        .hero-section {
            height: 100dvh;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255,255,255,0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            max-width: 900px;
            padding: 0 20px;
        }
        
        .hero-title {
            font-size: clamp(2.5rem, 8vw, 4.5rem);
            font-weight: 800;
            margin-bottom: 24px;
            line-height: 1.1;
            letter-spacing: -0.02em;
            text-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        
        .hero-subtitle {
            font-size: clamp(1.1rem, 3vw, 1.5rem);
            margin-bottom: 40px;
            opacity: 0.95;
            font-weight: 400;
            line-height: 1.6;
        }
        
        .hero-cta {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 32px;
        }
        
        .btn {
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: white;
            color: var(--primary);
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 30px rgba(0,0,0,0.3);
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.15);
            color: white;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.3);
        }
        
        .btn-secondary:hover {
            background: rgba(255,255,255,0.25);
            border-color: rgba(255,255,255,0.5);
        }
        
        /* Auth Modal */
        .auth-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(8px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-modal.active {
            display: flex;
        }
        
        .auth-container {
            background: white;
            border-radius: 24px;
            max-width: 480px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideUp 0.3s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .auth-header {
            padding: 32px 32px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .auth-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
        }
        
        .auth-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-light);
            cursor: pointer;
            padding: 4px;
            line-height: 1;
        }
        
        .auth-close:hover {
            color: var(--text);
        }
        
        .auth-tabs {
            display: flex;
            border-bottom: 2px solid var(--border);
        }
        
        .auth-tab {
            flex: 1;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-light);
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
            background: none;
            border-top: none;
            border-left: none;
            border-right: none;
        }
        
        .auth-tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        
        .auth-form {
            display: none;
            padding: 32px;
        }
        
        .auth-form.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text);
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s ease;
            background: var(--bg);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
        
        .form-control.is-invalid {
            border-color: #ef4444;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 6px;
        }
        
        .btn-auth {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 8px;
        }
        
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
        }
        
        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        
        .form-check input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .form-check label {
            cursor: pointer;
            font-size: 0.9rem;
            color: var(--text);
        }
        
        .auth-footer {
            padding: 24px 32px;
            border-top: 1px solid var(--border);
            text-align: center;
        }
        
        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .hero-cta {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .auth-container {
                max-height: 95vh;
            }
            
            .auth-header,
            .auth-form {
                padding: 24px 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section - 100dvh -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">
                <i class="fas fa-chart-line" style="margin-left: 12px;"></i>
                نظام التحليلات المتقدم
            </h1>
            <p class="hero-subtitle">
                تتبع زوار موقعك وتحليل سلوكهم بسهولة وأمان مع لوحة تحكم قوية وسهلة الاستخدام
            </p>
            <div class="hero-cta">
                <button class="btn btn-primary" onclick="openAuth('login')">
                    <i class="fas fa-sign-in-alt"></i>
                    تسجيل الدخول
                </button>
                <button class="btn btn-secondary" onclick="openAuth('register')">
                    <i class="fas fa-user-plus"></i>
                    إنشاء حساب جديد
                </button>
            </div>
        </div>
    </section>

    <!-- Auth Modal -->
    <div class="auth-modal" id="authModal" onclick="closeAuthOnBackdrop(event)">
        <div class="auth-container" onclick="event.stopPropagation()">
            <div class="auth-header">
                <h2 class="auth-title">الوصول إلى النظام</h2>
                <button class="auth-close" onclick="closeAuth()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="auth-tabs">
                <button class="auth-tab active" onclick="switchTab('login')">
                    <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                </button>
                <button class="auth-tab" onclick="switchTab('register')">
                    <i class="fas fa-user-plus"></i> إنشاء حساب
                </button>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="auth-form active" id="login-form">
                @csrf
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" required autofocus placeholder="example@email.com">
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">كلمة المرور</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                           required placeholder="••••••••">
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">تذكرني</label>
                </div>
                <button type="submit" class="btn-auth">
                    <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                </button>
                @if (Route::has('password.request'))
                <div class="auth-footer">
                    <a href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
                </div>
                @endif
            </form>

            <!-- Register Form -->
            <form method="POST" action="{{ route('register') }}" class="auth-form" id="register-form">
                @csrf
                <input type="hidden" name="recaptcha" id="recaptcha">
                <div class="form-group">
                    <label class="form-label">الاسم</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" required placeholder="اسمك الكامل">
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" required placeholder="example@email.com">
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">كلمة المرور</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                           required minlength="6" placeholder="••••••••">
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" class="form-control" 
                           required minlength="6" placeholder="••••••••">
                </div>
                <button type="submit" class="btn-auth">
                    <i class="fas fa-user-plus"></i> إنشاء حساب
                </button>
            </form>
        </div>
    </div>

    <script>
        function openAuth(tab) {
            document.getElementById('authModal').classList.add('active');
            if (tab) {
                switchTab(tab);
            }
        }
        
        function closeAuth() {
            document.getElementById('authModal').classList.remove('active');
        }
        
        function closeAuthOnBackdrop(event) {
            if (event.target === event.currentTarget) {
                closeAuth();
            }
        }
        
        function switchTab(tab) {
            // Update tabs
            document.querySelectorAll('.auth-tab').forEach(t => {
                t.classList.remove('active');
                if (t.textContent.includes(tab === 'login' ? 'تسجيل الدخول' : 'إنشاء حساب')) {
                    t.classList.add('active');
                }
            });
            
            // Update forms
            document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
            document.getElementById(tab + '-form').classList.add('active');
        }
        
        // Handle old values for active tab
        @if(old('name') || (old('email') && !old('password')))
            openAuth('register');
        @elseif(old('email'))
            openAuth('login');
        @endif
        
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAuth();
            }
        });
    </script>
    
    @if(config('services.google.recaptcha_key'))
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.google.recaptcha_key') }}"></script>
    <script>
        grecaptcha.ready(function() {
            document.getElementById('register-form').addEventListener("submit", function(event) {
                event.preventDefault();
                grecaptcha.execute('{{ config('services.google.recaptcha_key') }}', { action: 'register' }).then(function(token) {
                    document.getElementById("recaptcha").value = token;
                    document.getElementById('register-form').submit();
                });
            }, false);
        });
    </script>
    @endif
</body>
</html>
