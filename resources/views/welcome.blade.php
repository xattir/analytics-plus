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
            --gradient-1: linear-gradient(135deg, #0194fe 0%, #0178cc 100%);
            --gradient-2: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --text: #1e293b;
            --text-light: #64748b;
            --bg: #ffffff;
            --bg-light: #f8fafc;
            --bg-dark: #0f172a;
        }
        
        body {
            font-family: 'Baloo Bhaijaan', sans-serif;
            color: var(--text);
            line-height: 1.7;
            overflow-x: hidden;
            background: var(--bg);
        }
        
        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(1, 148, 254, 0.1);
            z-index: 1000;
            padding: 1.25rem 0;
            transition: all 0.3s ease;
        }
        
        .header.scrolled {
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
        }
        
        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.75rem;
            font-weight: 800;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .logo i {
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .nav-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .btn {
            padding: 0.875rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            font-family: 'Baloo Bhaijaan', sans-serif;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-primary {
            background: var(--gradient-1);
            color: white;
            box-shadow: 0 4px 20px rgba(1, 148, 254, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(1, 148, 254, 0.4);
        }
        
        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 8rem 2rem 4rem;
            overflow: hidden;
            background: linear-gradient(180deg, #ffffff 0%, #f0f9ff 100%);
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(1, 148, 254, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s ease-in-out infinite;
        }
        
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(1, 148, 254, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 15s ease-in-out infinite reverse;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(30px, -30px) rotate(180deg); }
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 1000px;
            animation: fadeInUp 1s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            background: rgba(1, 148, 254, 0.1);
            border-radius: 50px;
            color: var(--primary);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(1, 148, 254, 0.2);
        }
        
        .hero-title {
            font-size: clamp(2.5rem, 8vw, 5.5rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-subtitle {
            font-size: clamp(1.1rem, 3vw, 1.4rem);
            margin-bottom: 2.5rem;
            color: var(--text-light);
            font-weight: 400;
            line-height: 1.8;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .hero-cta {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        /* Features Section */
        .features-section {
            padding: 8rem 2rem;
            background: var(--bg);
            position: relative;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 5rem;
        }
        
        .section-badge {
            display: inline-block;
            padding: 0.5rem 1.25rem;
            background: rgba(1, 148, 254, 0.1);
            border-radius: 50px;
            color: var(--primary);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .section-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            color: var(--text);
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        
        .section-subtitle {
            font-size: 1.2rem;
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }
        
        .feature-card {
            background: white;
            padding: 2.5rem;
            border-radius: 24px;
            border: 1px solid rgba(1, 148, 254, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-1);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(1, 148, 254, 0.15);
            border-color: rgba(1, 148, 254, 0.3);
        }
        
        .feature-card:hover::before {
            transform: scaleX(1);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: var(--gradient-1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 25px rgba(1, 148, 254, 0.25);
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        .feature-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.75rem;
        }
        
        .feature-description {
            color: var(--text-light);
            line-height: 1.8;
            font-size: 1rem;
        }
        
        /* Stats Section */
        .stats-section {
            padding: 6rem 2rem;
            background: var(--gradient-1);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .stats-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.1;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-top: 3rem;
            position: relative;
            z-index: 1;
        }
        
        .stat-card {
            text-align: center;
            padding: 2rem;
        }
        
        .stat-number {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            font-weight: 800;
            margin-bottom: 0.5rem;
            display: block;
            line-height: 1;
        }
        
        .stat-label {
            font-size: 1.2rem;
            opacity: 0.95;
            font-weight: 500;
        }
        
        /* How It Works */
        .how-it-works {
            padding: 8rem 2rem;
            background: var(--bg-light);
        }
        
        .steps-container {
            max-width: 1000px;
            margin: 4rem auto 0;
            position: relative;
        }
        
        .step {
            display: flex;
            align-items: center;
            gap: 3rem;
            margin-bottom: 4rem;
            position: relative;
        }
        
        .step:nth-child(even) {
            flex-direction: row-reverse;
        }
        
        .step-number {
            width: 90px;
            height: 90px;
            background: var(--gradient-1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 8px 30px rgba(1, 148, 254, 0.3);
            position: relative;
        }
        
        .step-number::after {
            content: '';
            position: absolute;
            width: 120%;
            height: 120%;
            border: 2px solid rgba(1, 148, 254, 0.2);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.7; }
        }
        
        .step-content {
            flex: 1;
            background: white;
            padding: 2.5rem;
            border-radius: 24px;
            border-right: 4px solid var(--primary);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        
        .step:nth-child(even) .step-content {
            border-right: none;
            border-left: 4px solid var(--primary);
        }
        
        .step-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.75rem;
        }
        
        .step-description {
            color: var(--text-light);
            line-height: 1.8;
            font-size: 1.1rem;
        }
        
        /* CTA Section */
        .cta-section {
            padding: 8rem 2rem;
            background: var(--bg-dark);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(1, 148, 254, 0.2) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .cta-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }
        
        .cta-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            z-index: 1;
        }
        
        /* Footer */
        .footer {
            background: #0a0f1a;
            color: white;
            padding: 4rem 2rem 2rem;
            text-align: center;
        }
        
        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2.5rem;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
            font-size: 1rem;
        }
        
        .footer-links a:hover {
            color: var(--primary);
        }
        
        .footer-copyright {
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
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
            padding: 20px;
        }
        
        .auth-modal.active {
            display: flex;
        }
        
        .auth-container {
            background: white;
            border-radius: 28px;
            max-width: 500px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 80px rgba(0,0,0,0.3);
            animation: slideUp 0.3s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .auth-header {
            padding: 2rem 2rem 1.5rem;
            border-bottom: 1px solid rgba(1, 148, 254, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .auth-title {
            font-size: 1.75rem;
            font-weight: 700;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .auth-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-light);
            cursor: pointer;
            padding: 4px;
            line-height: 1;
            transition: color 0.3s ease;
        }
        
        .auth-close:hover {
            color: var(--primary);
        }
        
        .auth-tabs {
            display: flex;
            border-bottom: 2px solid rgba(1, 148, 254, 0.1);
        }
        
        .auth-tab {
            flex: 1;
            padding: 1rem;
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
            font-family: 'Baloo Bhaijaan', sans-serif;
            font-size: 1rem;
        }
        
        .auth-tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        
        .auth-form {
            display: none;
            padding: 2rem;
        }
        
        .auth-form.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text);
            font-size: 0.95rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.875rem 1.25rem;
            border: 2px solid rgba(1, 148, 254, 0.2);
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Baloo Bhaijaan', sans-serif;
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
            margin-top: 0.5rem;
        }
        
        .btn-auth {
            width: 100%;
            padding: 1rem;
            background: var(--gradient-1);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
            font-family: 'Baloo Bhaijaan', sans-serif;
        }
        
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(1, 148, 254, 0.4);
        }
        
        .form-check {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .form-check input {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .form-check label {
            cursor: pointer;
            font-size: 0.95rem;
            color: var(--text);
        }
        
        .auth-footer {
            padding: 1.5rem 2rem;
            border-top: 1px solid rgba(1, 148, 254, 0.1);
            text-align: center;
        }
        
        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .header-container {
                padding: 0 1.5rem;
            }
            
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
                gap: 0.5rem;
            }
            
            .nav-buttons .btn {
                padding: 0.625rem 1.25rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header" id="header">
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
            <div class="hero-badge">
                <i class="fas fa-star"></i>
                نظام تحليلات متقدم وسهل الاستخدام
            </div>
            <h1 class="hero-title">
                افهم زوارك بشكل أفضل
            </h1>
            <p class="hero-subtitle">
                تتبع وتحليل زوار موقعك بسهولة تامة. احصل على إحصائيات مفصلة تساعدك على اتخاذ قرارات أفضل وزيادة أرباحك
            </p>
            <div class="hero-cta">
                <button class="btn btn-primary" onclick="openAuth('register')" style="background: var(--gradient-1); color: white; font-size: 1.1rem; padding: 1rem 2.5rem;">
                    <i class="fas fa-rocket"></i>
                    ابدأ الآن مجاناً
                </button>
                <button class="btn btn-outline" onclick="openAuth('login')" style="font-size: 1.1rem; padding: 1rem 2.5rem;">
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
                <h2 class="section-title">لماذا تختار نظامنا؟</h2>
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
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3 class="feature-title">كن على اطلاع دائم</h3>
                    <p class="feature-description">
                        إشعارات فورية عن أي شيء مهم. مثلاً: عندما يصل زائر جديد، أو عندما يحدث شيء يحتاج لانتباهك
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3 class="feature-title">تتبع مواقع متعددة</h3>
                    <p class="feature-description">
                        لديك أكثر من موقع؟ لا مشكلة. تتبع كل مواقعك من مكان واحد بسهولة تامة
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="feature-title">دعم فني متواصل</h3>
                    <p class="feature-description">
                        فريق دعم فني جاهز لمساعدتك في أي وقت. أسئلة؟ مشاكل؟ نحن هنا لمساعدتك دائماً
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="section-header" style="color: white;">
                <span class="section-badge" style="background: rgba(255,255,255,0.2); color: white; border-color: rgba(255,255,255,0.3);">أرقامنا</span>
                <h2 class="section-title" style="color: white;">لماذا يثق بنا الآلاف</h2>
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
            <p class="cta-subtitle">انضم إلى آلاف المستخدمين الذين يثقون بنظامنا لتحسين مواقعهم وزيادة أرباحهم</p>
            <div class="hero-cta">
                <button class="btn btn-primary" onclick="openAuth('register')" style="background: var(--gradient-1); color: white; font-size: 1.2rem; padding: 1.25rem 3rem;">
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
        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
        
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
