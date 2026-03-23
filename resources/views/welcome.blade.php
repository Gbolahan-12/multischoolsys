<!DOCTYPE html>
<html lang="en" style="overflow-x: hidden">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>MahkhalHub — Multi-School Management System</title>

    <!-- Overpass Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{asset('admin-assets/images/logo/multischoollogo.jpeg')}}" />


    <style>
        :root {
            --primary: #1A69AE;
            --primary-dark: #155a94;
            --primary-light: rgba(26, 105, 174, 0.1);
        }

        /* ── Font applied globally ── */
        *,
        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        a,
        span,
        li,
        button,
        input {
            font-family: 'Overpass', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            color: #1a1a2e;
        }

        /* ── Scroll reveal animations ── */
        .reveal {
            opacity: 0;
            transform: translateY(36px);
            transition: opacity 0.6s cubic-bezier(.4, 0, .2, 1), transform 0.6s cubic-bezier(.4, 0, .2, 1);
        }

        .reveal-left {
            opacity: 0;
            transform: translateX(-40px);
            transition: opacity 0.6s cubic-bezier(.4, 0, .2, 1), transform 0.6s cubic-bezier(.4, 0, .2, 1);
        }

        .reveal-right {
            opacity: 0;
            transform: translateX(40px);
            transition: opacity 0.6s cubic-bezier(.4, 0, .2, 1), transform 0.6s cubic-bezier(.4, 0, .2, 1);
        }

        .reveal.visible,
        .reveal-left.visible,
        .reveal-right.visible {
            opacity: 1;
            transform: translate(0);
        }

        .reveal-delay-1 {
            transition-delay: 0.1s;
        }

        .reveal-delay-2 {
            transition-delay: 0.2s;
        }

        .reveal-delay-3 {
            transition-delay: 0.3s;
        }

        .reveal-delay-4 {
            transition-delay: 0.4s;
        }

        /* ── Navbar ── */
        .navbar {
            padding: 18px 0;
            background: #fff;
            box-shadow: 0 1px 0 #e9ecef;
        }

        .navbar-brand {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--primary) !important;
            letter-spacing: -0.5px;
        }

        .navbar-brand span {
            color: #1a1a2e;
        }

        .nav-link {
            color: #444 !important;
            font-weight: 600;
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        /* ── Buttons ── */
        .btn {
            font-weight: 700;
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        /* ── Hero ── */
        .hero-section {
            background: linear-gradient(135deg, #f0f7ff 0%, #e8f4fd 50%, #f8fbff 100%);
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(26, 105, 174, 0.08) 0%, transparent 70%);
            border-radius: 50%;
        }

        /* Hero entrance animations */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(28px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-in {
            animation: fadeUp 0.7s ease forwards;
            opacity: 0;
        }

        .hero-in-1 {
            animation-delay: 0.1s;
        }

        .hero-in-2 {
            animation-delay: 0.25s;
        }

        .hero-in-3 {
            animation-delay: 0.4s;
        }

        .hero-in-4 {
            animation-delay: 0.55s;
        }

        .hero-in-5 {
            animation-delay: 0.7s;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--primary-light);
            color: var(--primary);
            border: 1px solid rgba(26, 105, 174, 0.2);
            border-radius: 50px;
            padding: 6px 16px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .hero-title {
            font-size: 3.2rem;
            font-weight: 900;
            line-height: 1.15;
            color: #0d1b2a;
        }

        .hero-title span {
            color: var(--primary);
        }

        .hero-subtitle {
            font-size: 1.1rem;
            color: #5a6a7a;
            line-height: 1.8;
            font-weight: 400;
        }

        .check-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .check-item i {
            color: #22c55e;
            font-size: 16px;
            flex-shrink: 0;
        }

        /* ── Stats ── */
        .stat-card {
            text-align: center;
            padding: 36px 20px;
        }

        .stat-number {
            font-size: 2.6rem;
            font-weight: 900;
            color: var(--primary);
            line-height: 1;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
            margin-top: 6px;
            font-weight: 600;
        }

        /* ── Section spacing ── */
        .section-gap {
            padding: 100px 0;
        }

        .section-gap-bg {
            padding: 100px 0;
            background: #f8fafc;
        }

        /* ── Feature Cards ── */
        .feature-icon {
            width: 56px;
            height: 56px;
            background: var(--primary-light);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .feature-card {
            padding: 32px 24px;
            border-radius: 16px;
            border: 1px solid #e8f0f8;
            background: #fff;
            height: 100%;
            transition: all .25s ease;
        }

        .feature-card:hover {
            box-shadow: 0 12px 40px rgba(26, 105, 174, 0.12);
            transform: translateY(-5px);
            border-color: rgba(26, 105, 174, 0.2);
        }

        .feature-card h4 {
            font-weight: 800;
            font-size: 1.05rem;
            margin-bottom: 10px;
            color: #0d1b2a;
        }

        .feature-card p {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
            margin: 0;
            font-weight: 400;
        }

        /* ── Section titles ── */
        .section-label {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 2.3rem;
            font-weight: 900;
            color: #0d1b2a;
            line-height: 1.2;
        }

        .section-sub {
            font-size: 1rem;
            color: #6b7280;
            line-height: 1.8;
            margin-top: 14px;
            font-weight: 400;
        }

        /* ── Role Cards ── */
        .role-card {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e8f0f8;
            transition: all .25s ease;
        }

        .role-card:hover {
            box-shadow: 0 12px 40px rgba(26, 105, 174, 0.12);
            transform: translateY(-4px);
        }

        .role-card-header {
            padding: 28px 24px;
            color: #fff;
        }

        .role-card-header h5 {
            font-weight: 800;
        }

        .role-card-body {
            padding: 24px;
            background: #fff;
        }

        .role-feature {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #374151;
            margin-bottom: 9px;
            font-weight: 500;
        }

        .role-feature i {
            color: #22c55e;
        }

        /* ── Steps ── */
        .step-circle {
            width: 64px;
            height: 64px;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 900;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            transition: all .25s ease;
        }

        .step-circle:hover {
            background: var(--primary);
            color: #fff;
            transform: scale(1.08);
        }

        /* ── Testimonials ── */
        .testimonial-card {
            background: #fff;
            border: 1px solid #e8f0f8;
            border-radius: 16px;
            padding: 32px;
            height: 100%;
            transition: all .25s ease;
        }

        .testimonial-card:hover {
            box-shadow: 0 8px 30px rgba(26, 105, 174, 0.1);
            transform: translateY(-3px);
        }

        .testimonial-card p {
            font-size: 15px;
            color: #4b5563;
            line-height: 1.8;
            font-style: italic;
            font-weight: 400;
        }

        .testimonial-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            flex-shrink: 0;
        }

        /* ── Pricing ── */
        .pricing-card {
            border-radius: 16px;
            border: 1px solid #e8f0f8;
            overflow: hidden;
            transition: all .25s ease;
        }

        .pricing-card:hover {
            box-shadow: 0 12px 40px rgba(26, 105, 174, 0.12);
            transform: translateY(-4px);
        }

        .pricing-card.featured {
            border-color: var(--primary);
            box-shadow: 0 8px 30px rgba(26, 105, 174, 0.15);
        }

        .pricing-card-body {
            padding: 40px 36px;
        }

        .pricing-price {
            font-size: 3rem;
            font-weight: 900;
            color: #0d1b2a;
            line-height: 1;
        }

        .pricing-feature {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #374151;
            margin-bottom: 12px;
            font-weight: 500;
        }

        /* ── CTA ── */
        .cta-section {
            background: linear-gradient(135deg, var(--primary) 0%, #1557a0 100%);
            color: #fff;
            padding: 90px 0;
            border-radius: 24px;
            margin: 0 24px;
        }

        .cta-section h2 {
            font-weight: 900;
        }

        /* ── Footer ── */
        footer {
            background: #0d1b2a;
            color: #9ca3af;
            padding: 70px 0 30px;
        }

        footer h5 {
            color: #fff;
            font-weight: 800;
            margin-bottom: 20px;
        }

        footer a {
            color: #9ca3af;
            text-decoration: none;
            font-size: 14px;
            display: block;
            margin-bottom: 10px;
            font-weight: 400;
            transition: color .2s;
        }

        footer a:hover {
            color: #fff;
        }

        .footer-brand {
            font-size: 1.5rem;
            font-weight: 900;
            color: #fff;
        }

        .footer-brand span {
            color: var(--primary);
        }

        /* ── Scroll to top ── */
        .scroll-top {
            position: fixed;
            bottom: 28px;
            right: 28px;
            width: 46px;
            height: 46px;
            background: var(--primary);
            color: #fff;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(26, 105, 174, 0.45);
            z-index: 999;
            transition: background .2s;
        }

        .scroll-top:hover {
            background: var(--primary-dark);
        }

        .scroll-top.show {
            display: flex;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.2rem;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .cta-section {
                margin: 0 12px;
                border-radius: 16px;
                padding: 60px 0;
            }

            .section-gap,
            .section-gap-bg {
                padding: 70px 0;
            }
        }
    </style>
</head>

<body>

    <!-- ── Navbar ─────────────────────────────────────────────────── -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('welcome') }}"><img
        src="{{ asset('admin-assets/images/logo/multischoollogo-removebg-preview.png') }}" width="80"
        alt=""></a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <i class="bi bi-list fs-3"></i>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav mx-auto gap-1">
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#roles">Who It's For</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pricing">Pricing</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                    <a href="/login" class="btn btn-outline-primary btn-sm px-3">Sign In</a>
                    <a href="/register" class="btn btn-primary btn-sm px-3">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <main>

        <!-- ── Hero ───────────────────────────────────────────────────── -->
        <section class="hero-section">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-12 col-lg-6">
                        <div class="hero-badge hero-in hero-in-1">
                            <i class="bi bi-stars"></i> All-in-one school management platform
                        </div>
                        <h1 class="hero-title mb-4 hero-in hero-in-2">
                            Manage Every School<br><span>From One Platform</span>
                        </h1>
                        <p class="hero-subtitle mb-5 hero-in hero-in-3">
                            MahkhalHub gives proprietors, admins, and staff a powerful yet simple system to manage
                            students, fees, results, report cards, and subscriptions — all under one roof.
                        </p>
                        <div class="mb-5 hero-in hero-in-4">
                            <div class="check-item"><i class="bi bi-check-circle-fill"></i> Manage multiple schools from
                                a single super-admin panel</div>
                            <div class="check-item"><i class="bi bi-check-circle-fill"></i> Automated report cards with
                                class averages & positions</div>
                            <div class="check-item"><i class="bi bi-check-circle-fill"></i> Full payment tracking —
                                compulsory & optional fees</div>
                            <div class="check-item"><i class="bi bi-check-circle-fill"></i> CSV result upload for bulk
                                score entry</div>
                        </div>
                        <div class="d-flex align-items-center gap-3 flex-wrap hero-in hero-in-5">
                            <a href="/register" class="btn btn-primary px-4 py-2">Get Started Free</a>
                            <a href="#features" class="btn btn-outline-primary px-4 py-2">See Features</a>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 hero-in hero-in-3">
                        <div class="rounded-4 shadow-lg overflow-hidden border" style="background:#f8fafc;">
                            <div class="d-flex align-items-center gap-2 px-3 py-2"
                                style="background:#e9ecef;border-bottom:1px solid #dee2e6;">
                                <div class="rounded-circle" style="width:10px;height:10px;background:#ff5f57;"></div>
                                <div class="rounded-circle" style="width:10px;height:10px;background:#febc2e;"></div>
                                <div class="rounded-circle" style="width:10px;height:10px;background:#28c840;"></div>
                                <div class="flex-grow-1 mx-3 rounded"
                                    style="background:#fff;height:24px;padding:0 10px;display:flex;align-items:center;">
                                    <span style="font-size:11px;color:#9ca3af;">MahkhalHub.app/admin/dashboard</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="rounded-3 p-3" style="background:#fff;border:1px solid #e8f0f8;">
                                            <div style="font-size:11px;color:#6b7280;">Total Students</div>
                                            <div style="font-size:1.4rem;font-weight:800;color:#1A69AE;">1,248</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="rounded-3 p-3" style="background:#fff;border:1px solid #e8f0f8;">
                                            <div style="font-size:11px;color:#6b7280;">Fees Collected</div>
                                            <div style="font-size:1.4rem;font-weight:800;color:#22c55e;">₦2.4M</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="rounded-3 p-3" style="background:#fff;border:1px solid #e8f0f8;">
                                            <div style="font-size:11px;color:#6b7280;">Students Owing</div>
                                            <div style="font-size:1.4rem;font-weight:800;color:#ef4444;">84</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="rounded-3 p-3" style="background:#fff;border:1px solid #e8f0f8;">
                                            <div style="font-size:11px;color:#6b7280;">Results Uploaded</div>
                                            <div style="font-size:1.4rem;font-weight:800;color:#f59f00;">3,560</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="rounded-3 p-3" style="background:#fff;border:1px solid #e8f0f8;">
                                    <div style="font-size:11px;color:#6b7280;margin-bottom:10px;font-weight:700;">Fee
                                        Collection — Last 6 Months</div>
                                    <div class="d-flex align-items-end gap-1" style="height:54px;">
                                        <div style="flex:1;background:#bfdbfe;border-radius:4px 4px 0 0;height:38%;">
                                        </div>
                                        <div style="flex:1;background:#93c5fd;border-radius:4px 4px 0 0;height:55%;">
                                        </div>
                                        <div style="flex:1;background:#60a5fa;border-radius:4px 4px 0 0;height:44%;">
                                        </div>
                                        <div style="flex:1;background:#3b82f6;border-radius:4px 4px 0 0;height:72%;">
                                        </div>
                                        <div style="flex:1;background:#2563eb;border-radius:4px 4px 0 0;height:84%;">
                                        </div>
                                        <div style="flex:1;background:#1A69AE;border-radius:4px 4px 0 0;height:100%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats row -->
                <div class="row mt-6 pt-5 border-top g-0 reveal">
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Schools Registered</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-number">50K+</div>
                            <div class="stat-label">Students Managed</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-number">₦1B+</div>
                            <div class="stat-label">Fees Processed</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-number">99.9%</div>
                            <div class="stat-label">Uptime Guarantee</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Features ───────────────────────────────────────────────── -->
        <section class="section-gap" id="features">
            <div class="container">
                <div class="text-center mb-6 reveal">
                    <div class="section-label">Platform Features</div>
                    <h2 class="section-title">Everything your school needs</h2>
                    <p class="section-sub mx-auto" style="max-width:560px;">From student admission to report card
                        generation, MahkhalHub covers every aspect of school management in one seamless platform.</p>
                </div>
                <div class="row g-4">
                    <div class="col-12 col-md-6 col-lg-4 reveal reveal-delay-1">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-people-fill"></i></div>
                            <h4>Student Management</h4>
                            <p>Admit students, track class assignments per session/term, manage profiles with photos,
                                and maintain complete academic records.</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 reveal reveal-delay-2">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-cash-stack"></i></div>
                            <h4>Fee & Payment Tracking</h4>
                            <p>Create compulsory and optional fees, record payments, track balances, and see exactly who
                                is owing — by term and session.</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 reveal reveal-delay-3">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-file-earmark-bar-graph"></i></div>
                            <h4>Result Management</h4>
                            <p>Upload CA1, CA2, and exam scores individually or in bulk via CSV. Automatic grade and
                                remark computation.</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 reveal reveal-delay-1">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-card-text"></i></div>
                            <h4>PDF Report Cards</h4>
                            <p>Generate beautiful report cards with school logo, class averages, student position, grade
                                key, and teacher remarks — downloadable as PDF.</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 reveal reveal-delay-2">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-building"></i></div>
                            <h4>Multi-School Support</h4>
                            <p>One super-admin manages multiple schools. Each school is fully isolated — data, users,
                                fees, and results never cross schools.</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 reveal reveal-delay-3">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-shield-lock"></i></div>
                            <h4>Role-Based Access</h4>
                            <p>Proprietor, Admin, Staff, and School-User roles with granular permissions. Each person
                                sees only what they need to.</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 reveal reveal-delay-1">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-journal-bookmark"></i></div>
                            <h4>Subject Assignment</h4>
                            <p>Assign subjects to classes and teachers per session. Teachers only see their assigned
                                classes and upload their subject scores.</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 reveal reveal-delay-2">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-calendar3"></i></div>
                            <h4>Session & Term Control</h4>
                            <p>Manage academic sessions and terms. Set active sessions, track payments and results per
                                term automatically.</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 reveal reveal-delay-3">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                            <h4>Dashboards & Reports</h4>
                            <p>Live charts for fee collection, student enrollment growth, grade distributions, and
                                payment status breakdowns — for every role.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Who It's For ────────────────────────────────────────────── -->
        <section class="section-gap-bg" id="roles">
            <div class="container">
                <div class="text-center mb-6 reveal">
                    <div class="section-label">Built for Everyone</div>
                    <h2 class="section-title">One system, every role</h2>
                    <p class="section-sub mx-auto" style="max-width:520px;">MahkhalHub is designed around how schools
                        actually work. Every role gets exactly what they need.</p>
                </div>
                <div class="row g-4">
                    <div class="col-12 col-md-6 col-lg-3 reveal reveal-delay-1">
                        <div class="role-card h-100">
                            <div class="role-card-header" style="background:linear-gradient(135deg,#1A69AE,#1557a0);"><i
                                    class="bi bi-globe2 fs-2 mb-2 d-block"></i>
                                <h5>Super Admin</h5><small style="opacity:.8;">Platform Owner</small>
                            </div>
                            <div class="role-card-body">
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Manage all schools
                                </div>
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Activate & ban schools
                                </div>
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Record subscriptions
                                </div>
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Auto-suspend expired
                                </div>
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Platform-wide stats
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 reveal reveal-delay-2">
                        <div class="role-card h-100">
                            <div class="role-card-header" style="background:linear-gradient(135deg,#6f42c1,#5a35a0);"><i
                                    class="bi bi-person-badge fs-2 mb-2 d-block"></i>
                                <h5>Proprietor</h5><small style="opacity:.8;">School Owner</small>
                            </div>
                            <div class="role-card-body">
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> School profile & logo
                                </div>
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Manage roles &
                                    permissions</div>
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Payment overview</div>
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Staff & admin control
                                </div>
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Financial dashboards
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 reveal reveal-delay-3">
                        <div class="role-card h-100">
                            <div class="role-card-header" style="background:linear-gradient(135deg,#198754,#157347);"><i
                                    class="bi bi-pc-display-horizontal fs-2 mb-2 d-block"></i>
                                <h5>Admin</h5><small style="opacity:.8;">School Manager</small>
                            </div>
                            <div class="role-card-body">
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Manage students &
                                    classes</div>
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Record fees & payments
                                </div>
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Assign subjects to
                                    staff</div>
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Generate report cards
                                </div>
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> View defaulter list
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 reveal reveal-delay-4">
                        <div class="role-card h-100">
                            <div class="role-card-header" style="background:linear-gradient(135deg,#fd7e14,#e06b0a);"><i
                                    class="bi bi-person-workspace fs-2 mb-2 d-block"></i>
                                <h5>Staff / Teacher</h5><small style="opacity:.8;">Classroom Level</small>
                            </div>
                            <div class="role-card-body">
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> View assigned subjects
                                </div>
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Upload results via CSV
                                </div>
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Track upload progress
                                </div>
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Preview report cards
                                </div>
                                <div class="role-feature"><i class="bi bi-check-circle-fill"></i> Class-only access
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── How It Works ────────────────────────────────────────────── -->
        <section class="section-gap">
            <div class="container">
                <div class="text-center mb-6 reveal">
                    <div class="section-label">How It Works</div>
                    <h2 class="section-title">Up and running in minutes</h2>
                </div>
                <div class="row g-4 text-center">
                    <div class="col-12 col-md-4 reveal reveal-delay-1">
                        <div class="step-circle">1</div>
                        <h5 class="fw-bold mb-2">Register Your School</h5>
                        <p class="text-muted" style="font-size:14px;line-height:1.8;">Sign up, get approved by
                            super-admin, and your school is live with its own isolated environment.</p>
                    </div>
                    <div class="col-12 col-md-4 reveal reveal-delay-2">
                        <div class="step-circle">2</div>
                        <h5 class="fw-bold mb-2">Set Up Your School</h5>
                        <p class="text-muted" style="font-size:14px;line-height:1.8;">Add classes, subjects, students,
                            fee types, and staff. Assign teachers to subjects and set the current term.</p>
                    </div>
                    <div class="col-12 col-md-4 reveal reveal-delay-3">
                        <div class="step-circle">3</div>
                        <h5 class="fw-bold mb-2">Run Your School</h5>
                        <p class="text-muted" style="font-size:14px;line-height:1.8;">Record payments, upload results,
                            generate report cards, track defaulters — everything in one place.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Testimonials ────────────────────────────────────────────── -->
        <section class="section-gap-bg">
            <div class="container">
                <div class="text-center mb-6 reveal">
                    <div class="section-label">Testimonials</div>
                    <h2 class="section-title">Trusted by school owners</h2>
                </div>
                <div class="row g-4">
                    <div class="col-12 col-md-4 reveal reveal-delay-1">
                        <div class="testimonial-card">
                            <div class="mb-3" style="color:#f59f00;"><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                            <p>"MahkhalHub transformed how we manage our school. Payment tracking alone saved us hours
                                every week. The report card generation is simply brilliant."</p>
                            <div class="d-flex align-items-center gap-3 mt-4">
                                <div class="testimonial-avatar">A</div>
                                <div>
                                    <div class="fw-bold" style="font-size:14px;">Adaeze Okonkwo</div>
                                    <div style="font-size:12px;color:#9ca3af;">Proprietress, Starlight Academy</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 reveal reveal-delay-2">
                        <div class="testimonial-card">
                            <div class="mb-3" style="color:#f59f00;"><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                            <p>"As a school admin I can now see exactly which students are owing and which fees are
                                compulsory vs optional. The CSV upload for results is a huge time saver."</p>
                            <div class="d-flex align-items-center gap-3 mt-4">
                                <div class="testimonial-avatar" style="background:#198754;">B</div>
                                <div>
                                    <div class="fw-bold" style="font-size:14px;">Babatunde Adeyemi</div>
                                    <div style="font-size:12px;color:#9ca3af;">Admin, Royale Schools</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 reveal reveal-delay-3">
                        <div class="testimonial-card">
                            <div class="mb-3" style="color:#f59f00;"><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                            <p>"I manage 3 schools from one super-admin panel. The subscription management and
                                auto-suspension feature keeps everything organised without manual work."</p>
                            <div class="d-flex align-items-center gap-3 mt-4">
                                <div class="testimonial-avatar" style="background:#6f42c1;">C</div>
                                <div>
                                    <div class="fw-bold" style="font-size:14px;">Chukwuemeka Eze</div>
                                    <div style="font-size:12px;color:#9ca3af;">Owner, EduGroup Nigeria</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Pricing ─────────────────────────────────────────────────── -->
        <section class="section-gap" id="pricing">
            <div class="container">
                <div class="text-center mb-6 reveal">
                    <div class="section-label">Pricing</div>
                    <h2 class="section-title">Simple, transparent pricing</h2>
                    <p class="section-sub">Choose a subscription plan that works for your school size.</p>
                </div>
                <div class="row g-4 justify-content-center">
                    <div class="col-12 col-md-6 col-lg-4 reveal reveal-delay-1">
                        <div class="pricing-card h-100">
                            <div class="pricing-card-body">
                                <h5 class="fw-bold text-uppercase mb-1" style="letter-spacing:1px;">Monthly</h5>
                                <small class="text-muted">Flexible, cancel anytime</small>
                                <div class="d-flex align-items-end gap-1 my-4"><span class="fw-bold fs-4">₦</span>
                                    <div class="pricing-price">5,000</div><span class="text-muted mb-2">/mo</span>
                                </div>
                                <a href="/register" class="btn btn-outline-primary w-100 mb-4">Get Started</a>
                                <div>
                                    <div class="pricing-feature"><i class="bi bi-check-circle-fill text-success"></i>
                                        Unlimited students</div>
                                    <div class="pricing-feature"><i class="bi bi-check-circle-fill text-success"></i>
                                        All modules included</div>
                                    <div class="pricing-feature"><i class="bi bi-check-circle-fill text-success"></i>
                                        PDF report cards</div>
                                    <div class="pricing-feature"><i class="bi bi-check-circle-fill text-success"></i>
                                        CSV result upload</div>
                                    <div class="pricing-feature"><i class="bi bi-x-circle text-muted"></i> Priority
                                        support</div>
                                    <div class="pricing-feature"><i class="bi bi-x-circle text-muted"></i> Dedicated
                                        onboarding</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 reveal reveal-delay-2">
                        <div class="pricing-card featured h-100">
                            <div class="pricing-card-body">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h5 class="fw-bold text-uppercase mb-0" style="letter-spacing:1px;">Termly</h5>
                                    <span class="badge" style="background:var(--primary);font-size:11px;">Most
                                        Popular</span>
                                </div>
                                <small class="text-muted">3 months — save 10%</small>
                                <div class="d-flex align-items-end gap-1 my-4"><span class="fw-bold fs-4">₦</span>
                                    <div class="pricing-price" style="color:var(--primary);">15,000</div><span
                                        class="text-muted mb-2">/term</span>
                                </div>
                                <a href="/register" class="btn btn-primary w-100 mb-4">Get Started</a>
                                <div>
                                    <div class="pricing-feature"><i class="bi bi-check-circle-fill text-success"></i>
                                        Unlimited students</div>
                                    <div class="pricing-feature"><i class="bi bi-check-circle-fill text-success"></i>
                                        All modules included</div>
                                    <div class="pricing-feature"><i class="bi bi-check-circle-fill text-success"></i>
                                        PDF report cards</div>
                                    <div class="pricing-feature"><i class="bi bi-check-circle-fill text-success"></i>
                                        CSV result upload</div>
                                    <div class="pricing-feature"><i class="bi bi-check-circle-fill text-success"></i>
                                        Priority support</div>
                                    <div class="pricing-feature"><i class="bi bi-x-circle text-muted"></i> Dedicated
                                        onboarding</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 reveal reveal-delay-3">
                        <div class="pricing-card h-100">
                            <div class="pricing-card-body">
                                <h5 class="fw-bold text-uppercase mb-1" style="letter-spacing:1px;">Yearly</h5>
                                <small class="text-muted">12 months — save 25%</small>
                                <div class="d-flex align-items-end gap-1 my-4"><span class="fw-bold fs-4">₦</span>
                                    <div class="pricing-price">45,000</div><span class="text-muted mb-2">/yr</span>
                                </div>
                                <a href="/register" class="btn btn-outline-primary w-100 mb-4">Get Started</a>
                                <div>
                                    <div class="pricing-feature"><i class="bi bi-check-circle-fill text-success"></i>
                                        Unlimited students</div>
                                    <div class="pricing-feature"><i class="bi bi-check-circle-fill text-success"></i>
                                        All modules included</div>
                                    <div class="pricing-feature"><i class="bi bi-check-circle-fill text-success"></i>
                                        PDF report cards</div>
                                    <div class="pricing-feature"><i class="bi bi-check-circle-fill text-success"></i>
                                        CSV result upload</div>
                                    <div class="pricing-feature"><i class="bi bi-check-circle-fill text-success"></i>
                                        Priority support</div>
                                    <div class="pricing-feature"><i class="bi bi-check-circle-fill text-success"></i>
                                        Dedicated onboarding</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-5 reveal">
                    <p class="text-muted">Need a custom plan for multiple schools? <a href="#contact"
                            style="color:var(--primary);font-weight:700;">Contact us</a></p>
                </div>
            </div>
        </section>

        <!-- ── CTA ─────────────────────────────────────────────────────── -->
        <section class="section-gap reveal">
            <div class="cta-section text-center">
                <div class="container">
                    <span
                        style="font-size:12px;font-weight:800;letter-spacing:2px;text-transform:uppercase;opacity:.7;">Get
                        things done</span>
                    <h2 class="display-5 mt-3 mb-3">Ready to modernise your school?</h2>
                    <p class="lead mb-5" style="opacity:.85;max-width:500px;margin:0 auto 2rem;font-weight:400;">Join
                        hundreds of schools already using MahkhalHub to manage students, fees, and results effortlessly.
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="/register" class="btn btn-light fw-bold px-5 py-2" style="color:var(--primary);">Start
                            for Free</a>
                        <a href="#contact" class="btn btn-outline-light fw-bold px-5 py-2">Talk to Us</a>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- ── Footer ──────────────────────────────────────────────────── -->
    <footer id="contact">
        <div class="container">
            <div class="row g-5">
                <div class="col-12 col-lg-4">
                    <div class="footer-brand mb-3"><img
        src="{{ asset('admin-assets/images/logo/multischoollogo-removebg-preview.png') }}" width="80"
        alt=""></div>
                    <p style="font-size:14px;line-height:1.8;font-weight:400;">A powerful multi-school management
                        platform for proprietors, admins, and staff. Manage students, fees, results, and report cards —
                        all in one place.</p>
                    <div class="d-flex gap-3 mt-4" style="font-size:1.2rem;">
                        <a href="#" style="color:#9ca3af;display:inline;"><i class="bi bi-facebook"></i></a>
                        <a href="#" style="color:#9ca3af;display:inline;"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" style="color:#9ca3af;display:inline;"><i class="bi bi-instagram"></i></a>
                        <a href="#" style="color:#9ca3af;display:inline;"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <h5>Platform</h5>
                    <a href="#features">Features</a>
                    <a href="#pricing">Pricing</a>
                    <a href="#roles">Who It's For</a>
                    <a href="/login">Sign In</a>
                    <a href="/register">Register</a>
                </div>
                <div class="col-6 col-lg-2">
                    <h5>Support</h5>
                    <a href="#">Help Center</a>
                    <a href="#">Getting Started</a>
                    <a href="#">FAQs</a>
                    <a href="#">Contact Support</a>
                </div>
                <div class="col-12 col-lg-4">
                    <h5>Get in Touch</h5>
                    <p style="font-size:14px;">Have questions about Makhalhub? We're here to help.</p>
                    <p style="font-size:14px;margin-bottom:6px;"><i class="bi bi-envelope me-2"
                            style="color:var(--primary);"></i><a
                            href="mailto:hello@MahkhalHub.app">hello@makhalhub.app</a></p>
                    <p style="font-size:14px;margin-bottom:6px;"><i class="bi bi-whatsapp me-2"
                            style="color:#22c55e;"></i><a href="https://wa.me/2349011903753">+234 901 190 3753</a></p>
                    <p style="font-size:14px;"><i class="bi bi-geo-alt me-2" style="color:var(--primary);"></i>Nigeria
                    </p>
                </div>
            </div>
            <div class="row align-items-center border-top mt-5 pt-4" style="border-color:#1e3a5f !important;">
                <div class="col-12 col-md-6" style="font-size:13px;">&copy; <span id="year"></span> MahkhalHub. All
                    Rights Reserved.</div>
                <div class="col-12 col-md-6 d-md-flex justify-content-end gap-4 mt-2 mt-md-0">
                    <a href="#" style="font-size:13px;display:inline;">Privacy Policy</a>
                    <a href="#" style="font-size:13px;display:inline;">Terms of Use</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to top -->
    <div class="scroll-top" id="scrollTop"><i class="bi bi-arrow-up"></i></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();

        // Scroll to top
        const scrollBtn = document.getElementById('scrollTop');
        window.addEventListener('scroll', () => scrollBtn.classList.toggle('show', window.scrollY > 400));
        scrollBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const target = document.querySelector(a.getAttribute('href'));
                if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
            });
        });

        // Scroll reveal with IntersectionObserver
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.reveal, .reveal-left, .reveal-right').forEach(el => observer.observe(el));
    </script>

</body>

</html>