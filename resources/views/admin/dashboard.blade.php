<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    @php
        $roleName = match($role) {
            'staff' => 'Staff',
            'manager' => 'Manager',
            default => 'Admin'
        };
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-gray-400">Dashboard {{ $roleName }}</p>
            <h1 class="text-xl font-bold text-gray-800 mt-1">Ringkasan HSE Hari Ini</h1>
            <p class="text-sm text-gray-400 mt-1">Pantau antrean Safety Permit dan HSE Plan dalam satu tampilan.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="/admin/approvals"
               class="inline-flex items-center justify-center gap-2 bg-inka-navy text-white text-sm font-semibold px-4 py-2.5 rounded-xl hover:opacity-90 transition-opacity">
                Review Permit
            </a>
            @if($role === 'manager')
                <a href="/admin/hse-plans"
                   class="inline-flex items-center justify-center gap-2 bg-white border border-gray-200 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-xl hover:border-inka-navy hover:text-inka-navy transition-colors">
                    Review HSE
                </a>
            @endif
        </div>
    </div>

    {{-- ===== RINGKASAN ===== --}}
    <div class="grid grid-cols-2 {{ $role === 'manager' ? 'xl:grid-cols-5' : 'sm:grid-cols-2' }} gap-4 mb-6">
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

        @if($role === 'manager')
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
        @endif
    </div>

    {{-- ===== ANTREAN PER MODUL ===== --}}
    <div class="grid grid-cols-1 {{ $role === 'manager' ? 'xl:grid-cols-2' : '' }} gap-5">

        {{-- Safety Permit --}}
        <section class="table-card flex flex-col">
            <div class="flex items-start justify-between gap-3 px-4 md:px-6 py-4 border-b border-gray-100">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-accent-orange"></span>
                        <h3 class="text-sm font-bold uppercase tracking-wide text-gray-800">Safety Permit</h3>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Permit menunggu review {{ strtolower($roleName) }}</p>
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
        @if($role === 'manager')
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
        @endif

    </div>
</x-app-layout>
