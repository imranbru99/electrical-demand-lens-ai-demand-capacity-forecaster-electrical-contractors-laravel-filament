<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ElectricalDemandLens — AI Demand & Capacity Forecaster for Electrical Contractors')</title>
    <meta name="description" content="@yield('meta_description', 'ElectricalDemandLens is the AI demand and capacity forecaster for electrical contractors. Forecast weekly workload, plan electrician resources, approve AI drafts, and keep audit-ready staffing decisions.')">
    <meta name="keywords" content="electrical demand forecasting, electrical capacity planning, electrician workload forecaster, ServiceTitan capacity, Jobber demand planning, AI electrical automation, Laravel Filament SaaS">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,700;1,9..40,400&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #0b1c2c;
            --muted: #4d6478;
            --ice: #e8f4fb;
            --sky: #1f8ecf;
            --sky-deep: #0d6ea8;
            --mint: #1fa97a;
            --sand: #f7fafc;
            --line: #c9dce8;
            --card: #ffffff;
            --warn: #d97706;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "DM Sans", system-ui, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(1200px 500px at 10% -10%, #cfeaf8 0%, transparent 55%),
                radial-gradient(900px 420px at 100% 0%, #d9f3ea 0%, transparent 50%),
                linear-gradient(180deg, #f4f9fc 0%, #eef5f9 40%, #f7fafc 100%);
            min-height: 100vh;
        }
        a { color: var(--sky-deep); text-decoration: none; }
        .wrap { width: min(1120px, calc(100% - 2rem)); margin: 0 auto; }
        header.site {
            position: sticky; top: 0; z-index: 20;
            backdrop-filter: blur(10px);
            background: rgba(247, 250, 252, 0.85);
            border-bottom: 1px solid rgba(201, 220, 232, 0.7);
        }
        .nav {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.95rem 0; gap: 1rem;
        }
        .brand {
            font-family: Outfit, sans-serif; font-weight: 800; font-size: 1.25rem;
            letter-spacing: -0.03em; color: var(--ink);
        }
        .brand span { color: var(--sky-deep); }
        .nav-links { display: flex; flex-wrap: wrap; gap: 0.85rem 1.1rem; align-items: center; }
        .nav-links a { color: var(--muted); font-weight: 500; font-size: 0.95rem; }
        .nav-links a:hover { color: var(--ink); }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 999px; padding: 0.7rem 1.2rem; font-weight: 700;
            border: 0; cursor: pointer; transition: transform .15s ease, background .15s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary { background: linear-gradient(135deg, var(--sky), var(--sky-deep)); color: #fff; }
        .btn-ghost { background: #fff; color: var(--ink); border: 1px solid var(--line); }
        .hero {
            padding: 4.5rem 0 3rem;
            display: grid; gap: 2rem;
            grid-template-columns: 1.15fr 0.85fr;
            align-items: center;
        }
        .eyebrow {
            display: inline-block; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.08em;
            text-transform: uppercase; color: var(--sky-deep); margin-bottom: 0.85rem;
        }
        h1, h2, h3 { font-family: Outfit, sans-serif; letter-spacing: -0.03em; margin: 0 0 0.75rem; }
        h1 { font-size: clamp(2.2rem, 4vw, 3.4rem); line-height: 1.05; }
        h2 { font-size: clamp(1.6rem, 2.5vw, 2.2rem); }
        .lede { font-size: 1.1rem; color: var(--muted); line-height: 1.6; max-width: 38rem; }
        .cta-row { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 1.5rem; }
        .panel {
            background: linear-gradient(160deg, #0d6ea8, #0b3d5c 55%, #0f766e);
            color: #fff; border-radius: 1.5rem; padding: 1.5rem; min-height: 280px;
            box-shadow: 0 20px 50px rgba(13, 110, 168, 0.25);
            position: relative; overflow: hidden;
        }
        .panel::after {
            content: ""; position: absolute; inset: auto -20% -30% 20%; height: 70%;
            background: radial-gradient(circle, rgba(255,255,255,.18), transparent 60%);
        }
        .panel h3 { color: #fff; font-size: 1.2rem; margin-bottom: 1rem; }
        .slot {
            background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
            border-radius: 0.9rem; padding: 0.85rem 1rem; margin-bottom: 0.65rem;
        }
        .slot strong { display: block; font-size: 0.95rem; }
        .slot span { font-size: 0.82rem; opacity: 0.85; }
        .section { padding: 3rem 0; }
        .grid-3 { display: grid; gap: 1rem; grid-template-columns: repeat(3, 1fr); }
        .grid-2 { display: grid; gap: 1rem; grid-template-columns: repeat(2, 1fr); }
        .feature {
            background: var(--card); border: 1px solid var(--line); border-radius: 1.1rem;
            padding: 1.25rem 1.3rem; box-shadow: 0 8px 24px rgba(11, 28, 44, 0.04);
        }
        .feature h3 { font-size: 1.1rem; }
        .feature p { margin: 0; color: var(--muted); line-height: 1.55; }
        .pill {
            display: inline-flex; align-items: center; gap: 0.35rem;
            border-radius: 999px; padding: 0.2rem 0.65rem; font-size: 0.75rem; font-weight: 700;
        }
        .on { background: #d1fae5; color: #065f46; }
        .off { background: #e2e8f0; color: #475569; }
        .table {
            width: 100%; border-collapse: collapse; background: #fff;
            border-radius: 1rem; overflow: hidden; border: 1px solid var(--line);
        }
        .table th, .table td { text-align: left; padding: 0.85rem 1rem; border-bottom: 1px solid var(--line); }
        .table th { background: var(--ice); font-size: 0.85rem; }
        .price {
            background: #fff; border: 1px solid var(--line); border-radius: 1.25rem; padding: 1.5rem;
        }
        .price .amt { font-family: Outfit, sans-serif; font-size: 2.2rem; font-weight: 800; }
        footer.site {
            margin-top: 3rem; border-top: 1px solid var(--line);
            padding: 2rem 0 2.5rem; color: var(--muted); font-size: 0.95rem;
        }
        .steps { counter-reset: step; display: grid; gap: 1rem; }
        .step {
            background: #fff; border: 1px solid var(--line); border-radius: 1rem; padding: 1.1rem 1.2rem 1.1rem 3.4rem;
            position: relative;
        }
        .step::before {
            counter-increment: step; content: counter(step);
            position: absolute; left: 1rem; top: 1rem; width: 1.7rem; height: 1.7rem;
            border-radius: 999px; background: var(--sky); color: #fff;
            display: grid; place-items: center; font-weight: 800; font-size: 0.85rem;
        }
        @media (max-width: 900px) {
            .hero, .grid-3, .grid-2 { grid-template-columns: 1fr; }
            .nav { flex-direction: column; align-items: flex-start; }
        }
    </style>
    @stack('head')
</head>
<body>
<header class="site">
    <div class="wrap nav">
        <a class="brand" href="{{ route('home') }}">Electrical<span>DemandLens</span></a>
        <nav class="nav-links">
            <a href="{{ route('features') }}">Features</a>
            <a href="{{ route('modules') }}">Modules</a>
            <a href="{{ route('how-it-works') }}">How it works</a>
            <a href="{{ route('pricing') }}">Pricing</a>
            <a href="{{ route('demo') }}">Demo users</a>
            <a class="btn btn-primary" href="/admin">Open Admin</a>
        </nav>
    </div>
</header>
<main>
    @yield('content')
</main>
<footer class="site">
    <div class="wrap">
        <strong>ElectricalDemandLens</strong> — AI Demand &amp; Capacity Forecaster for Electrical Contractors.<br>
        Built with Laravel + Filament · Modular SaaS · Docker-ready<br>
        Contact: <a href="https://imrandev.bd/contact">https://imrandev.bd/contact</a> · Imran Ahmed (IMRAN Dev BD)
    </div>
</footer>
</body>
</html>
