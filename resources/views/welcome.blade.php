<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام التحليلات المتقدم - Analytics Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary: #0194fe;
            --primary-dark: #0178cc;
            --primary-light: #e6f4ff;
            --secondary: #10b981;
            --accent: #f59e0b;
            --text: #1f2937;
            --text-light: #6b7280;
            --bg: #ffffff;
            --bg-light: #f9fafb;
            --bg-dark: #0f172a;
            --border: #e5e7eb;
        }
        
        body {
            font-family: 'Baloo Bhaijaan', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
            z-index: 1000;
            padding: 1rem 0;
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .nav-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-family: 'Baloo Bhaijaan 2', sans-serif;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 15px rgba(1, 148, 254, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(1, 148, 254, 0.4);
        }
        
        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }
        
        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background: linear-gradient(135deg, #0194fe 0%, #0178cc 100%);
            padding: 8rem 2rem 4rem;
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
        }
        
        .hero-title {
            font-size: clamp(2.5rem, 8vw, 5rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            text-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        
        .hero-subtitle {
            font-size: clamp(1.1rem, 3vw, 1.5rem);
            margin-bottom: 2.5rem;
            opacity: 0.95;
            font-weight: 400;
            line-height: 1.6;
        }
        
        .hero-cta {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        /* Features Section */
        .features-section {
            padding: 6rem 2rem;
            background: var(--bg-light);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .section-title {
            text-align: center;
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 800;
            color: var(--text);
            margin-bottom: 1rem;
        }
        
        .section-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: var(--text-light);
            margin-bottom: 4rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .feature-card {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(1, 148, 254, 0.15);
            border-color: var(--primary-light);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(1, 148, 254, 0.3);
        }
        
        .feature-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 1rem;
        }
        
        .feature-description {
            color: var(--text-light);
            line-height: 1.8;
            font-size: 1rem;
        }
        
        /* Stats Section */
        .stats-section {
            padding: 6rem 2rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-top: 3rem;
        }
        
        .stat-card {
            text-align: center;
            padding: 2rem;
        }
        
        .stat-number {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .stat-label {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 500;
        }
        
        /* How It Works */
        .how-it-works {
            padding: 6rem 2rem;
            background: white;
        }
        
        .steps-container {
            max-width: 900px;
            margin: 4rem auto 0;
            position: relative;
        }
        
        .step {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 3rem;
            position: relative;
        }
        
        .step:nth-child(even) {
            flex-direction: row-reverse;
        }
        
        .step-number {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 800;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(1, 148, 254, 0.3);
        }
        
        .step-content {
            flex: 1;
            background: var(--bg-light);
            padding: 2rem;
            border-radius: 20px;
            border-right: 4px solid var(--primary);
        }
        
        .step:nth-child(even) .step-content {
            border-right: none;
            border-left: 4px solid var(--primary);
        }
        
        .step-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.5rem;
        }
        
        .step-description {
            color: var(--text-light);
            line-height: 1.8;
        }
        
        /* CTA Section */
        .cta-section {
            padding: 6rem 2rem;
            background: linear-gradient(135deg, var(--bg-dark) 0%, #1e293b 100%);
            color: white;
            text-align: center;
        }
        
        .cta-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
        }
        
        .cta-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Footer */
        .footer {
            background: var(--bg-dark);
            color: white;
            padding: 3rem 2rem 2rem;
            text-align: center;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .footer-copyright {
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
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
            z-index: 2000;
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
            font-family: 'Baloo Bhaijaan 2', sans-serif;
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
            font-family: 'Baloo Bhaijaan 2', sans-serif;
            transition: all 0.3s ease;
            background: var(--bg);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(1, 148, 254, 0.1);
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
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 8px;
            font-family: 'Baloo Bhaijaan 2', sans-serif;
        }
        
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(1, 148, 254, 0.4);
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
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .step {
                flex-direction: column !important;
            }
            
            .step-content {
                border-right: 4px solid var(--primary) !important;
                border-left: none !important;
            }
            
            .nav-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <a href="/" class="logo">
                <i class="fas fa-chart-line"></i>
                نظام التحليلات المتقدم
            </a>
            <div class="nav-buttons">
                <button class="btn btn-outline" onclick="openAuth('login')">
                    <i class="fas fa-sign-in-alt"></i>
                    تسجيل الدخول
                </button>
                <button class="btn btn-primary" onclick="openAuth('register')">
                    <i class="fas fa-user-plus"></i>
                    إنشاء حساب
                </button>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
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
                <button class="btn btn-primary" onclick="openAuth('register')" style="background: white; color: var(--primary);">
                    <i class="fas fa-rocket"></i>
                    ابدأ الآن مجاناً
                </button>
                <button class="btn btn-outline" onclick="openAuth('login')" style="background: rgba(255,255,255,0.15); color: white; border-color: white;">
                    <i class="fas fa-sign-in-alt"></i>
                    تسجيل الدخول
                </button>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">ميزات النظام المتكاملة</h2>
            <p class="section-subtitle">نظام تحليلات شامل يوفر لك كل ما تحتاجه لتتبع وتحليل زوار موقعك</p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="feature-title">إحصائيات شاملة</h3>
                    <p class="feature-description">
                        تتبع الزوار في الوقت الفعلي مع إحصائيات مفصلة عن الصفحات الأكثر زيارة، مصادر الزيارات، الدول، الأجهزة والمتصفحات
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">حماية متقدمة</h3>
                    <p class="feature-description">
                        نظام حماية ذكي مع راصد الأخطاء التلقائي، حدود الزيارات، وإعدادات Robots جاهزة لحماية موقعك
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h3 class="feature-title">نظام إعلانات متقدم</h3>
                    <p class="feature-description">
                        نظام إعلانات شامل مع تتبع الضغطات والمشاهدات، إعلانات داخل المحتوى، pop-ups، وإحصائيات مفصلة لكل إعلان
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-link"></i>
                    </div>
                    <h3 class="feature-title">أنماط URL مخصصة</h3>
                    <p class="feature-description">
                        إنشاء أنماط URL مخصصة لتتبع صفحات محددة، مع دعم wildcards وأنماط متقدمة للتحكم الكامل في التتبع
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">إدارة الفرق</h3>
                    <p class="feature-description">
                        دعوة أعضاء للانضمام إلى موقعك، إدارة الصلاحيات، وتتبع نشاط كل عضو مع نظام صلاحيات متقدم
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="feature-title">بحث متقدم</h3>
                    <p class="feature-description">
                        بحث شامل في جميع البيانات مع إمكانية البحث عن زوار محددين، صفحات، جلسات، ومصادر الزيارات
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">متجاوب بالكامل</h3>
                    <p class="feature-description">
                        لوحة تحكم متجاوبة تعمل بشكل مثالي على جميع الأجهزة - الكمبيوتر، التابلت، والهواتف الذكية
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="feature-title">أداء سريع</h3>
                    <p class="feature-description">
                        نظام محسّن للأداء مع تخزين مؤقت ذكي، استعلامات محسّنة، وتحميل سريع للبيانات
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <h3 class="feature-title">SiteMap تلقائي</h3>
                    <p class="feature-description">
                        منشئ SiteMap تلقائي لتحسين محركات البحث، مع دعم الروابط المخصصة وتحديث تلقائي
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3 class="feature-title">إشعارات فورية</h3>
                    <p class="feature-description">
                        نظام إشعارات في الوقت الفعلي مع إمكانية إرسال إشعارات مخصصة للمستخدمين والفرق
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3 class="feature-title">صفحات مخصصة</h3>
                    <p class="feature-description">
                        إنشاء صفحات مخصصة مع محرر متقدم، دعم HTML/CSS/JS، وإمكانية إنشاء صفحات ديناميكية
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-plug"></i>
                    </div>
                    <h3 class="feature-title">نظام Plugins</h3>
                    <p class="feature-description">
                        نظام plugins قابل للتوسع مع إمكانية إضافة ميزات جديدة مثل Google Analytics، Facebook Pixel، والمزيد
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <h2 class="section-title" style="color: white;">أرقامنا تتحدث</h2>
            <p class="section-subtitle" style="color: rgba(255,255,255,0.9);">نظام موثوق يستخدمه الآلاف</p>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">موثوقية النظام</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">دعم فني متواصل</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number">∞</span>
                    <span class="stat-label">مواقع غير محدودة</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">عربية بالكامل</span>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works">
        <div class="container">
            <h2 class="section-title">كيف يعمل النظام؟</h2>
            <p class="section-subtitle">خطوات بسيطة لبدء تتبع زوار موقعك</p>
            
            <div class="steps-container">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3 class="step-title">إنشاء حساب</h3>
                        <p class="step-description">سجّل حسابك مجاناً في دقائق معدودة وابدأ فوراً</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3 class="step-title">إضافة موقعك</h3>
                        <p class="step-description">أضف موقعك أو مواقعك المتعددة بسهولة مع إدارة كاملة لكل موقع</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3 class="step-title">تثبيت كود التتبع</h3>
                        <p class="step-description">انسخ كود التتبع البسيط والصقه في موقعك - لا حاجة لخبرة تقنية</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3 class="step-title">ابدأ التتبع</h3>
                        <p class="step-description">ابدأ تتبع زوارك فوراً مع إحصائيات مفصلة في الوقت الفعلي</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">جاهز للبدء؟</h2>
            <p class="cta-subtitle">انضم إلى آلاف المستخدمين الذين يثقون بنظامنا لتتبع وتحليل زوار مواقعهم</p>
            <div class="hero-cta">
                <button class="btn btn-primary" onclick="openAuth('register')" style="background: var(--primary); color: white; font-size: 1.2rem; padding: 1rem 2rem;">
                    <i class="fas fa-rocket"></i>
                    ابدأ مجاناً الآن
                </button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-links">
                <a href="#">الرئيسية</a>
                <a href="#">الميزات</a>
                <a href="#">الأسعار</a>
                <a href="#">تواصل معنا</a>
            </div>
            <div class="footer-copyright">
                <p>&copy; {{ date('Y') }} جميع الحقوق محفوظة - نظام التحليلات المتقدم</p>
            </div>
        </div>
    </footer>

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
