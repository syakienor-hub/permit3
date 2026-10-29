<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    @php
        $roleName = match($role) {
            'staff' => 'Staff',
            'manager' => 'Manager',
            default => 'Admin'
        };
        $siteLabel = $site ?: 'Semua Site';
    @endphp

    @if($role === 'staff')
        {{-- ===== HERO ===== --}}
        <section class="relative overflow-hidden rounded-2xl bg-inka-navy p-6 sm:p-7 mb-6 text-white shadow-sm">
            <div class="pointer-events-none absolute -right-16 -top-20 h-56 w-56 rounded-full bg-white/5"></div>
            <div class="pointer-events-none absolute -right-4 bottom-[-70px] h-44 w-44 rounded-full bg-accent-orange/20 blur-xl"></div>
            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="min-w-0">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.12em] text-white/90">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Staff HSE &middot; {{ $siteLabel }}
                    </span>
                    <h1 class="mt-3 text-2xl sm:text-[28px] font-bold leading-tight">{{ $greeting }}, {{ auth()->user()->name }}</h1>
                    <p class="mt-1 text-sm text-white/60">
                        {{ $todayLabel }} &mdash;
                        @if($counts['permit_pending'] > 0)
                            <span class="font-semibold text-white">{{ $counts['permit_pending'] }} permit</span> menunggu tindakan Anda.
                        @else
                            Semua antrean sudah tertangani. Kerja bagus, tetap utamakan keselamatan!
                        @endif
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <a href="/admin/approvals"
                       class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-inka-navy hover:bg-white/90 transition-colors">
                        Review Permit
                        @if($counts['permit_pending'] > 0)
                            <span class="inline-flex min-w-[20px] items-center justify-center rounded-full bg-accent-orange px-1.5 py-0.5 text-[11px] font-bold text-white">{{ $counts['permit_pending'] }}</span>
                        @endif
                    </a>
                    <a href="/admin/work-inspections"
                       class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/5 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/10 transition-colors">
                        Kelola Daftar Periksa
                    </a>
                </div>
            </div>
        </section>

        {{-- ===== RINGKASAN ===== --}}
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 mb-6">
            <a href="/admin/approvals"
               class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 hover:shadow-md hover:border-orange-200 transition-all">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="text-[11px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide">Permit Pending</p>
                    <span class="w-8 h-8 rounded-xl bg-orange-50 flex items-center justify-center shrink-0 group-hover:bg-orange-100 transition-colors">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-orange-500">{{ $counts['permit_pending'] }}</p>
                <p class="text-[11px] sm:text-xs text-gray-400 mt-1">{{ $counts['permit_today'] }} masuk hari ini</p>
            </a>

            <a href="/admin/work-inspections"
               class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 hover:shadow-md hover:border-purple-200 transition-all">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="text-[11px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide">Daftar Periksa</p>
                    <span class="w-8 h-8 rounded-xl bg-purple-50 flex items-center justify-center shrink-0 group-hover:bg-purple-100 transition-colors">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-purple-500">{{ $counts['inspection_pending'] }}</p>
                <p class="text-[11px] sm:text-xs text-gray-400 mt-1">Total daftar periksa site ini</p>
            </a>

            <a href="/admin/hse-plans"
               class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 hover:shadow-md hover:border-blue-200 transition-all">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="text-[11px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide">HSE Plan</p>
                    <span class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center shrink-0 group-hover:bg-blue-100 transition-colors">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-blue-500">{{ $counts['hse_review'] }}</p>
                <p class="text-[11px] sm:text-xs text-gray-400 mt-1">Menunggu review</p>
            </a>

            <a href="/admin/history?status=Active"
               class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 hover:shadow-md hover:border-green-200 transition-all">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="text-[11px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide">Permit Aktif</p>
                    <span class="w-8 h-8 rounded-xl bg-green-50 flex items-center justify-center shrink-0 group-hover:bg-green-100 transition-colors">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-green-500">{{ $counts['permit_active'] }}</p>
                <p class="text-[11px] sm:text-xs text-gray-400 mt-1">Sedang berlaku di site ini</p>
            </a>
        </div>

        {{-- ===== ANTREAN ===== --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

            {{-- Safety Permit --}}
            <section class="table-card flex flex-col">
                <div class="flex items-start justify-between gap-3 px-4 md:px-6 py-4 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-accent-orange"></span>
                            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-800">Safety Permit</h3>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Permit menunggu review staff</p>
                    </div>
                    <a href="/admin/approvals" class="text-sm font-semibold text-inka-navy hover:underline whitespace-nowrap">Lihat Semua</a>
                </div>

                @if($permits->isEmpty())
                    <div class="flex flex-col items-center justify-center py-14 px-6 text-center flex-1">
                        <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Tidak ada permit yang menunggu review.</p>
                        <p class="text-xs text-gray-400 mt-1">Permit baru akan muncul di sini.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($permits as $permit)
                            <a href="/admin/approvals/{{ $permit->id }}" class="block px-4 md:px-6 py-4 hover:bg-slate-50/70 transition-colors">
                                <div class="flex items-center justify-between gap-3 mb-1.5">
                                    <span class="text-xs font-bold text-inka-navy font-mono">{{ $permit->no_permit }}</span>
                                    <x-permit-status-badge :status="$permit->status" class="shrink-0" />
                                </div>
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $permit->nama_pekerjaan }}</p>
                                <p class="text-xs text-gray-400 mt-1 truncate">
                                    {{ optional($permit->user)->name ?? $permit->divisi_pengaju ?? '-' }}
                                    &middot; {{ $permit->kontraktor }}
                                    &middot; {{ $permit->site ?? $siteLabel }}
                                </p>
                                <div class="flex items-center justify-between gap-3 mt-2.5">
                                    <x-permit-tanggal :permit="$permit" />
                                    <span class="text-xs font-semibold text-inka-navy">Review</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="px-4 md:px-6 py-3 border-t border-gray-100 mt-auto">
                        <a href="/admin/approvals" class="text-xs font-semibold text-gray-400 hover:text-inka-navy transition-colors">Menampilkan {{ $permits->count() }} antrean teratas</a>
                    </div>
                @endif
            </section>

            {{-- Daftar Periksa --}}
            <section class="table-card flex flex-col">
                <div class="flex items-start justify-between gap-3 px-4 md:px-6 py-4 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-800">Daftar Periksa</h3>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Daftar periksa terbaru site ini</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="/admin/work-inspections/create" class="text-sm font-semibold text-inka-navy hover:underline whitespace-nowrap">Buat</a>
                        <a href="/admin/work-inspections/history" class="text-sm font-semibold text-gray-400 hover:text-inka-navy whitespace-nowrap">History</a>
                        <a href="/admin/work-inspections" class="text-sm font-semibold text-inka-navy hover:underline whitespace-nowrap">Lihat Semua</a>
                    </div>
                </div>

                @if($inspections->isEmpty())
                    <div class="flex flex-col items-center justify-center py-14 px-6 text-center flex-1">
                        <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Belum ada daftar periksa.</p>
                        <p class="text-xs text-gray-400 mt-1">Buat daftar periksa baru dari dashboard.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($inspections as $inspection)
                            <a href="/admin/work-inspections/{{ $inspection->id }}" class="block px-4 md:px-6 py-4 hover:bg-slate-50/70 transition-colors">
                                <div class="flex items-center justify-between gap-3 mb-1.5">
                                    <span class="text-xs font-bold text-inka-navy font-mono">{{ $inspection->no_periksa }}</span>
                                    @php
                                        $inspectionBadge = match($inspection->status) {
                                            \App\Models\WorkInspection::STATUS_APPROVED => 'bg-green-100 text-green-700',
                                            \App\Models\WorkInspection::STATUS_REJECTED => 'bg-red-100 text-red-700',
                                            default => 'bg-orange-100 text-orange-700',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $inspectionBadge }} shrink-0">{{ $inspection->status }}</span>
                                </div>
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $inspection->nama_pekerjaan }}</p>
                                <p class="text-xs text-gray-400 mt-1 truncate">
                                    {{ $inspection->kontraktor }}
                                    &middot; {{ $inspection->divisi_pengaju }}
                                    &middot; {{ $inspection->site ?? $siteLabel }}
                                </p>
                                <div class="flex items-center justify-between gap-3 mt-2.5">
                                    <span class="text-xs text-gray-400">{{ ($inspection->submitted_at ?? $inspection->created_at)?->format('d/m/Y H:i') }}</span>
                                    <span class="text-xs font-semibold text-inka-navy">Lihat</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="px-4 md:px-6 py-3 border-t border-gray-100 mt-auto">
                        <a href="/admin/work-inspections" class="text-xs font-semibold text-gray-400 hover:text-inka-navy transition-colors">Menampilkan {{ $inspections->count() }} antrean teratas</a>
                    </div>
                @endif
            </section>

        </div>
    @else
        {{-- ===== MANAGER ===== --}}
        <section class="relative overflow-hidden rounded-2xl bg-inka-navy p-6 sm:p-7 mb-6 text-white shadow-sm">
            <div class="pointer-events-none absolute -right-16 -top-20 h-56 w-56 rounded-full bg-white/5"></div>
            <div class="pointer-events-none absolute -right-4 bottom-[-70px] h-44 w-44 rounded-full bg-blue-400/20 blur-xl"></div>
            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="min-w-0">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.12em] text-white/90">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Manager HSE &middot; Semua Site
                    </span>
                    <h1 class="mt-3 text-2xl sm:text-[28px] font-bold leading-tight">{{ $greeting }}, {{ auth()->user()->name }}</h1>
                    <p class="mt-1 text-sm text-white/60">
                        {{ $todayLabel }} &mdash;
                        @if($counts['permit_pending'] + $counts['hse_review'] + $counts['inspection_pending'] > 0)
                            <span class="font-semibold text-white">{{ $counts['permit_pending'] }} permit</span>,
                            <span class="font-semibold text-white">{{ $counts['inspection_pending'] }} daftar periksa</span>, dan
                            <span class="font-semibold text-white">{{ $counts['hse_review'] }} HSE Plan</span> menunggu persetujuan Anda.
                        @else
                            Semua antrean sudah tertangani. Kerja bagus, tetap utamakan keselamatan!
                        @endif
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <a href="/admin/approvals"
                       class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-inka-navy hover:bg-white/90 transition-colors">
                        Review Permit
                        @if($counts['permit_pending'] > 0)
                            <span class="inline-flex min-w-[20px] items-center justify-center rounded-full bg-accent-orange px-1.5 py-0.5 text-[11px] font-bold text-white">{{ $counts['permit_pending'] }}</span>
                        @endif
                    </a>
                    <a href="/admin/work-inspections"
                       class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/5 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/10 transition-colors">
                        Review Daftar Periksa
                        @if($counts['inspection_pending'] > 0)
                            <span class="inline-flex min-w-[20px] items-center justify-center rounded-full bg-white/20 px-1.5 py-0.5 text-[11px] font-bold text-white">{{ $counts['inspection_pending'] }}</span>
                        @endif
                    </a>
                    <a href="/admin/hse-plans"
                       class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/5 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/10 transition-colors">
                        Review HSE
                        @if($counts['hse_review'] > 0)
                            <span class="inline-flex min-w-[20px] items-center justify-center rounded-full bg-white/20 px-1.5 py-0.5 text-[11px] font-bold text-white">{{ $counts['hse_review'] }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-2 xl:grid-cols-6 gap-4 mb-6">
            <a href="/admin/approvals"
               class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 hover:shadow-md hover:border-blue-200 transition-all">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="text-[11px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide">Permit Pending</p>
                    <span class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-blue-500">{{ $counts['permit_pending'] }}</p>
                <p class="text-[11px] sm:text-xs text-gray-400 mt-1">Menunggu review Anda</p>
            </a>

            <a href="/admin/approvals?date=today"
               class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 hover:shadow-md hover:border-gray-200 transition-all">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="text-[11px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide">Masuk Hari Ini</p>
                    <span class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-700">{{ $counts['permit_today'] }}</p>
                <p class="text-[11px] sm:text-xs text-gray-400 mt-1">Antrean Safety Permit</p>
            </a>

            <a href="/admin/work-inspections"
               class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 hover:shadow-md hover:border-purple-200 transition-all">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="text-[11px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide">Daftar Periksa</p>
                    <span class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-purple-500">{{ $counts['inspection_pending'] }}</p>
                <p class="text-[11px] sm:text-xs text-gray-400 mt-1">Menunggu persetujuan Anda</p>
            </a>

            <a href="/admin/hse-plans"
               class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 hover:shadow-md hover:border-orange-200 transition-all">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="text-[11px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide">HSE Review</p>
                    <span class="w-7 h-7 rounded-lg bg-orange-50 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-orange-500">{{ $counts['hse_review'] }}</p>
                <p class="text-[11px] sm:text-xs text-gray-400 mt-1">HSE Plan menunggu review</p>
            </a>

            <a href="/admin/hse-plans/history?status={{ \App\Models\HsePlan::STATUS_APPROVED }}"
               class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 hover:shadow-md hover:border-green-200 transition-all">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="text-[11px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide">HSE Disetujui</p>
                    <span class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-green-500">{{ $counts['hse_approved'] }}</p>
                <p class="text-[11px] sm:text-xs text-gray-400 mt-1">Total HSE Plan disetujui</p>
            </a>

            <a href="/admin/hse-plans/history?status={{ \App\Models\HsePlan::STATUS_REJECTED }}"
               class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 hover:shadow-md hover:border-red-200 transition-all">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="text-[11px] sm:text-xs font-semibold text-gray-400 uppercase tracking-wide">HSE Ditolak</p>
                    <span class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-red-500">{{ $counts['hse_rejected'] }}</p>
                <p class="text-[11px] sm:text-xs text-gray-400 mt-1">Total HSE Plan ditolak</p>
            </a>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
            {{-- Safety Permit --}}
            <section class="table-card flex flex-col">
                <div class="flex items-start justify-between gap-3 px-4 md:px-6 py-4 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-accent-orange"></span>
                            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-800">Safety Permit</h3>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Permit menunggu review manager</p>
                    </div>
                    <a href="/admin/approvals" class="text-sm font-semibold text-inka-navy hover:underline whitespace-nowrap">Lihat Semua</a>
                </div>

                @if($permits->isEmpty())
                    <div class="flex flex-col items-center justify-center py-14 px-6 text-center flex-1">
                        <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Tidak ada permit yang menunggu review.</p>
                        <p class="text-xs text-gray-400 mt-1">Permit baru akan muncul di sini.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($permits as $permit)
                            <a href="/admin/approvals/{{ $permit->id }}" class="block px-4 md:px-6 py-4 hover:bg-slate-50/70 transition-colors">
                                <div class="flex items-center justify-between gap-3 mb-1.5">
                                    <span class="text-xs font-bold text-inka-navy font-mono">{{ $permit->no_permit }}</span>
                                    <x-permit-status-badge :status="$permit->status" class="shrink-0" />
                                </div>
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $permit->nama_pekerjaan }}</p>
                                <p class="text-xs text-gray-400 mt-1 truncate">
                                    {{ optional($permit->user)->name ?? $permit->divisi_pengaju ?? '-' }}
                                    &middot; {{ $permit->kontraktor }}
                                    &middot; {{ $permit->site ?? 'Madiun' }}
                                </p>
                                <div class="flex items-center justify-between gap-3 mt-2.5">
                                    <x-permit-tanggal :permit="$permit" />
                                    <span class="text-xs font-semibold text-inka-navy">Review</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="px-4 md:px-6 py-3 border-t border-gray-100 mt-auto">
                        <a href="/admin/approvals" class="text-xs font-semibold text-gray-400 hover:text-inka-navy transition-colors">Menampilkan {{ $permits->count() }} antrean teratas</a>
                    </div>
                @endif
            </section>

            {{-- HSE Plan --}}
            <section class="table-card flex flex-col">
                <div class="flex items-start justify-between gap-3 px-4 md:px-6 py-4 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-800">HSE Plan</h3>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">HSE Plan menunggu persetujuan manager</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="/admin/hse-plans/history" class="text-sm font-semibold text-gray-400 hover:text-inka-navy whitespace-nowrap">History</a>
                        <a href="/admin/hse-plans" class="text-sm font-semibold text-inka-navy hover:underline whitespace-nowrap">Lihat Semua</a>
                    </div>
                </div>

                @if($hsePlans->isEmpty())
                    <div class="flex flex-col items-center justify-center py-14 px-6 text-center flex-1">
                        <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Tidak ada HSE Plan yang menunggu review.</p>
                        <p class="text-xs text-gray-400 mt-1">Pengajuan HSE Plan baru akan muncul di sini.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($hsePlans as $hsePlan)
                            <a href="/admin/hse-plans/{{ $hsePlan->id }}" class="block px-4 md:px-6 py-4 hover:bg-slate-50/70 transition-colors">
                                <div class="flex items-center justify-between gap-3 mb-1.5">
                                    <span class="text-xs font-bold text-inka-navy font-mono">{{ $hsePlan->no_hse_plan }}</span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-orange-100 text-orange-700 shrink-0">Menunggu Review</span>
                                </div>
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $hsePlan->deskripsi_pekerjaan }}</p>
                                <p class="text-xs text-gray-400 mt-1 truncate">
                                    {{ $hsePlan->nama_kontraktor }}
                                    &middot; {{ $hsePlan->divisi_pengaju }}
                                    &middot; {{ $hsePlan->site }}
                                </p>
                                <div class="flex items-center justify-between gap-3 mt-2.5">
                                    <span class="text-xs text-gray-400">{{ ($hsePlan->submitted_at ?? $hsePlan->created_at)?->format('d/m/Y H:i') }}</span>
                                    <span class="text-xs font-semibold text-inka-navy">Review</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="px-4 md:px-6 py-3 border-t border-gray-100 mt-auto">
                        <a href="/admin/hse-plans" class="text-xs font-semibold text-gray-400 hover:text-inka-navy transition-colors">Menampilkan {{ $hsePlans->count() }} antrean teratas</a>
                    </div>
                @endif
            </section>
        </div>

        {{-- Daftar Periksa --}}
        <section class="table-card mt-5">
            <div class="flex items-start justify-between gap-3 px-4 md:px-6 py-4 border-b border-gray-100">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                        <h3 class="text-sm font-bold uppercase tracking-wide text-gray-800">Daftar Periksa</h3>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Daftar periksa menunggu persetujuan Manager HSE</p>
                </div>
                <a href="/admin/work-inspections" class="text-sm font-semibold text-inka-navy hover:underline whitespace-nowrap">Lihat Semua</a>
            </div>

            @if($inspections->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 px-6 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Tidak ada daftar periksa yang menunggu persetujuan.</p>
                    <p class="text-xs text-gray-400 mt-1">Daftar periksa baru dari Staff HSE akan muncul di sini.</p>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($inspections as $inspection)
                        <a href="/admin/work-inspections/{{ $inspection->id }}" class="block px-4 md:px-6 py-4 hover:bg-slate-50/70 transition-colors">
                            <div class="flex items-center justify-between gap-3 mb-1.5">
                                <span class="text-xs font-bold text-inka-navy font-mono">{{ $inspection->no_periksa }}</span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-orange-100 text-orange-700 shrink-0">Review Manager</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $inspection->nama_pekerjaan }}</p>
                            <p class="text-xs text-gray-400 mt-1 truncate">
                                {{ $inspection->kontraktor }}
                                &middot; {{ $inspection->divisi_pengaju }}
                                &middot; {{ $inspection->site }}
                            </p>
                            <div class="flex items-center justify-between gap-3 mt-2.5">
                                <span class="text-xs text-gray-400">{{ ($inspection->submitted_at ?? $inspection->created_at)?->format('d/m/Y H:i') }}</span>
                                <span class="text-xs font-semibold text-inka-navy">Review</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="px-4 md:px-6 py-3 border-t border-gray-100">
                    <a href="/admin/work-inspections" class="text-xs font-semibold text-gray-400 hover:text-inka-navy transition-colors">Menampilkan {{ $inspections->count() }} antrean teratas</a>
                </div>
            @endif
        </section>
    @endif
</x-app-layout>
