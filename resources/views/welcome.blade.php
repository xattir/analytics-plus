<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحليلات + - نظام التحليلات المتقدم</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            --gradient: linear-gradient(135deg, #0194fe 0%, #0178cc 100%);
            --text: #1e293b;
            --text-light: #64748b;
            --bg: #ffffff;
            --bg-light: #f8fafc;
            --bg-dark: #0f172a;
            --border: #e2e8f0;
        }
        
        body {
            font-family: 'Baloo Bhaijaan 2', sans-serif;
            color: var(--text);
            line-height: 1.7;
            overflow-x: hidden;
            background: var(--bg);
            font-weight: 400;
        }
        
        /* Mobile First - Base Styles */
        
        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            z-index: 1000;
            padding: 1rem 0;
        }
        
        .header-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 0 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .logo i {
            font-size: 1.75rem;
        }
        
        .nav-buttons {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }
        
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.9rem;
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
            background: var(--gradient);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(1, 148, 254, 0.3);
        }
        
        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 1.5px solid var(--primary);
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
            padding: 6rem 1.25rem 4rem;
            background: linear-gradient(180deg, #ffffff 0%, #f0f9ff 50%, #e0f2fe 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(1, 148, 254, 0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 100%;
            width: 100%;
        }
        
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(1, 148, 254, 0.1);
            border-radius: 20px;
            color: var(--primary);
            font-weight: 500;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }
        
        .hero-title {
            font-size: 2.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            line-height: 1.2;
            color: var(--text);
        }
        
        .hero-title .highlight {
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            color: var(--text-light);
            font-weight: 400;
            line-height: 1.7;
            max-width: 100%;
        }
        
        .hero-cta {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            align-items: center;
        }
        
        .hero-cta .btn {
            width: 100%;
            max-width: 300px;
            justify-content: center;
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
        }
        
        /* Features Section */
        .features-section {
            padding: 4rem 1.25rem;
            background: var(--bg);
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(1, 148, 254, 0.1);
            border-radius: 20px;
            color: var(--primary);
            font-weight: 500;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        
        .section-title {
            font-size: 1.875rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }
        
        .section-subtitle {
            font-size: 1rem;
            color: var(--text-light);
            font-weight: 400;
            max-width: 100%;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .feature-card {
            background: white;
            padding: 1.75rem;
            border-radius: 20px;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(1, 148, 254, 0.1);
            border-color: var(--primary);
        }
        
        .feature-icon {
            width: 56px;
            height: 56px;
            background: var(--gradient);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1.25rem;
        }
        
        .feature-title {
            font-size: 1.25rem;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 0.5rem;
        }
        
        .feature-description {
            color: var(--text-light);
            line-height: 1.7;
            font-size: 0.95rem;
            font-weight: 400;
        }
        
        /* Stats Section */
        .stats-section {
            padding: 4rem 1.25rem;
            background: var(--gradient);
            color: white;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .stat-card {
            text-align: center;
            padding: 1.5rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
            line-height: 1;
        }
        
        .stat-label {
            font-size: 0.95rem;
            opacity: 0.95;
            font-weight: 400;
        }
        
        /* How It Works */
        .how-it-works {
            padding: 4rem 1.25rem;
            background: var(--bg-light);
        }
        
        .steps-container {
            max-width: 100%;
            margin: 2rem auto 0;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.25rem;
            margin-bottom: 2.5rem;
            text-align: center;
        }
        
        .step-number {
            width: 64px;
            height: 64px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            font-weight: 600;
            color: white;
            flex-shrink: 0;
        }
        
        .step-content {
            flex: 1;
            background: white;
            padding: 1.75rem;
            border-radius: 20px;
            border-top: 3px solid var(--primary);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            width: 100%;
        }
        
        .step-title {
            font-size: 1.25rem;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 0.5rem;
        }
        
        .step-description {
            color: var(--text-light);
            line-height: 1.7;
            font-size: 0.95rem;
            font-weight: 400;
        }
        
        /* CTA Section */
        .cta-section {
            padding: 4rem 1.25rem;
            background: var(--bg-dark);
            color: white;
            text-align: center;
        }
        
        .cta-title {
            font-size: 1.875rem;
            font-weight: 600;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        
        .cta-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            font-weight: 400;
            line-height: 1.7;
        }
        
        /* Footer */
        .footer {
            background: #0a0f1a;
            color: white;
            padding: 3rem 1.25rem 2rem;
            text-align: center;
        }
        
        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
            font-size: 0.95rem;
            font-weight: 400;
        }
        
        .footer-links a:hover {
            color: var(--primary);
        }
        
        .footer-copyright {
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
            font-weight: 400;
        }
        
        /* Auth Modal */
        .auth-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(8px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
        }
        
        .auth-modal.active {
            display: flex;
        }
        
        .auth-container {
            background: white;
            border-radius: 24px;
            max-width: 100%;
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
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .auth-title {
            font-size: 1.5rem;
            font-weight: 500;
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
        
        .auth-tabs {
            display: flex;
            border-bottom: 2px solid var(--border);
        }
        
        .auth-tab {
            flex: 1;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            font-weight: 500;
            color: var(--text-light);
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
            background: none;
            border-top: none;
            border-left: none;
            border-right: none;
            font-family: 'Baloo Bhaijaan 2', sans-serif;
            font-size: 0.95rem;
        }
        
        .auth-tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        
        .auth-form {
            display: none;
            padding: 1.5rem;
        }
        
        .auth-form.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--text);
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Baloo Bhaijaan 2', sans-serif;
            transition: all 0.3s ease;
            background: var(--bg);
            font-weight: 400;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(1, 148, 254, 0.1);
        }
        
        .form-control.is-invalid {
            border-color: #ef4444;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            font-weight: 400;
        }
        
        .btn-auth {
            width: 100%;
            padding: 0.875rem;
            background: var(--gradient);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
            font-family: 'Baloo Bhaijaan 2', sans-serif;
        }
        
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(1, 148, 254, 0.3);
        }
        
        .form-check {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
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
            font-weight: 400;
        }
        
        .auth-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid var(--border);
            text-align: center;
        }
        
        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        /* Tablet Styles */
        @media (min-width: 640px) {
            .header-container {
                padding: 0 2rem;
            }
            
            .hero-section {
                padding: 7rem 2rem 5rem;
            }
            
            .hero-title {
                font-size: 3rem;
            }
            
            .hero-subtitle {
                font-size: 1.2rem;
            }
            
            .hero-cta {
                flex-direction: row;
                justify-content: center;
            }
            
            .hero-cta .btn {
                width: auto;
            }
            
            .features-section {
                padding: 5rem 2rem;
            }
            
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 2rem;
            }
            
            .stats-section {
                padding: 5rem 2rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(4, 1fr);
            }
            
            .how-it-works {
                padding: 5rem 2rem;
            }
            
            .step {
                flex-direction: row;
                text-align: right;
            }
            
            .step:nth-child(even) {
                flex-direction: row-reverse;
                text-align: left;
            }
            
            .step-content {
                border-top: none;
                border-right: 3px solid var(--primary);
            }
            
            .step:nth-child(even) .step-content {
                border-right: none;
                border-left: 3px solid var(--primary);
            }
            
            .cta-section {
                padding: 5rem 2rem;
            }
            
            .cta-title {
                font-size: 2.5rem;
            }
            
            .footer-links {
                flex-direction: row;
                justify-content: center;
                gap: 2rem;
            }
            
            .auth-container {
                max-width: 480px;
            }
        }
        
        /* Desktop Styles */
        @media (min-width: 1024px) {
            .header-container {
                max-width: 1200px;
                padding: 0 3rem;
            }
            
            .hero-section {
                padding: 8rem 3rem 6rem;
            }
            
            .hero-content {
                max-width: 900px;
            }
            
            .hero-title {
                font-size: 3.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.3rem;
                max-width: 700px;
                margin-left: auto;
                margin-right: auto;
            }
            
            .container {
                max-width: 1200px;
            }
            
            .features-section {
                padding: 6rem 3rem;
            }
            
            .section-title {
                font-size: 2.5rem;
            }
            
            .section-subtitle {
                font-size: 1.15rem;
            }
            
            .features-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 2.5rem;
            }
            
            .stats-section {
                padding: 6rem 3rem;
            }
            
            .how-it-works {
                padding: 6rem 3rem;
            }
            
            .steps-container {
                max-width: 900px;
            }
            
            .cta-section {
                padding: 6rem 3rem;
            }
            
            .footer {
                padding: 4rem 3rem 2rem;
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
                تحليلات +
            </a>
            <div class="nav-buttons">
                <button class="btn btn-outline" onclick="openAuth('login')">
                    <i class="fas fa-sign-in-alt"></i>
                    دخول
                </button>
                <button class="btn btn-primary" onclick="openAuth('register')">
                    <i class="fas fa-user-plus"></i>
                    حساب جديد
                </button>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-star"></i>
                نظام تحليلات متقدم
            </div>
            <h1 class="hero-title">
                افهم <span class="highlight">زوارك</span> بشكل أفضل
            </h1>
            <p class="hero-subtitle">
                تتبع وتحليل زوار موقعك بسهولة تامة. احصل على إحصائيات مفصلة تساعدك على اتخاذ قرارات أفضل وزيادة أرباحك
            </p>
            <div class="hero-cta">
                <button class="btn btn-primary" onclick="openAuth('register')">
                    <i class="fas fa-rocket"></i>
                    ابدأ الآن مجاناً
                </button>
                <button class="btn btn-outline" onclick="openAuth('login')">
                    <i class="fas fa-sign-in-alt"></i>
                    تسجيل الدخول
                </button>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">ميزات النظام</span>
                <h2 class="section-title">لماذا تختار تحليلات +؟</h2>
                <p class="section-subtitle">نوفر لك كل ما تحتاجه لتحسين موقعك وزيادة أرباحك</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">افهم زوارك بشكل أفضل</h3>
                    <p class="feature-description">
                        تعرف على من يزور موقعك، من أين يأتون، وما الذي يبحثون عنه. هذه المعلومات تساعدك على تحسين محتواك وزيادة المبيعات
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3 class="feature-title">اربح المزيد من الإعلانات</h3>
                    <p class="feature-description">
                        نظام إعلانات متقدم يتتبع كل ضغطة ومشاهدة. ضع إعلاناتك في الأماكن الصحيحة واربح المزيد من المال
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="feature-title">ظهور أفضل في محركات البحث</h3>
                    <p class="feature-description">
                        نظام SiteMap تلقائي يساعد محركات البحث على فهرسة موقعك بسهولة، مما يعني ظهورك في نتائج البحث أكثر
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="feature-title">اتخذ قرارات صحيحة</h3>
                    <p class="feature-description">
                        إحصائيات مفصلة عن الصفحات الأكثر زيارة، أفضل أوقات الزيارات، وأكثر. استخدم هذه البيانات لتحسين موقعك
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">موقعك آمن ومحمي</h3>
                    <p class="feature-description">
                        نظام حماية ذكي يحمي موقعك من الزيارات المشبوهة والأخطاء. راصد تلقائي للأخطاء يساعدك على إصلاح المشاكل بسرعة
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3 class="feature-title">تتبع دقيق لصفحاتك المهمة</h3>
                    <p class="feature-description">
                        حدد الصفحات المهمة لديك وتتبع زوارها بدقة. مثلاً: تتبع صفحة المنتجات فقط، أو صفحة التسجيل، أو أي صفحة تريدها
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <h3 class="feature-title">اعمل مع فريقك بسهولة</h3>
                    <p class="feature-description">
                        ادعُ أعضاء فريقك للانضمام وشاركهم البيانات. كل عضو له صلاحياته الخاصة. مثالي للشركات والفرق
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">راقب موقعك من أي مكان</h3>
                    <p class="feature-description">
                        لوحة تحكم متجاوبة تعمل بشكل مثالي على هاتفك، تابلتك، أو كمبيوترك. تابع موقعك وأنت في الطريق
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="feature-title">سرعة فائقة</h3>
                    <p class="feature-description">
                        نظام محسّن للأداء يعمل بسرعة فائقة. لا تنتظر تحميل البيانات، كل شيء سريع وسلس
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="section-header" style="color: white;">
                <span class="section-badge" style="background: rgba(255,255,255,0.2); color: white;">أرقامنا</span>
                <h2 class="section-title" style="color: white; font-weight: 500;">لماذا يثق بنا الآلاف</h2>
            </div>
            
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
            <div class="section-header">
                <span class="section-badge">كيف يعمل</span>
                <h2 class="section-title">ابدأ في 4 خطوات بسيطة</h2>
                <p class="section-subtitle">سجّل، أضف موقعك، ثبت الكود، وابدأ التتبع فوراً</p>
            </div>
            
            <div class="steps-container">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3 class="step-title">سجّل حسابك مجاناً</h3>
                        <p class="step-description">أنشئ حسابك في دقائق معدودة. لا حاجة لبطاقة ائتمان، كل شيء مجاني</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3 class="step-title">أضف موقعك</h3>
                        <p class="step-description">أضف موقعك أو مواقعك المتعددة. يمكنك إضافة عدد غير محدود من المواقع</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3 class="step-title">انسخ والصق كود التتبع</h3>
                        <p class="step-description">كود بسيط واحد. انسخه والصقه في موقعك - لا حاجة لخبرة تقنية</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3 class="step-title">ابدأ التتبع فوراً</h3>
                        <p class="step-description">ابدأ رؤية البيانات فوراً. إحصائيات مفصلة في الوقت الفعلي</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">جاهز للبدء؟</h2>
            <p class="cta-subtitle">انضم إلى آلاف المستخدمين الذين يثقون بتحليلات + لتحسين مواقعهم وزيادة أرباحهم</p>
            <div class="hero-cta">
                <button class="btn btn-primary" onclick="openAuth('register')" style="background: var(--gradient); color: white;">
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
                <p>&copy; {{ date('Y') }} جميع الحقوق محفوظة - تحليلات +</p>
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
            document.querySelectorAll('.auth-tab').forEach(t => {
                t.classList.remove('active');
                if (t.textContent.includes(tab === 'login' ? 'تسجيل الدخول' : 'إنشاء حساب')) {
                    t.classList.add('active');
                }
            });
            
            document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
            document.getElementById(tab + '-form').classList.add('active');
        }
        
        @if(old('name') || (old('email') && !old('password')))
            openAuth('register');
        @elseif(old('email'))
            openAuth('login');
        @endif
        
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
