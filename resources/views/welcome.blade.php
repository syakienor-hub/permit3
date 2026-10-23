<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safety Permit PT INKA (Persero) — Sistem Monitoring K3 Kontraktor</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/images/logok3.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --navy-950: #0d1526;
            --navy-900: #122040;
            --navy-800: #1a2c52;
            --navy-700: #243a68;
            --steel-500: #5b6b8c;
            --steel-300: #a7b1c9;
            --paper: #f5f6f9;
            --card: #ffffff;
            --ink-900: #141a2b;
            --ink-600: #4b5470;
            --amber-500: #f5a623;
            --amber-600: #d9861a;
            --amber-100: #fdf1da;
            --teal-500: #1f9d8a;
            --border: #e4e7ef;
        }

        html { scroll-behavior: smooth; }

        [x-cloak] { display: none !important; }

        ::selection { background: var(--amber-500); color: var(--navy-950); }

        .nav {
            position: sticky; top: 0; z-index: 50;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
        }

        .hero {
            position: relative;
            background: linear-gradient(180deg, rgba(13,21,38,.5), rgba(18,32,64,.6)), url('{{ asset('assets/images/bg-landingpage.jpeg') }}');
            background-size: cover;
            background-position: center 58%;
            color: #fff; overflow: hidden; padding: 60px 0; text-align: center;
        }

        /* HP saja: pakai hero versi ringan (~87KB WebP vs 1.7MB JPEG).
           Laptop/desktop tetap pakai bg-landingpage.jpeg asli di atas. */
        @media (max-width: 767px) {
            .hero {
                background-image: linear-gradient(180deg, rgba(13,21,38,.5), rgba(18,32,64,.6)), url('{{ asset('assets/images/bg-landingpage-mobile.webp') }}');
            }
            /* Matikan efek GPU berat hanya di HP */
            .nav {
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
                background: #fff;
            }
            .hero::before { display: none; }
            .eyebrow .dot { animation: none; }
            .reveal, .reveal-left, .reveal-right, .reveal-scale, .hero-animate {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
            .flow-step:hover .sn { transform: none; }
            .about-stat:hover { transform: none; box-shadow: none; }
            .btn-amber:hover { transform: none; }
        }

        @media (min-width: 768px) {
            .hero { padding: 86px 0; }
        }

        .hero::before {
            content: ''; position: absolute; inset: 0;
            background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: radial-gradient(ellipse 70% 70% at 50% 30%, #000 40%, transparent 90%);
            -webkit-mask-image: radial-gradient(ellipse 70% 70% at 50% 30%, #000 40%, transparent 90%);
            pointer-events: none;
        }

        .hero-inner { position: relative; z-index: 1; }

        .eyebrow {
            display: inline-flex; align-items: center; gap: 7px;
            color: #fff; font-size: 12px; font-weight: 600;
            letter-spacing: .02em;
        }

        .eyebrow .dot {
            width: 6px; height: 6px; border-radius: 50%; background: var(--teal-500);
            box-shadow: 0 0 0 3px rgba(31,157,138,.25);
            animation: pulse-dot 2s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { box-shadow: 0 0 0 3px rgba(31,157,138,.25); }
            50% { box-shadow: 0 0 0 6px rgba(31,157,138,.1); }
        }

        .sec-tag {
            display: inline-flex; align-items: center; gap: 7px;
            font-size: 11.5px; font-weight: 700; color: var(--amber-600);
            text-transform: uppercase; letter-spacing: .06em; margin-bottom: 12px;
        }

        .sec-tag::before {
            content: ''; width: 16px; height: 2px; background: var(--amber-500);
        }

        .flow { position: relative; }

        .flow-line {
            position: absolute; top: 24px; left: 9%; right: 9%; height: 2px;
            background: repeating-linear-gradient(90deg, var(--border) 0 8px, transparent 8px 14px);
            z-index: 0; 
        }

        .flow-step { text-align: center; }

        .flow-step .sn {
            width: 48px; height: 48px; border-radius: 50%; background: var(--navy-900); color: #fff;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;
            font-weight: 700; font-size: 16px;
            border: 4px solid var(--paper);
            transition: transform .3s cubic-bezier(.16,1,.3,1), background .3s ease;
        }

        .flow-step:hover .sn {
            transform: scale(1.1);
        }

        .flow-step:nth-child(5) .sn { background: var(--amber-500); color: var(--navy-950); }

        .flow-step h4 { font-size: 13.5px; font-weight: 700; margin-bottom: 7px; color: var(--navy-950); }
        .flow-step p { font-size: 12px; color: var(--ink-600); line-height: 1.5; }

        .flow-note {
            display: flex; gap: 12px; align-items: flex-start;
            background: var(--amber-100); border: 1px solid #f0d090;
            border-radius: 11px; padding: 16px 20px; margin-top: 44px;
            font-size: 13px; color: #7a4d0d; line-height: 1.6;
        }

        .flow-note strong { color: #5a3a08; }

        .about-visual {
            background: var(--paper); border: 1px solid var(--border); border-radius: 16px;
            padding: 32px; position: relative; overflow: hidden;
        }

        .about-visual::before {
            content: ''; position: absolute; top: -30px; right: -30px; width: 150px; height: 150px;
            background: repeating-linear-gradient(135deg, var(--amber-500) 0 12px, transparent 12px 24px);
            opacity: .12;
        }

        .about-stat {
            background: #fff; border: 1px solid var(--border); border-radius: 11px; padding: 18px;
            transition: transform .3s cubic-bezier(.16,1,.3,1), box-shadow .3s ease;
        }

        .about-stat:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,.06);
        }

        .about-stat .v { font-weight: 700; font-size: 26px; color: var(--navy-950); }
        .about-stat .l { font-size: 11.5px; color: var(--ink-600); margin-top: 4px; }

        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 7px;
            border-radius: 9px; padding: 10px 18px; font-size: 13.5px; font-weight: 600;
            cursor: pointer; border: 1.5px solid transparent; transition: all .2s;
            font-family: inherit;
        }

        .btn-ghost { color: var(--navy-950); border-color: var(--border); background: #fff; }
        .btn-ghost:hover { border-color: var(--navy-700); }
        .btn-solid { background: var(--navy-900); color: #fff; }
        .btn-solid:hover { background: var(--navy-800); }
        .btn-amber { background: var(--amber-500); color: var(--navy-950); }
        .btn-amber:hover { background: var(--amber-600); transform: translateY(-1px); }
        .btn-lg { padding: 13px 24px; font-size: 14.5px; }

        .btn-outline-light {
            background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.22); color: #fff;
            backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
        }
        .btn-outline-light:hover {
            background: rgba(255,255,255,.22); border-color: rgba(255,255,255,.5);
            transform: translateY(-1px);
        }

        /* Scroll Animations */
        .reveal {
            opacity: 0; transform: translateY(30px);
            transition: all .7s cubic-bezier(.16,1,.3,1);
        }
        .reveal.is-visible {
            opacity: 1; transform: translateY(0);
        }
        .reveal-left {
            opacity: 0; transform: translateX(-30px);
            transition: all .7s cubic-bezier(.16,1,.3,1);
        }
        .reveal-left.is-visible {
            opacity: 1; transform: translateX(0);
        }
        .reveal-right {
            opacity: 0; transform: translateX(30px);
            transition: all .7s cubic-bezier(.16,1,.3,1);
        }
        .reveal-right.is-visible {
            opacity: 1; transform: translateX(0);
        }
        .reveal-scale {
            opacity: 0; transform: scale(.92);
            transition: all .7s cubic-bezier(.16,1,.3,1);
        }
        .reveal-scale.is-visible {
            opacity: 1; transform: scale(1);
        }

        /* Hero entrance animations */
        .hero-animate {
            opacity: 0; transform: translateY(20px);
        }
        .hero-visible {
            animation: heroFadeUp .6s cubic-bezier(.16,1,.3,1) forwards;
        }
        @keyframes heroFadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 860px) {
            .flow-grid { grid-template-columns: repeat(2,1fr); gap: 28px; }
            .flow-line { display: none; }
            .about-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 560px) {
            .flow-grid { grid-template-columns: 1fr; }
            .about-stats { grid-template-columns: 1fr 1fr; gap: 12px; }
            .flow-note { margin-top: 28px; padding: 14px 16px; font-size: 12.5px; }
            .about-visual { padding: 20px; }
            .btn-lg { width: 100%; }
        }

        /* Staggered animation delays for flow steps */
        .flow-step:nth-child(1) .sn { transition-delay: 0ms; }
        .flow-step:nth-child(2) .sn { transition-delay: 80ms; }
        .flow-step:nth-child(3) .sn { transition-delay: 160ms; }
        .flow-step:nth-child(4) .sn { transition-delay: 240ms; }
        .flow-step:nth-child(5) .sn { transition-delay: 320ms; }
    </style>
</head>
<body class="bg-[var(--paper)] text-[var(--ink-900)]">

<!-- ================= HEADER ================= -->
<header class="nav" x-data="{ mobileOpen: false, monitoringOpen: false, activeNav: window.location.hash ? window.location.hash.substring(1) : null }">
    <div class="max-w-[1080px] mx-auto px-4 md:px-6 flex items-center h-[60px] md:h-[68px] gap-3">
        <div class="flex items-center gap-2.5 mr-auto min-w-0">
            <img src="{{ asset('assets/images/logoinka.svg') }}" alt="INKA" class="h-6 md:h-7 w-auto shrink-0">
            <div class="border-l border-[var(--border)] pl-2.5 md:pl-3 whitespace-nowrap">
                <span class="text-[13px] md:text-sm font-bold">SIMONIKA</span>
                <span class="block text-[9px] md:text-[10px] font-semibold text-[var(--ink-600)] tracking-wide">PT INKA (Persero)</span>
            </div>
        </div>
        <div class="hidden lg:flex items-center gap-5 mr-3 text-[13px] xl:text-sm font-semibold text-[var(--ink-600)]">
            <div class="relative" @click.outside="monitoringOpen = false" @mouseleave="monitoringOpen = false">
                <button type="button" @click="monitoringOpen = !monitoringOpen" @keydown.escape.window="monitoringOpen = false"
                    class="hover:text-[var(--navy-950)] transition-colors flex items-center gap-1.5 whitespace-nowrap"
                    :class="(activeNav === 'monitoring' || activeNav === 'monitoring-kontraktor') ? 'text-inka-navy font-bold' : ''"
                    :aria-expanded="monitoringOpen ? 'true' : 'false'">
                    <span x-show="activeNav === 'monitoring' || activeNav === 'monitoring-kontraktor'" class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Monitoring
                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="monitoringOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="monitoringOpen" x-cloak class="absolute left-0 top-full w-60 pt-2 z-50">
                    <div class="rounded-xl border border-[var(--border)] bg-white p-1.5 shadow-xl">
                        <a href="#monitoring" @click="activeNav = 'monitoring'; monitoringOpen = false"
                            class="flex items-center gap-2 rounded-lg px-3 py-2 text-[13px] text-[var(--ink-600)] hover:bg-gray-50 hover:text-[var(--navy-950)] transition-colors"
                            :class="activeNav === 'monitoring' ? 'text-inka-navy font-bold' : ''">
                            <span class="w-2 h-2 rounded-full bg-emerald-500" :class="activeNav === 'monitoring' ? 'animate-pulse' : 'opacity-40'"></span>
                            Monitoring Permit
                        </a>
                        <a href="#monitoring-kontraktor" @click="activeNav = 'monitoring-kontraktor'; monitoringOpen = false"
                            class="flex items-center gap-2 rounded-lg px-3 py-2 text-[13px] text-[var(--ink-600)] hover:bg-gray-50 hover:text-[var(--navy-950)] transition-colors"
                            :class="activeNav === 'monitoring-kontraktor' ? 'text-inka-navy font-bold' : ''">
                            <span class="w-2 h-2 rounded-full bg-emerald-500" :class="activeNav === 'monitoring-kontraktor' ? 'animate-pulse' : 'opacity-40'"></span>
                            Monitoring Kontraktor
                        </a>
                    </div>
                </div>
            </div>
            <a href="/data-kontraktor" class="hover:text-[var(--navy-950)] transition-colors whitespace-nowrap">Data Kontraktor</a>
            <a href="#alur" @click="activeNav = 'alur'" class="hover:text-[var(--navy-950)] transition-colors whitespace-nowrap"
                :class="activeNav === 'alur' ? 'text-inka-navy font-bold' : ''">Alur Pengajuan</a>
            <a href="#tentang" @click="activeNav = 'tentang'" class="hover:text-[var(--navy-950)] transition-colors whitespace-nowrap"
                :class="activeNav === 'tentang' ? 'text-inka-navy font-bold' : ''">Tentang Kami</a>
        </div>
        <div class="hidden sm:flex items-center gap-2.5 shrink-0">
            <a href="/ajukan-permit" class="text-[13.5px] font-semibold text-[var(--navy-950)] hover:text-[var(--amber-600)] transition-colors whitespace-nowrap">Ajukan Permit</a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-solid lg:translate-x-2">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-solid lg:translate-x-2">Masuk</a>
            @endauth
        </div>
        <button type="button" @click="mobileOpen = !mobileOpen" class="lg:hidden w-10 h-10 -mr-1 flex items-center justify-center rounded-lg hover:bg-gray-100 text-[var(--navy-950)] shrink-0" aria-label="Buka menu">
            <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <div x-show="mobileOpen" x-cloak class="lg:hidden border-t border-[var(--border)] bg-white px-4 pt-2 pb-4">
        <nav class="flex flex-col text-sm font-semibold text-[var(--ink-600)]">
            <a href="#monitoring" @click="activeNav = 'monitoring'; mobileOpen = false" class="flex items-center gap-2 py-3 border-b border-gray-100"
                :class="activeNav === 'monitoring' ? 'text-inka-navy font-bold' : ''">
                <span x-show="activeNav === 'monitoring'" class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Monitoring Permit
            </a>
            <a href="#monitoring-kontraktor" @click="activeNav = 'monitoring-kontraktor'; mobileOpen = false" class="flex items-center gap-2 py-3 border-b border-gray-100"
                :class="activeNav === 'monitoring-kontraktor' ? 'text-inka-navy font-bold' : ''">
                <span x-show="activeNav === 'monitoring-kontraktor'" class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Monitoring Kontraktor
            </a>
            <a href="/data-kontraktor" @click="mobileOpen = false" class="py-3 border-b border-gray-100">Ajukan Data Kontraktor</a>
            <a href="#alur" @click="activeNav = 'alur'; mobileOpen = false" class="py-3 border-b border-gray-100"
                :class="activeNav === 'alur' ? 'text-inka-navy font-bold' : ''">Alur Pengajuan</a>
            <a href="#tentang" @click="activeNav = 'tentang'; mobileOpen = false" class="py-3 border-b border-gray-100"
                :class="activeNav === 'tentang' ? 'text-inka-navy font-bold' : ''">Tentang Kami</a>
        </nav>
        <div class="flex flex-col gap-2.5 mt-3 sm:hidden">
            <a href="/ajukan-permit" class="text-sm font-semibold text-[var(--navy-950)] text-center py-2">Ajukan Permit</a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-solid btn-lg w-full">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-solid btn-lg w-full">Masuk</a>
            @endauth
        </div>
    </div>
</header>

<!-- ================= HERO ================= -->
<section class="hero">
    <div class="max-w-[660px] mx-auto px-4 md:px-6 hero-inner">
        <div class="inline-flex flex-wrap items-center justify-center gap-x-3 gap-y-1.5 rounded-full border border-white/25 bg-white/10 px-4 py-2 mb-5 hero-animate backdrop-blur-md">
            <span class="eyebrow" id="hero-eyebrow"><span class="dot"></span> SISTEM ONLINE · MONITORING REALTIME</span>
            <span class="hidden sm:block h-4 w-px bg-white/25"></span>
            <span id="hero-realtime" class="inline-flex items-center gap-2 whitespace-nowrap text-[11px] sm:text-xs font-medium text-white/90">
                <span id="hero-realtime-date">Memuat tanggal…</span>
                <span class="text-white/40">•</span>
                <span id="hero-realtime-time" class="tabular-nums font-semibold text-white">--:--:--</span>
                <span class="text-[10px] font-semibold text-white/60">WIB</span>
            </span>
        </div>
        <h1 class="text-[30px] sm:text-[38px] md:text-[50px] leading-[1.2] font-extrabold tracking-[-.01em] mb-4 hero-animate" id="hero-title">
            SIMONIKA  <span class="text-[var(--amber-500)]"> </span>
        </h1>
        <p class="text-sm sm:text-[15.5px] text-white/100 mb-6 md:mb-8 hero-animate px-1" id="hero-sub">
        Sistem Informasi Monitoring dan Izin Kerja PT INKA (Persero)        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center items-stretch sm:items-center sm:flex-wrap hero-animate px-2 sm:px-0" id="hero-actions">
            <a href="/ajukan-permit" class="btn btn-outline-light btn-lg w-full sm:w-auto">
                Ajukan Safety Permit
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-lg w-full sm:w-auto">
                    Ke Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg w-full sm:w-auto">
                    Masuk Petugas
                </a>
            @endauth
            <a href="/data-kontraktor" class="btn btn-outline-light btn-lg w-full sm:w-auto">
                Ajukan Data Kontraktor
            </a>
            <a href="/cek-permit" class="btn btn-outline-light btn-lg w-full sm:w-auto">
                Cek Status
            </a>
        </div>
        <p class="text-xs text-white/100 mt-6 md:mt-[36px] hero-animate px-2" id="hero-note">
            Tanpa akun. Isi form pengajuan langsung — permit otomatis masuk verifikasi Staff HSE.
        </p>
    </div>
</section>

<section class="pt-6 pb-12 md:pt-8 md:pb-16 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 md:px-6">
        <p class="text-center italic text-black-500 mb-8 md:mb-10 text-sm md:text-base px-2">
            Pastikan setiap pekerjaan dimulai dengan izin yang tervalidasi demi lingkungan kerja yang aman dan tertib.
        </p>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 md:gap-6">
            <div>
                <div class="overflow-hidden rounded-lg shadow-sm">
                    <picture>
                        <source media="(max-width: 767px)" srcset="{{ asset('assets/images/brief1-mobile.webp') }}" type="image/webp">
                        <img src="{{ asset('assets/images/brief1.jpeg') }}" alt="Galeri 1" loading="lazy" decoding="async" class="w-full h-32 sm:h-40 object-cover [object-position:100%_80%] md:scale-110 md:hover:scale-125 md:transition-transform md:duration-300">
                    </picture>
                </div>
                <p class="text-center text-sm font-medium text-gray-700 mt-3">Safety Briefing</p>
            </div>
            <div>
                <div class="overflow-hidden rounded-lg shadow-sm">
                    <picture>
                        <source media="(max-width: 767px)" srcset="{{ asset('assets/images/brief2-mobile.webp') }}" type="image/webp">
                        <img src="{{ asset('assets/images/brief2.jpeg') }}" alt="Galeri 2" loading="lazy" decoding="async" class="w-full h-32 sm:h-40 object-cover [object-position:70 10%_20%] md:scale-110 md:hover:scale-125 md:transition-transform md:duration-300">
                    </picture>
                </div>
                <p class="text-center text-sm font-medium text-gray-700 mt-3"> Safety Induction </p>
            </div>
            <div>
                <div class="overflow-hidden rounded-lg shadow-sm">
                    <picture>
                        <source media="(max-width: 767px)" srcset="{{ asset('assets/images/brief3-mobile.webp') }}" type="image/webp">
                        <img src="{{ asset('assets/images/brief3.jpeg') }}" alt="Galeri 3" loading="lazy" decoding="async" class="w-full h-32 sm:h-40 object-cover [object-position:center_80%] md:scale-110 md:hover:scale-125 md:transition-transform md:duration-300">
                    </picture>
                </div>
                <p class="text-center text-sm font-medium text-gray-700 mt-3">Audit Eksternal</p>
            </div>
            <div>
                <div class="overflow-hidden rounded-lg shadow-sm">
                    <picture>
                        <source media="(max-width: 767px)" srcset="{{ asset('assets/images/brief4-mobile.webp') }}" type="image/webp">
                        <img src="{{ asset('assets/images/brief4.jpeg') }}" alt="Galeri 4" loading="lazy" decoding="async" class="w-full h-32 sm:h-40 object-cover md:scale-110 md:hover:scale-125 md:transition-transform md:duration-300">
                    </picture>
                </div>
                <p class="text-center text-sm font-medium text-gray-700 mt-3">Lean Manufacturing</p>
            </div>
            <div>
                <div class="overflow-hidden rounded-lg shadow-sm">
                    <picture>
                        <source media="(max-width: 767px)" srcset="{{ asset('assets/images/brief5-mobile.webp') }}" type="image/webp">
                        <img src="{{ asset('assets/images/brief5.jpeg') }}" alt="Galeri 5" loading="lazy" decoding="async" class="w-full h-32 sm:h-40 object-cover [object-position:100%_80%] md:scale-110 md:hover:scale-125 md:transition-transform md:duration-300">
                    </picture>
                </div>
                <p class="text-center text-sm font-medium text-gray-700 mt-3">Safety Patrol</p>
            </div>
            <div>
                <div class="overflow-hidden rounded-lg shadow-sm">
                    <picture>
                        <source media="(max-width: 767px)" srcset="{{ asset('assets/images/brief6-mobile.webp') }}" type="image/webp">
                        <img src="{{ asset('assets/images/brief6.jpeg') }}" alt="Galeri 6" loading="lazy" decoding="async" class="w-full h-32 sm:h-40 object-cover [object-position:38%_100%] md:scale-110 md:hover:scale-125 md:transition-transform md:duration-300">
                    </picture>
                </div>
                <p class="text-center text-sm font-medium text-gray-700 mt-3">Patroli 5R</p>
            </div>
        </div>
    </div>
</section>

<!-- ================= MONITORING PERMIT TERKINI ================= -->
<section class="py-12 md:py-20 px-4 md:px-6 bg-[var(--paper)]" id="monitoring" x-data="{
    isModalOpen: false,
    activePermit: {
        no_permit: '',
        site: '',
        tipe: '',
        nama_pekerjaan: '',
        divisi: '',
        kontraktor: '',
        lokasi: '',
        tanggal_permit: '',
        penanggung_jawab: '',
        status: '',
        status_badge: '',
        klasifikasi: []
    },
    openModal(data) {
        this.activePermit = data;
        this.isModalOpen = true;
        document.body.style.overflow = 'hidden';
    },
    closeModal() {
        this.isModalOpen = false;
        document.body.style.overflow = '';
    }
}">
    <div class="max-w-[1140px] mx-auto">
        <div class="max-w-[640px] mx-auto text-center mb-10 reveal">
            <div class="sec-tag inline-flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Monitoring Real-Time
            </div>
            <h2 class="text-[22px] sm:text-[28px] font-extrabold tracking-[-.01em] mb-3 text-[var(--navy-950)]">
                Monitoring Izin Kerja (Safety Permit)
            </h2>
            <p class="text-[14.5px] text-[var(--ink-600)]">
                Pantau daftar izin kerja risiko tinggi di lingkungan PT INKA (Persero) secara transparan dan mudah.
            </p>
        </div>

        <!-- Table Card -->
        <div class="table-card reveal">
            <div class="px-4 md:px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-inka-navy border border-blue-100 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5 text-inka-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Daftar Permit Kerja Terkini</h3>
                        <p class="text-xs text-gray-500">Menampilkan 5 permit terbaru dengan update berkala</p>
                    </div>
                </div>
                <div class="text-xs font-semibold text-gray-500 bg-white px-3 py-1.5 rounded-lg border border-gray-200">
                    Total: <span class="text-inka-navy font-bold">{{ $permits->total() }}</span> Permit Terdaftar
                </div>
            </div>
            <form method="GET" action="{{ url('/') }}#monitoring" class="px-4 md:px-6 py-3 border-b border-gray-100 bg-white flex flex-wrap items-center gap-x-3 gap-y-2">
                <label class="text-xs font-semibold text-gray-500">Filter site:</label>
                <select name="site" onchange="this.form.submit()" class="text-xs font-semibold border border-gray-200 rounded-lg px-3 py-1.5 text-gray-700 focus:outline-none focus:border-inka-navy">
                    <option value="">Semua Site</option>
                    @foreach(\App\Models\Permit::SITES as $s)
                        <option value="{{ $s }}" {{ request('site') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
                <label class="text-xs font-semibold text-gray-500">Filter tipe:</label>
                <select name="tipe" onchange="this.form.submit()" class="text-xs font-semibold border border-gray-200 rounded-lg px-3 py-1.5 text-gray-700 focus:outline-none focus:border-inka-navy">
                    <option value="">Semua Tipe</option>
                    <option value="Internal" {{ request('tipe') === 'Internal' ? 'selected' : '' }}>Internal</option>
                    <option value="Eksternal" {{ request('tipe') === 'Eksternal' ? 'selected' : '' }}>Eksternal</option>
                </select>
                @if(request('site') || request('tipe') || request('search'))
                <a href="{{ url('/') }}#monitoring" class="text-xs text-gray-400 hover:text-gray-600">Reset</a>
                @endif
                <div class="ml-auto flex items-center gap-2 w-full sm:w-auto">
                    <div class="relative flex-1 sm:flex-none">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" maxlength="100"
                               placeholder="Cari no. permit, pekerjaan, kontraktor..."
                               class="w-full sm:w-64 text-xs border border-gray-200 rounded-lg pl-8 pr-3 py-1.5 text-gray-700 placeholder-gray-400 focus:outline-none focus:border-inka-navy">
                    </div>
                    <button type="submit" class="text-xs font-semibold bg-inka-navy text-white px-3 py-1.5 rounded-lg hover:opacity-90 transition-opacity">Cari</button>
                </div>
            </form>

            @if($permits->isEmpty())
                <div class="py-16 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-sm font-semibold text-gray-600">
                        @if(request('search'))
                            Tidak ada permit yang cocok dengan pencarian "{{ request('search') }}".
                        @else
                            Belum ada permit kerja yang terdaftar.
                        @endif
                    </p>
                </div>
            @else
                {{-- Desktop/laptop: tabel tetap --}}
                <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0 hidden md:block">
                    <table class="data-table min-w-[1000px]">
                        <thead>
                            <tr>
                                <th>No. Permit</th>
                                <th>Site</th>
                                <th>Tipe</th>
                                <th>Klasifikasi Pekerjaan</th>
                                <th>Divisi</th>
                                <th>Kontraktor</th>
                                <th>Lokasi</th>
                                <th>Tanggal Permit</th>
                                <th>Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permits as $permit)
                            @php
                                $klasArr = [];
                                if ($permit->relationLoaded('classifications') && $permit->classifications->isNotEmpty()) {
                                    $klasArr = $permit->classifications->pluck('name')->toArray();
                                } elseif (!empty($permit->klasifikasi_pekerjaan) && is_array($permit->klasifikasi_pekerjaan)) {
                                    $nameMap = [
                                        'panas'          => 'Pekerjaan Panas',
                                        'ketinggian'     => 'Pekerjaan Ketinggian',
                                        'ruang_terbatas' => 'Ruang Terbatas',
                                        'galian'         => 'Pekerjaan Galian',
                                        'tegangan_tinggi'=> 'Pekerjaan Tegangan Tinggi',
                                        'radiasi'        => 'Radiasi',
                                    ];
                                    foreach ($permit->klasifikasi_pekerjaan as $k) {
                                        $klasArr[] = $nameMap[$k] ?? ucwords(str_replace('_', ' ', $k));
                                    }
                                }

                                $mulai = $permit->tanggal_mulai ? $permit->tanggal_mulai->format('d/m/Y') : null;
                                $selesai = $permit->tanggal_selesai ? $permit->tanggal_selesai->format('d/m/Y') : null;
                                if ($mulai && $selesai) {
                                    $tglDisplay = ($mulai === $selesai) ? $mulai : "$mulai – $selesai";
                                } elseif ($mulai) {
                                    $tglDisplay = $mulai;
                                } elseif ($selesai) {
                                    $tglDisplay = $selesai;
                                } elseif ($permit->submitted_at) {
                                    $tglDisplay = $permit->submitted_at->format('d/m/Y');
                                } else {
                                    $tglDisplay = '—';
                                }

                                $statusMap = [
                                    'Draft'                    => 'bg-gray-100 text-gray-600 border-gray-200',
                                    'Submitted'                => 'bg-blue-50 text-blue-700 border-blue-200/70',
                                    'Review Staff'             => 'bg-orange-50 text-orange-700 border-orange-200/70',
                                    'Review Manager'           => 'bg-orange-50 text-orange-700 border-orange-200/70',
                                    'Revision'                 => 'bg-red-50 text-red-700 border-red-200/70',
                                    'Active'                   => 'bg-green-50 text-green-700 border-green-200/70',
                                    'Closed'                   => 'bg-slate-50 text-slate-700 border-slate-200',
                                    'Cancelled'                => 'bg-red-600 text-white border-red-600',
                                ];
                                $statusBadge = $statusMap[$permit->status] ?? 'bg-gray-100 text-gray-600 border-gray-200';

                                $modalPayload = [
                                    'no_permit' => $permit->no_permit,
                                    'site' => $permit->site ?? 'Madiun',
                                    'tipe' => $permit->tipe,
                                    'nama_pekerjaan' => $permit->nama_pekerjaan,
                                    'divisi' => optional($permit->user)->name ?? $permit->divisi_pengaju ?? '—',
                                    'kontraktor' => $permit->kontraktor,
                                    'lokasi' => $permit->lokasi ?? '—',
                                    'tanggal_permit' => $tglDisplay,
                                    'penanggung_jawab' => $permit->penanggung_jawab ?? '—',
                                    'status' => $permit->status,
                                    'status_badge' => $statusBadge,
                                    'klasifikasi' => $klasArr,
                                ];
                            @endphp
                            <tr class="transition-colors">
                                <td class="whitespace-nowrap font-semibold text-inka-navy">
                                    {{ $permit->no_permit }}
                                </td>
                                <td class="whitespace-nowrap">
                                    <x-permit-site-badge :site="$permit->site" />
                                </td>
                                <td class="whitespace-nowrap">
                                    <x-permit-tipe-badge :tipe="$permit->tipe" />
                                </td>
                                <td>
                                    <x-permit-klasifikasi-badges :permit="$permit" />
                                </td>
                                <td class="font-medium text-gray-800 whitespace-nowrap">
                                    {{ optional($permit->user)->name ?? $permit->divisi_pengaju ?? '—' }}
                                </td>
                                <td class="text-gray-600">
                                    {{ $permit->kontraktor }}
                                </td>
                                <td class="text-gray-600">
                                    {{ $permit->lokasi ?? '—' }}
                                </td>
                                <td class="whitespace-nowrap">
                                    <x-permit-tanggal :permit="$permit" />
                                </td>
                                <td class="whitespace-nowrap">
                                    <x-permit-status-badge :status="$permit->status" />
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <button type="button"
                                            @click="openModal({{ json_encode($modalPayload) }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-inka-navy text-white hover:opacity-90 transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Lihat Detail
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- HP saja: card vertikal, tanpa tabel 900px (anti scroll-jank).
                     Laptop/desktop tetap pakai tabel di atas. Data sama, modal sama. --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @foreach($permits as $permit)
                    @php
                        $mKlas = [];
                        if ($permit->relationLoaded('classifications') && $permit->classifications->isNotEmpty()) {
                            $mKlas = $permit->classifications->pluck('name')->toArray();
                        } elseif (!empty($permit->klasifikasi_pekerjaan) && is_array($permit->klasifikasi_pekerjaan)) {
                            $mMap = ['panas'=>'Pekerjaan Panas','ketinggian'=>'Pekerjaan Ketinggian','ruang_terbatas'=>'Ruang Terbatas','galian'=>'Pekerjaan Galian','tegangan_tinggi'=>'Pekerjaan Tegangan Tinggi','radiasi'=>'Radiasi'];
                            foreach ($permit->klasifikasi_pekerjaan as $k) { $mKlas[] = $mMap[$k] ?? ucwords(str_replace('_',' ',$k)); }
                        }
                        $mMulai = $permit->tanggal_mulai ? $permit->tanggal_mulai->format('d/m/Y') : null;
                        $mSelesai = $permit->tanggal_selesai ? $permit->tanggal_selesai->format('d/m/Y') : null;
                        $mTgl = ($mMulai && $mSelesai) ? (($mMulai === $mSelesai) ? $mMulai : "$mMulai – $mSelesai") : ($mMulai ?? $mSelesai ?? ($permit->submitted_at ? $permit->submitted_at->format('d/m/Y') : '—'));
                        $mStatusMap = ['Draft'=>'bg-gray-100 text-gray-600 border-gray-200','Submitted'=>'bg-blue-50 text-blue-700 border-blue-200/70','Review Staff'=>'bg-orange-50 text-orange-700 border-orange-200/70','Review Manager'=>'bg-orange-50 text-orange-700 border-orange-200/70','Revision'=>'bg-red-50 text-red-700 border-red-200/70','Active'=>'bg-green-50 text-green-700 border-green-200/70','Closed'=>'bg-slate-50 text-slate-700 border-slate-200','Cancelled'=>'bg-red-600 text-white border-red-600'];
                        $mPayload = ['no_permit'=>$permit->no_permit,'site'=>$permit->site ?? 'Madiun','tipe'=>$permit->tipe,'nama_pekerjaan'=>$permit->nama_pekerjaan,'divisi'=>optional($permit->user)->name ?? $permit->divisi_pengaju ?? '—','kontraktor'=>$permit->kontraktor,'lokasi'=>$permit->lokasi ?? '—','tanggal_permit'=>$mTgl,'penanggung_jawab'=>$permit->penanggung_jawab ?? '—','status'=>$permit->status,'status_badge'=>$mStatusMap[$permit->status] ?? 'bg-gray-100 text-gray-600','klasifikasi'=>$mKlas];
                    @endphp
                    <button type="button" @click="openModal({{ json_encode($mPayload) }})" class="block w-full text-left px-4 py-4 active:bg-blue-50/50">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-bold text-inka-navy text-sm">{{ $permit->no_permit }}</span>
                            <x-permit-status-badge :status="$permit->status" class="shrink-0" />
                        </div>
                        <p class="text-sm text-gray-800 font-medium leading-snug">{{ $permit->nama_pekerjaan }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $permit->kontraktor }} &middot; {{ $permit->lokasi ?? '—' }} &middot; {{ $mTgl }}</p>
                        <div class="flex items-center gap-1.5 mt-2">
                            <x-permit-site-badge :site="$permit->site" />
                            <x-permit-tipe-badge :tipe="$permit->tipe" />
                        </div>
                    </button>
                    @endforeach
                </div>

                @if($permits->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                        {{ $permits->fragment('monitoring')->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Modal Pratinjau Publik (Read-Only) -->
    <div x-show="isModalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
         role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="isModalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
              class="fixed inset-0 bg-slate-900/60 md:backdrop-blur-sm transition-opacity"
              @click="closeModal()"></div>

        <!-- Dialog Window -->
        <div x-show="isModalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-10 my-8">
            
            <!-- Modal Header -->
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-gray-50/90 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="font-bold text-gray-900 text-lg" x-text="activePermit.no_permit"></span>
                    <span class="badge"
                          :class="activePermit.site === 'Banyuwangi' ? 'bg-emerald-50 text-emerald-700 border-emerald-200/70' : 'bg-indigo-50 text-indigo-700 border-indigo-200/70'"
                          x-text="activePermit.site || 'Madiun'"></span>
                    <span class="badge bg-blue-50 text-blue-700 border-blue-200/70" x-text="activePermit.tipe"></span>
                    <span class="badge"
                          :class="activePermit.status_badge || 'bg-gray-100 text-gray-600 border-gray-200'"
                          x-text="activePermit.status"></span>
                </div>
                <button @click="closeModal()" class="w-8 h-8 rounded-lg hover:bg-gray-200/60 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-4 sm:p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                <!-- Klasifikasi Badges -->
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Klasifikasi Pekerjaan</p>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="k in activePermit.klasifikasi" :key="k">
                            <span class="badge bg-blue-50 text-blue-700 border-blue-200/70" x-text="k"></span>
                        </template>
                        <template x-if="!activePermit.klasifikasi || activePermit.klasifikasi.length === 0">
                            <span class="text-sm text-gray-400 italic">Tidak ada klasifikasi pekerjaan</span>
                        </template>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50/70 rounded-xl p-4 border border-gray-100 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Uraian / Nama Pekerjaan</p>
                        <p class="font-semibold text-gray-800 mt-0.5" x-text="activePermit.nama_pekerjaan || '—'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Divisi Pengaju</p>
                        <p class="font-semibold text-gray-800 mt-0.5" x-text="activePermit.divisi"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Kontraktor Pelaksana</p>
                        <p class="font-semibold text-gray-800 mt-0.5" x-text="activePermit.kontraktor"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Lokasi Pekerjaan</p>
                        <p class="font-semibold text-gray-800 mt-0.5" x-text="activePermit.lokasi"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Site</p>
                        <p class="font-semibold text-gray-800 mt-0.5" x-text="activePermit.site || '—'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Tanggal Permit (Periode Pengerjaan)</p>
                        <p class="font-semibold text-gray-800 mt-0.5" x-text="activePermit.tanggal_permit"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Penanggung Jawab Lapangan</p>
                        <p class="font-semibold text-gray-800 mt-0.5" x-text="activePermit.penanggung_jawab"></p>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200/70 rounded-xl p-3.5 flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xs text-amber-800 leading-relaxed">
                        Data di atas adalah ringkasan monitoring publik. Dokumen teknis (JSA/HIRADC/APD) dan tanda tangan verifikasi dapat diakses oleh user berwenang melalui dashboard sistem.
                    </p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-4 sm:px-6 py-3.5 border-t border-gray-100 bg-gray-50 flex items-center justify-between gap-3">
                <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-200/60 transition-colors">
                    Tutup
                </button>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-inka-navy text-white hover:opacity-90 transition-opacity">
                    <span>Login Sistem</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

        </div>
    </div>
</section>

<!-- ================= MONITORING KONTRAKTOR ================= -->
<section class="py-12 md:py-20 px-4 md:px-6 bg-white border-y border-[var(--border)]" id="monitoring-kontraktor" x-data="{
    isContractorModalOpen: false,
    activeContractor: {
        vendor: '',
        pic: '',
        jenis: '',
        nama_pekerjaan: '',
        site: '',
        lokasi: '',
        periode: '',
        jumlah: '',
        status: '',
        status_badge: '',
        dokumen: 0
    },
    openContractorModal(data) {
        this.activeContractor = data;
        this.isContractorModalOpen = true;
        document.body.style.overflow = 'hidden';
    },
    closeContractorModal() {
        this.isContractorModalOpen = false;
        document.body.style.overflow = '';
    }
}">
    <div class="max-w-[1140px] mx-auto">
        <div class="max-w-[640px] mx-auto text-center mb-10 reveal">
            <div class="sec-tag inline-flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Monitoring Kontraktor
            </div>
            <h2 class="text-[22px] sm:text-[28px] font-extrabold tracking-[-.01em] mb-3 text-[var(--navy-950)]">
                Monitoring Data Kontraktor
            </h2>
            <p class="text-[14.5px] text-[var(--ink-600)]">
                Pantau status HSE Plan dan kesiapan dokumen kontraktor sebelum pekerjaan dimulai di lingkungan PT INKA (Persero).
            </p>
        </div>

        <div class="table-card reveal">
            <div class="px-4 md:px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 3a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Daftar Kontraktor Terkini</h3>
                        <p class="text-xs text-gray-500">Menampilkan 5 kontraktor terbaru beserta status HSE Plan</p>
                    </div>
                </div>
                <div class="text-xs font-semibold text-gray-500 bg-white px-3 py-1.5 rounded-lg border border-gray-200">
                    Total: <span class="text-inka-navy font-bold">{{ $contractors->total() }}</span> Kontraktor Terdaftar
                </div>
            </div>

            <form method="GET" action="{{ url('/') }}#monitoring-kontraktor" class="px-4 md:px-6 py-3 border-b border-gray-100 bg-white flex flex-wrap items-center gap-x-3 gap-y-2">
                @foreach(['site', 'tipe', 'search'] as $permitFilter)
                    @if(request()->filled($permitFilter))
                        <input type="hidden" name="{{ $permitFilter }}" value="{{ request($permitFilter) }}">
                    @endif
                @endforeach
                <label class="text-xs font-semibold text-gray-500">Filter site:</label>
                <select name="k_site" onchange="this.form.submit()" class="text-xs font-semibold border border-gray-200 rounded-lg px-3 py-1.5 text-gray-700 focus:outline-none focus:border-inka-navy">
                    <option value="">Semua Site</option>
                    @foreach(\App\Models\HsePlan::SITES as $s)
                        <option value="{{ $s }}" {{ request('k_site') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
                <label class="text-xs font-semibold text-gray-500">Status HSE Plan:</label>
                <select name="k_status" onchange="this.form.submit()" class="text-xs font-semibold border border-gray-200 rounded-lg px-3 py-1.5 text-gray-700 focus:outline-none focus:border-inka-navy">
                    <option value="">Semua Status</option>
                    @foreach($contractorStatuses as $status)
                        <option value="{{ $status }}" {{ request('k_status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
                @if(request('k_site') || request('k_status') || request('k_search'))
                    <a href="{{ url('/') }}#monitoring-kontraktor" class="text-xs text-gray-400 hover:text-gray-600">Reset</a>
                @endif
                <div class="ml-auto flex items-center gap-2 w-full sm:w-auto">
                    <div class="relative flex-1 sm:flex-none">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                        </svg>
                        <input type="text" name="k_search" value="{{ request('k_search') }}" maxlength="100"
                               placeholder="Cari kontraktor, PIC, pekerjaan..."
                               class="w-full sm:w-64 text-xs border border-gray-200 rounded-lg pl-8 pr-3 py-1.5 text-gray-700 placeholder-gray-400 focus:outline-none focus:border-inka-navy">
                    </div>
                    <button type="submit" class="text-xs font-semibold bg-inka-navy text-white px-3 py-1.5 rounded-lg hover:opacity-90 transition-opacity">Cari</button>
                </div>
            </form>

            @if($contractors->isEmpty())
                <div class="py-16 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 3a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="text-sm font-semibold text-gray-600">
                        @if(request('k_search'))
                            Tidak ada kontraktor yang cocok dengan pencarian "{{ request('k_search') }}".
                        @else
                            Belum ada data kontraktor yang terdaftar.
                        @endif
                    </p>
                </div>
            @else
                {{-- Desktop/laptop --}}
                <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0 hidden md:block">
                    <table class="data-table min-w-[1320px]">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Status</th>
                                <th>Vendor</th>
                                <th>Jenis Pekerjaan</th>
                                <th>Pekerjaan</th>
                                <th>Lokasi</th>
                                <th>PIC</th>
                                <th>No. Telepon</th>
                                <th>Tgl Mulai</th>
                                <th>Tgl Selesai</th>
                                <th>Lama Kerja</th>
                                <th>Jumlah Pekerja</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contractors as $contractor)
                            @php
                                $jenisLabel = $classifications[$contractor->jenis_pekerjaan]
                                    ?? ucwords(str_replace('_', ' ', $contractor->jenis_pekerjaan));

                                $mulai = $contractor->tanggal_mulai ? $contractor->tanggal_mulai->format('d/m/Y') : null;
                                $selesai = $contractor->tanggal_selesai ? $contractor->tanggal_selesai->format('d/m/Y') : null;

                                if ($mulai && $selesai) {
                                    $periode = ($mulai === $selesai) ? $mulai : "$mulai – $selesai";
                                } else {
                                    $periode = $mulai ?? $selesai ?? '—';
                                }

                                $lamaKerja = ($contractor->tanggal_mulai && $contractor->tanggal_selesai)
                                    ? ((int) $contractor->tanggal_mulai->diffInDays($contractor->tanggal_selesai) + 1)
                                    : null;

                                $contractorStatusMap = [
                                    'Menunggu HSE Plan' => 'bg-amber-50 text-amber-700 border-amber-200/70',
                                    'Review Manager' => 'bg-orange-50 text-orange-700 border-orange-200/70',
                                    'HSE Plan Diajukan' => 'bg-blue-50 text-blue-700 border-blue-200/70',
                                    'HSE Plan Final' => 'bg-emerald-50 text-emerald-700 border-emerald-200/70',
                                ];
                                $contractorStatusBadge = $contractorStatusMap[$contractor->status] ?? 'bg-gray-100 text-gray-600 border-gray-200';

                                $contractorPayload = [
                                    'vendor' => $contractor->vendor,
                                    'pic' => $contractor->pic,
                                    'jenis' => $jenisLabel,
                                    'nama_pekerjaan' => $contractor->nama_pekerjaan,
                                    'site' => $contractor->site,
                                    'lokasi' => $contractor->lokasi ?? '—',
                                    'periode' => $periode,
                                    'jumlah' => $contractor->jumlah_pekerja,
                                    'status' => $contractor->status,
                                    'status_badge' => $contractorStatusBadge,
                                    'dokumen' => $contractor->documents_count,
                                ];
                            @endphp
                            <tr class="transition-colors">
                                <td class="text-center text-gray-500">{{ ($contractors->currentPage() - 1) * $contractors->perPage() + $loop->iteration }}</td>
                                <td class="whitespace-nowrap">
                                    <span class="badge {{ $contractorStatusBadge }}">{{ $contractor->status }}</span>
                                </td>
                                <td class="font-semibold text-inka-navy whitespace-nowrap">{{ $contractor->vendor }}</td>
                                <td class="whitespace-nowrap">
                                    <span class="badge bg-blue-50 text-blue-700 border-blue-200/70">{{ $jenisLabel }}</span>
                                </td>
                                <td class="text-gray-600 min-w-[220px] max-w-[320px]">{{ $contractor->nama_pekerjaan }}</td>
                                <td class="text-gray-600">{{ $contractor->lokasi ?? '—' }}</td>
                                <td class="text-gray-600 whitespace-nowrap">{{ $contractor->pic }}</td>
                                <td class="text-gray-600 whitespace-nowrap">{{ $contractor->telepon }}</td>
                                <td class="whitespace-nowrap text-gray-600">{{ $mulai ?? '—' }}</td>
                                <td class="whitespace-nowrap text-gray-600">{{ $selesai ?? '—' }}</td>
                                <td class="whitespace-nowrap text-gray-600">{{ $lamaKerja ? $lamaKerja . ' hari' : '—' }}</td>
                                <td class="whitespace-nowrap text-gray-600">{{ $contractor->jumlah_pekerja }} orang</td>
                                <td class="text-right whitespace-nowrap">
                                    <button type="button"
                                            @click="openContractorModal({{ json_encode($contractorPayload, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-inka-navy text-white hover:opacity-90 transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Lihat Detail
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- HP --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @foreach($contractors as $contractor)
                    @php
                        $mJenis = $classifications[$contractor->jenis_pekerjaan]
                            ?? ucwords(str_replace('_', ' ', $contractor->jenis_pekerjaan));
                        $mMulai = $contractor->tanggal_mulai ? $contractor->tanggal_mulai->format('d/m/Y') : null;
                        $mSelesai = $contractor->tanggal_selesai ? $contractor->tanggal_selesai->format('d/m/Y') : null;
                        $mPeriode = ($mMulai && $mSelesai)
                            ? (($mMulai === $mSelesai) ? $mMulai : "$mMulai – $mSelesai")
                            : ($mMulai ?? $mSelesai ?? '—');
                        $mStatusMap = [
                            'Menunggu HSE Plan' => 'bg-amber-50 text-amber-700 border-amber-200/70',
                            'Review Manager' => 'bg-orange-50 text-orange-700 border-orange-200/70',
                            'HSE Plan Diajukan' => 'bg-blue-50 text-blue-700 border-blue-200/70',
                            'HSE Plan Final' => 'bg-emerald-50 text-emerald-700 border-emerald-200/70',
                        ];
                        $mStatusBadge = $mStatusMap[$contractor->status] ?? 'bg-gray-100 text-gray-600 border-gray-200';
                        $mPayload = [
                            'vendor' => $contractor->vendor,
                            'pic' => $contractor->pic,
                            'jenis' => $mJenis,
                            'nama_pekerjaan' => $contractor->nama_pekerjaan,
                            'site' => $contractor->site,
                            'lokasi' => $contractor->lokasi ?? '—',
                            'periode' => $mPeriode,
                            'jumlah' => $contractor->jumlah_pekerja,
                            'status' => $contractor->status,
                            'status_badge' => $mStatusBadge,
                            'dokumen' => $contractor->documents_count,
                        ];
                    @endphp
                    <button type="button" @click="openContractorModal({{ json_encode($mPayload, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) }})" class="block w-full text-left px-4 py-4 active:bg-emerald-50/50">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-bold text-inka-navy text-sm">{{ $contractor->vendor }}</span>
                            <span class="badge {{ $mStatusBadge }} shrink-0">{{ $contractor->status }}</span>
                        </div>
                        <p class="text-sm text-gray-800 font-medium leading-snug">{{ $contractor->nama_pekerjaan }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $contractor->pic }} &middot; {{ $contractor->lokasi ?? '—' }} &middot; {{ $mPeriode }}</p>
                        <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                            <x-permit-site-badge :site="$contractor->site" />
                            <span class="badge bg-blue-50 text-blue-700 border-blue-200/70">{{ $mJenis }}</span>
                            <span class="text-[11px] text-gray-400">{{ $contractor->jumlah_pekerja }} pekerja &middot; {{ $contractor->documents_count }} dokumen</span>
                        </div>
                    </button>
                    @endforeach
                </div>

                @if($contractors->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                        {{ $contractors->fragment('monitoring-kontraktor')->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Modal Detail Kontraktor (Read-Only) -->
    <div x-show="isContractorModalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
         role="dialog" aria-modal="true">
        <div x-show="isContractorModalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 md:backdrop-blur-sm transition-opacity"
             @click="closeContractorModal()"></div>

        <div x-show="isContractorModalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-10 my-8">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-gray-50/90 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5 flex-wrap min-w-0">
                    <span class="font-bold text-gray-900 text-lg truncate" x-text="activeContractor.vendor"></span>
                    <span class="badge"
                          :class="activeContractor.site === 'Banyuwangi' ? 'bg-emerald-50 text-emerald-700 border-emerald-200/70' : 'bg-indigo-50 text-indigo-700 border-indigo-200/70'"
                          x-text="activeContractor.site || 'Madiun'"></span>
                    <span class="badge"
                          :class="activeContractor.status_badge || 'bg-gray-100 text-gray-600 border-gray-200'"
                          x-text="activeContractor.status"></span>
                </div>
                <button @click="closeContractorModal()" class="w-8 h-8 rounded-lg hover:bg-gray-200/60 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-4 sm:p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Jenis Pekerjaan</p>
                    <span class="badge bg-blue-50 text-blue-700 border-blue-200/70" x-text="activeContractor.jenis"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50/70 rounded-xl p-4 border border-gray-100 text-sm">
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500 font-medium">Uraian / Nama Pekerjaan</p>
                        <p class="font-semibold text-gray-800 mt-0.5" x-text="activeContractor.nama_pekerjaan || '—'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">PIC / Penanggung Jawab</p>
                        <p class="font-semibold text-gray-800 mt-0.5" x-text="activeContractor.pic || '—'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Site</p>
                        <p class="font-semibold text-gray-800 mt-0.5" x-text="activeContractor.site || '—'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Lokasi Pekerjaan</p>
                        <p class="font-semibold text-gray-800 mt-0.5" x-text="activeContractor.lokasi"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Periode Pengerjaan</p>
                        <p class="font-semibold text-gray-800 mt-0.5" x-text="activeContractor.periode"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Jumlah Pekerja</p>
                        <p class="font-semibold text-gray-800 mt-0.5" x-text="(activeContractor.jumlah || '0') + ' orang'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Dokumen Pendukung</p>
                        <p class="font-semibold text-gray-800 mt-0.5" x-text="activeContractor.dokumen ? activeContractor.dokumen + ' berkas' : 'Belum ada dokumen'"></p>
                    </div>
                </div>

                <template x-if="activeContractor.status === 'HSE Plan Final'">
                    <div class="bg-emerald-50 border border-emerald-200/70 rounded-xl p-3.5 flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-emerald-800 leading-relaxed">
                            HSE Plan kontraktor ini sudah final. Pekerjaan dapat dilanjutkan sesuai periode dan ketentuan K3 yang berlaku.
                        </p>
                    </div>
                </template>

                <template x-if="activeContractor.status !== 'HSE Plan Final'">
                    <div class="bg-amber-50 border border-amber-200/70 rounded-xl p-3.5 flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-amber-800 leading-relaxed">
                            HSE Plan kontraktor ini belum final. Kontraktor perlu melengkapi dokumen dan mengajukan HSE Plan sebelum pekerjaan dimulai.
                        </p>
                    </div>
                </template>

                <div class="bg-blue-50 border border-blue-200/70 rounded-xl p-3.5 flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xs text-blue-800 leading-relaxed">
                        Data di atas adalah ringkasan monitoring publik. Dokumen teknis dan detail HSE Plan dapat diakses oleh user berwenang melalui dashboard sistem.
                    </p>
                </div>
            </div>

            <div class="px-4 sm:px-6 py-3.5 border-t border-gray-100 bg-gray-50 flex items-center justify-between gap-3">
                <button type="button" @click="closeContractorModal()" class="px-4 py-2 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-200/60 transition-colors">
                    Tutup
                </button>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-inka-navy text-white hover:opacity-90 transition-opacity">
                    <span>Login Sistem</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ================= ALUR PENGAJUAN ================= -->
<section class="py-12 md:py-20 px-4 md:px-6" id="alur">
    <div class="max-w-[1080px] mx-auto">
        <div class="max-w-[560px] mx-auto text-center mb-8 md:mb-[46px] reveal">
            <div class="sec-tag">Alur Pengajuan</div>
            <h2 class="text-[22px] sm:text-[28px] font-extrabold tracking-[-.01em] mb-3 text-[var(--navy-950)]">Bagaimana Cara Mengajukan Izin Kerja</h2>
            <p class="text-[14.5px] text-[var(--ink-600)]">Tanpa akun — isi form langsung di landing page, permit otomatis masuk verifikasi HSE.</p>
        </div>

        <div class="flow">
            <div class="flow-line"></div>
            <div class="flow-grid grid grid-cols-1 md:grid-cols-5 gap-4 relative z-10">
                <div class="flow-step reveal" style="transition-delay:0ms">
                    <div class="sn">1</div>
                    <h4>Buka Form Pengajuan</h4>
                    <p>Klik <strong>Ajukan Safety Permit</strong> di atas — tanpa login, tanpa akun.</p>
                </div>
                <div class="flow-step reveal" style="transition-delay:80ms">
                    <div class="sn">2</div>
                    <h4>Isi Data & Divisi</h4>
                    <p>Tulis nama divisi/unit pengaju, data kontraktor, dan detail pekerjaan.</p>
                </div>
                <div class="flow-step reveal" style="transition-delay:160ms">
                    <div class="sn">3</div>
                    <h4>Identifikasi Pekerjaan</h4>
                    <p>Klasifikasi pekerjaan, bahaya, serta APD didiskusikan bersama kontraktor.</p>
                </div>
                <div class="flow-step reveal" style="transition-delay:240ms">
                    <div class="sn">4</div>
                    <h4>Lengkapi Berkas K3</h4>
                    <p>Lampirkan dokumen pendukung seperti HIRADC, JSA, atau HSE Plan (wajib untuk Eksternal).</p>
                </div>
                <div class="flow-step reveal" style="transition-delay:320ms">
                    <div class="sn">5</div>
                    <h4>Kirim &amp; Verifikasi</h4>
                    <p>Permit langsung berstatus Review Staff untuk diverifikasi sebelum pekerjaan dimulai.</p>
                </div>
            </div>
        </div>

        <div class="flow-note reveal-scale" style="transition-delay:200ms">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0 mt-0.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><path d="M12 9v4M12 17h.01"/></svg>
            <span><strong>Info:</strong> pengajuan tidak memerlukan akun. Isi nama divisi/unit pengaju langsung di form, seluruh permit publik otomatis masuk antrian verifikasi Staff HSE dan terpantau di tabel Monitoring.</span>
        </div>
    </div>
</section>

<!-- ================= TENTANG KAMI ================= -->
<section class="py-12 md:py-20 px-4 md:px-6 bg-white border-y border-[var(--border)]" id="tentang">
    <div class="max-w-[1080px] mx-auto about-grid grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
        <div class="reveal-left">
            <div class="sec-tag">Tentang Kami</div>
            <h2 class="text-[22px] sm:text-[26px] font-extrabold tracking-[-.01em] mb-[14px] text-[var(--navy-950)]">Dibangun untuk Tim HSE yang Ingin Bekerja Lebih Cepat dan Aman</h2>
            <p class="text-sm text-[var(--ink-600)] mb-3.5 leading-relaxed">Safety Permit PT INKA (Persero) adalah sistem monitoring keselamatan kerja kontraktor yang menggantikan proses manual berbasis kertas dengan alur digital yang lebih rapi dan mudah diaudit.</p>
            <p class="text-sm text-[var(--ink-600)] mb-3.5 leading-relaxed">Kami percaya keselamatan kerja seharusnya tidak menghambat kecepatan operasional. Karena itu Safety Permit PT INKA (Persero) dirancang agar divisi bisa mengelola data kontraktor dan mengajukan izin kerja dengan cepat, sementara tim HSE tetap punya kendali penuh atas setiap persetujuan.</p>
            <a href="#" class="btn btn-solid" style="margin-top:8px;">Pelajari Lebih Lanjut</a>
        </div>
        <div class="about-visual reveal-right">
            <div class="about-stats grid grid-cols-2 gap-4 relative">
                <div class="about-stat"><div class="v">42+</div><div class="l">Kontraktor aktif dipantau</div></div>
                <div class="about-stat"><div class="v">91%</div><div class="l">Skor kepatuhan K3</div></div>
                <div class="about-stat"><div class="v">1.240+</div><div class="l">Izin kerja diproses</div></div>
                <div class="about-stat"><div class="v">&lt;2 mnt</div><div class="l">Rata-rata waktu persetujuan</div></div>
            </div>
        </div>
    </div>
</section>

<!-- ================= FOOTER ================= -->
<footer class="bg-[var(--navy-950)] text-white px-4 md:px-6 pt-10 md:pt-12 pb-6">
    <div class="max-w-[1080px] mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start gap-8 mb-8">
            <div>
                <div class="flex items-center gap-3" style="color:#fff;">
                    <img src="{{ asset('assets/images/logoinka.svg') }}" alt="INKA" class="h-7 w-auto brightness-0 invert opacity-80">
                    <div class="border-l border-white/20 pl-3">
                        <span class="text-sm font-bold">Safety Permit</span>
                        <span class="block text-[10px] font-semibold text-[var(--steel-300)] tracking-wide">PT INKA (Persero)</span>
                    </div>
                </div>
                <p class="text-[12.5px] text-[var(--steel-300)] max-w-[280px] mt-3">Platform digital untuk monitoring kontraktor dan izin kerja risiko tinggi dalam satu sistem terpadu.</p>
            </div>
            <div class="flex gap-12 flex-wrap">
                <div>
                    <h5 class="text-[11.5px] font-bold uppercase tracking-wide text-[var(--steel-300)] mb-3.5">Navigasi</h5>
                    <ul class="flex flex-col gap-2.5">
                        <li><a href="#monitoring" class="text-sm text-white/85 hover:text-white transition-opacity">Monitoring Permit</a></li>
                        <li><a href="#monitoring-kontraktor" class="text-sm text-white/85 hover:text-white transition-opacity">Monitoring Kontraktor</a></li>
                        <li><a href="#alur" class="text-sm text-white/85 hover:text-white transition-opacity">Alur Pengajuan</a></li>
                        <li><a href="#tentang" class="text-sm text-white/85 hover:text-white transition-opacity">Tentang Kami</a></li>
                        <li><a href="{{ route('login') }}" class="text-sm text-white/85 hover:text-white transition-opacity">Login</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-[11.5px] font-bold uppercase tracking-wide text-[var(--steel-300)] mb-3.5">Kontak</h5>
                    <ul class="flex flex-col gap-2.5">
                        <li><a href="mailto:hse@inkamadiun.co.id" class="text-sm text-white/85 hover:text-white transition-opacity">hse@inkamadiun.co.id</a></li>
                        <li><a href="tel:+622112345678" class="text-sm text-white/85 hover:text-white transition-opacity">(021) 1234-5678</a></li>
                        <li><span class="text-sm text-white/85">Madiun, Indonesia</span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="border-t border-white/12 pt-5 flex justify-between items-center flex-wrap gap-2.5 text-xs text-[var(--steel-300)]">
            <span>&copy; 2026 Safety Permit PT INKA (Persero). Seluruh hak cipta dilindungi.</span>
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/images/logok3.png') }}" alt="Logo K3" class="h-8 w-auto opacity-70">
                <span>HSE PT INKA (Persero)</span>
            </div>
        </div>
    </div>
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // HP: CSS sudah memaksa semua reveal terlihat (tanpa animasi),
        // jadi lewati timeout + IntersectionObserver agar hemat CPU/baterai.
        // Laptop/desktop tetap pakai animasi seperti semula.
        if (window.matchMedia('(max-width: 767px)').matches) {
            document.querySelectorAll('.hero-animate, .reveal, .reveal-left, .reveal-right, .reveal-scale')
                .forEach(el => el.classList.add('is-visible', 'hero-visible'));
            return;
        }
        // Hero entrance animation with staggered delays
        const heroEls = document.querySelectorAll('.hero-animate');
        heroEls.forEach((el, i) => {
            setTimeout(() => {
                el.classList.add('hero-visible');
            }, 200 + i * 150);
        });

        // Scroll reveal animations
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            root: null,
            rootMargin: '0px',
            threshold: 0.15
        });

        document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale').forEach(el => {
            revealObserver.observe(el);
        });
    });
</script>

<script>
    (function () {
        const timeEl = document.getElementById('hero-realtime-time');
        const dateEl = document.getElementById('hero-realtime-date');
        if (!timeEl) return;

        const timeFmt = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
        });
        const dateFmt = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta',
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
        });

        function render() {
            const now = new Date();
            timeEl.textContent = timeFmt.format(now).replace(/\./g, ':');
            if (dateEl) dateEl.textContent = dateFmt.format(now);
        }

        render();
        setInterval(render, 1000);
    })();
</script>

</body>
</html>