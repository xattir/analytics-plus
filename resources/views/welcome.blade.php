<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام التحليلات المتقدم - Analytics Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .hero-section {
            padding: 80px 0;
            color: white;
            text-align: center;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 40px;
            opacity: 0.95;
        }
        
        .features-section {
            padding: 60px 0;
            background: white;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 20px;
        }
        
        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }
        
        .feature-description {
            color: #666;
            line-height: 1.8;
        }
        
        .auth-section {
            padding: 80px 0;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        
        .auth-container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .auth-tabs {
            display: flex;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .auth-tab {
            flex: 1;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            color: #999;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }
        
        .auth-tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }
        
        .auth-form {
            display: none;
        }
        
        .auth-form.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            display: block;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .social-login {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .social-btn {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .social-btn:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        
        .social-btn.google {
            color: #db4437;
        }
        
        .social-btn.facebook {
            color: #4267B2;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.2rem;
            }
            
            .auth-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">
                <i class="fas fa-chart-line"></i> نظام التحليلات المتقدم
            </h1>
            <p class="hero-subtitle">
                تتبع زوار موقعك وتحليل سلوكهم بسهولة وأمان
            </p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="feature-title">تتبع الزوار</h3>
                        <p class="feature-description">
                            تتبع عدد الزوار الفريدين والمستخدمين النشطين في الوقت الفعلي مع إحصائيات مفصلة عن سلوكهم
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <h3 class="feature-title">مصادر الزيارات</h3>
                        <p class="feature-description">
                            تعرف على مصادر زياراتك (محركات البحث، روابط مباشرة، مواقع أخرى) مع تحليل شامل
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3 class="feature-title">التحليل الجغرافي</h3>
                        <p class="feature-description">
                            اكتشف من أين يأتي زوارك مع خرائط تفاعلية وإحصائيات مفصلة حسب البلد
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="feature-title">الأجهزة والمتصفحات</h3>
                        <p class="feature-description">
                            تحليل شامل للأجهزة المستخدمة (موبايل، تابلت، ديسكتوب) والمتصفحات وأنظمة التشغيل
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="feature-title">البحث المتقدم</h3>
                        <p class="feature-description">
                            ابحث في بياناتك بطرق متعددة (URL، IP، كود الدولة) مع فلاتر متقدمة للنتائج
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">الأمان والخصوصية</h3>
                        <p class="feature-description">
                            بياناتك محمية ومشفرة مع إمكانية مشاركة الوصول مع فريقك بشكل آمن
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Auth Section -->
    <section class="auth-section">
        <div class="container">
            <div class="auth-container">
                <div class="auth-tabs">
                    <div class="auth-tab active" onclick="switchTab('login')">
                        <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                    </div>
                    <div class="auth-tab" onclick="switchTab('register')">
                        <i class="fas fa-user-plus"></i> إنشاء حساب
                    </div>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="auth-form active" id="login-form">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">كلمة المرور</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            تذكرني
                        </label>
                    </div>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                    </button>
                    
                    @if(config('services.google.client_id') || config('services.facebook.client_id'))
                    <div class="social-login">
                        @if(config('services.google.client_id'))
                        <a href="/login/google/redirect" class="social-btn google">
                            <i class="fab fa-google"></i> تسجيل الدخول عبر Google
                        </a>
                        @endif
                        @if(config('services.facebook.client_id'))
                        <a href="/login/facebook/redirect" class="social-btn facebook">
                            <i class="fab fa-facebook-f"></i> تسجيل الدخول عبر Facebook
                        </a>
                        @endif
                    </div>
                    @endif
                    
                    @if (Route::has('password.request'))
                    <div style="text-align: center; margin-top: 15px;">
                        <a href="{{ route('password.request') }}" style="color: #667eea; text-decoration: none;">
                            نسيت كلمة المرور؟
                        </a>
                    </div>
                    @endif
                </form>

                <!-- Register Form -->
                <form method="POST" action="{{ route('register') }}" class="auth-form" id="register-form">
                    @csrf
                    <input type="hidden" name="recaptcha" id="recaptcha">
                    <div class="form-group">
                        <label class="form-label">الاسم</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">كلمة المرور</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="6">
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="6">
                    </div>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-user-plus"></i> إنشاء حساب
                    </button>
                    
                    @if(config('services.google.client_id') || config('services.facebook.client_id'))
                    <div class="social-login">
                        @if(config('services.google.client_id'))
                        <a href="/login/google/redirect" class="social-btn google">
                            <i class="fab fa-google"></i> التسجيل عبر Google
                        </a>
                        @endif
                        @if(config('services.facebook.client_id'))
                        <a href="/login/facebook/redirect" class="social-btn facebook">
                            <i class="fab fa-facebook-f"></i> التسجيل عبر Facebook
                        </a>
                        @endif
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </section>

    <script>
        function switchTab(tab) {
            // Update tabs
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            event.target.closest('.auth-tab').classList.add('active');
            
            // Update forms
            document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
            document.getElementById(tab + '-form').classList.add('active');
        }
        
        // Handle old values for active tab
        @if(old('name') || old('email') && !old('password'))
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.auth-tab')[1].classList.add('active');
            document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
            document.getElementById('register-form').classList.add('active');
        @endif
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

