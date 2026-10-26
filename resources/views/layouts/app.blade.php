<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' — ' : '' }}Safety Permit PT INKA (Persero)</title>

        <link rel="icon" type="image/png" href="{{ asset('assets/images/logok3.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-gray-900 flex app-shell overflow-hidden" x-data="{ sidebarOpen: false }">

        {{-- Overlay HP saat sidebar terbuka --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/50 z-20 lg:hidden"></div>

        {{-- ===== SIDEBAR ===== --}}
        @php 
            $role = auth()->user()->role ?? '';
            
            $pendingCount = 0;
            $pendingPermits = [];
            if (in_array($role, ['staff', 'manager'])) {
                $statusMap = [
                    'staff' => 'Review Staff',
                    'manager' => 'Review Manager'
                ];
                $expectedStatus = $statusMap[$role] ?? null;
                if ($expectedStatus) {
                    try {
                        $notifQuery = \App\Models\Permit::visibleTo(auth()->user())->where('status', $expectedStatus);
                        $pendingCount = (clone $notifQuery)->count();
                        $pendingPermits = $notifQuery->with('user')
                            ->orderBy('updated_at', 'desc')
                            ->take(5)
                            ->get();
                    } catch (\Throwable $e) {
                        $pendingPermits = [];
                        $pendingCount = 0;
                    }
                }
            }
        @endphp
        <aside id="sidebar" x-bind:class="sidebarOpen && 'sidebar-open'" class="fixed inset-y-0 left-0 w-64 bg-inka-navy text-white flex flex-col shrink-0 z-30 transition-transform duration-200 lg:static lg:translate-x-0">

            {{-- Logo --}}
            <div class="h-16 flex items-center gap-3 px-5 border-b border-white/10 shrink-0">
                <img src="{{ asset('assets/images/logoinka.svg') }}" alt="Logo PT INKA" class="h-8 w-auto brightness-0 invert">
                <div class="leading-tight pl-1 border-l border-white/20">
                    <p class="text-[11px] font-bold tracking-wider text-white">SAFETY PERMIT</p>
                    <p class="text-[9px] text-gray-400 tracking-wide">PT INKA (Persero)</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-5 px-3 space-y-1">

                {{-- === SUPERADMIN === --}}
                @if($role === 'superadmin')
                    <a href="/superadmin/dashboard" class="sidebar-link {{ request()->is('superadmin/dashboard') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                    <a href="/superadmin/users" class="sidebar-link {{ request()->is('superadmin/users*') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Kelola Akun
                    </a>
                    <a href="/superadmin/divisions" class="sidebar-link {{ request()->is('superadmin/divisions*') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Kelola Divisi
                    </a>
                @endif

                {{-- === DIVISI === --}}
                @if($role === 'divisi')
                    <a href="/divisi/dashboard" class="sidebar-link {{ request()->is('divisi/dashboard') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                    <a href="/divisi/permits/create" class="sidebar-link {{ request()->is('divisi/permits/create') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Buat Permit
                    </a>
                    <a href="/divisi/history" class="sidebar-link {{ request()->is('divisi/history*') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        History Permit
                    </a>
                    <a href="/divisi/cancellations" class="sidebar-link {{ request()->is('divisi/cancellations*') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Pembatalan Permit
                    </a>
                @endif

                {{-- === ADMIN (Staff, Manager) === --}}
                @if(in_array($role, ['staff', 'manager']))
                    <a href="/admin/dashboard" class="sidebar-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                    <div class="sidebar-section">
                        <span class="sidebar-section-dot bg-accent-orange"></span>
                        Safety Permit
                    </div>
                    <a href="/admin/approvals" class="sidebar-link sidebar-sublink {{ request()->is('admin/approvals*') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Review Permit
                    </a>
                    <a href="/admin/history" class="sidebar-link sidebar-sublink {{ request()->is('admin/history*') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        History
                    </a>
                    <a href="/admin/cancellations" class="sidebar-link sidebar-sublink {{ request()->is('admin/cancellations*') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Pembatalan Permit
                    </a>

                    <div class="sidebar-section">
                        <span class="sidebar-section-dot bg-blue-400"></span>
                        Data Kontraktor
                    </div>
                    <a href="/admin/hse-plans" class="sidebar-link sidebar-sublink {{ request()->is('admin/hse-plans') || (request()->is('admin/hse-plans/*') && !request()->is('admin/hse-plans/history*')) ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $role === 'manager' ? 'Review HSE' : 'HSE Plan' }}
                    </a>
                    <a href="/admin/hse-plans/history" class="sidebar-link sidebar-sublink {{ request()->is('admin/hse-plans/history*') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        History
                    </a>

                    <div class="sidebar-section">
                        <span class="sidebar-section-dot bg-purple-400"></span>
                        Daftar Periksa Izin
                    </div>
                    <a href="/admin/work-inspections" class="sidebar-link sidebar-sublink {{ request()->is('admin/work-inspections') || (request()->is('admin/work-inspections/*') && !request()->is('admin/work-inspections/history*')) ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Verifikasi
                    </a>
                    <a href="/admin/work-inspections/history" class="sidebar-link sidebar-sublink {{ request()->is('admin/work-inspections/history*') ? 'active' : '' }}">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        History
                    </a>

                @endif



            </nav>

            {{-- Logout --}}
            <div class="px-3 pb-4 shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-colors">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- ===== MAIN AREA ===== --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- Header --}}
            <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 md:px-6 shrink-0 shadow-sm relative z-20">
                <div class="flex items-center gap-2.5 min-w-0">
                    <button type="button" @click="sidebarOpen = !sidebarOpen" class="lg:hidden w-10 h-10 -ml-1 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600 shrink-0" aria-label="Buka menu">
                        <svg x-show="!sidebarOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="sidebarOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <h2 class="text-base font-semibold text-gray-800 truncate">{{ $title ?? 'Dashboard' }}</h2>
                </div>
                <div class="flex items-center gap-2 sm:gap-4">
                    {{-- Jam & tanggal realtime --}}
                    <div class="flex items-center leading-tight text-right" title="Waktu Indonesia Barat (WIB)">
                        <span id="realtime-time" class="text-lg sm:text-xl font-bold text-gray-800 tabular-nums">--:--:--</span>
                    </div>

                    {{-- Notification Bell --}}
                    @if(in_array($role, ['staff', 'manager']))
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" class="relative p-2 text-gray-400 hover:text-inka-navy transition-colors rounded-full hover:bg-gray-50 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        
                        @if($pendingCount > 0)
                        <span class="absolute top-1.5 right-2 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                        @endif
                    </button>
                    
                    {{-- Dropdown --}}
                    <div x-show="open" x-transition style="display: none;" class="absolute right-0 mt-2 w-72 sm:w-80 max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="text-sm font-semibold text-gray-800">Notifikasi Approval</h3>
                            <p class="text-[11px] text-gray-500">Anda memiliki {{ $pendingCount }} permit menunggu review.</p>
                        </div>
                        <div class="max-h-[300px] overflow-y-auto">
                            @if($pendingCount > 0)
                                @foreach($pendingPermits as $permit)
                                <a href="/admin/approvals/{{ $permit->id }}" class="block px-4 py-3 border-b border-gray-50 hover:bg-orange-50/50 transition-colors">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-xs font-bold text-inka-navy">{{ $permit->no_permit }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $permit->updated_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs text-gray-700 font-medium truncate">{{ $permit->nama_pekerjaan }}</p>
                                    <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ $permit->site ?? 'Madiun' }} &middot; {{ optional($permit->user)->name ?? 'Divisi' }}</p>
                                </a>
                                @endforeach
                                @if($pendingCount > 5)
                                <a href="/admin/approvals" class="block px-4 py-2.5 text-center text-xs font-semibold text-inka-navy hover:bg-gray-50 transition-colors">
                                    Lihat Semua ({{ $pendingCount }})
                                </a>
                                @endif
                            @else
                                <div class="px-4 py-8 text-center">
                                    <svg class="w-8 h-8 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-xs text-gray-500">Tidak ada notifikasi baru.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    </div>
                    @endif
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                {{ $slot }}
            </main>

        </div>

        <style>
            [x-cloak] { display: none !important; }
            /* Cegah sidebar HP sekilas terlihat tiap pindah halaman:
               sebelum Alpine siap, sidebar dipaksa tersembunyi via CSS murni
               (tanpa menunggu JS). Laptop/desktop tidak tersentuh (lg: ke atas
               dikendalikan Tailwind seperti semula). */
            @media (max-width: 1023.98px) {
                #sidebar { transform: translateX(-100%); }
                #sidebar.sidebar-open { transform: translateX(0); }
            }
            /* Tinggi shell pakai dynamic viewport agar pas dengan tinggi layar
               terlihat di HP (100vh ikut menghitung area di balik address bar
               browser sehingga tombol di paling bawah terpotong & tak terjangkau).
               Laptop/desktop tidak berubah (dvh == vh di sana). */
            html, body { height: 100%; }
            body.app-shell { height: 100vh; height: 100dvh; }
            .sidebar-link {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 9px 12px;
                border-radius: 10px;
                font-size: 0.8125rem;
                font-weight: 500;
                color: #9ca3af;
                transition: background-color 0.15s, color 0.15s;
            }
            .sidebar-link:hover {
                background-color: rgba(255,255,255,0.07);
                color: #fff;
            }
            .sidebar-link.active {
                background-color: rgba(255,255,255,0.12);
                color: #fff;
            }
            .sidebar-icon {
                width: 18px;
                height: 18px;
                flex-shrink: 0;
            }
            .sidebar-section {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 18px 12px 6px;
                font-size: 0.625rem;
                font-weight: 700;
                letter-spacing: 0.14em;
                text-transform: uppercase;
                color: #6b7280;
            }
            .sidebar-section-dot {
                width: 6px;
                height: 6px;
                border-radius: 9999px;
                flex-shrink: 0;
            }
            .sidebar-sublink {
                padding-left: 26px;
            }
        </style>

        <script>
            (function () {
                const timeEl = document.getElementById('realtime-time');
                const dateEl = document.getElementById('realtime-date');
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
