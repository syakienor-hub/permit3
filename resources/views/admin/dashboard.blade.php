<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    @php
        $roleName = match($role) {
            'staff' => 'Staff',
            'manager' => 'Manager',
            default => 'Admin'
        };
    @endphp

    {{-- ===== WIDGET RINGKASAN ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

        {{-- Pending Approval (Semua Role) --}}
        <a href="/admin/approvals"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-blue-200 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Pending Review</p>
                <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-bold text-blue-500">{{ $counts['pending'] ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Menunggu persetujuan Anda</p>
        </a>

        {{-- Permit Hari Ini (Semua Role) --}}
        <a href="/admin/approvals?date=today"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-gray-200 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Masuk Hari Ini</p>
                <div class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-bold text-gray-700">{{ $counts['today'] ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Antrean masuk hari ini</p>
        </a>

    </div>

    {{-- ===== SECTION: PERMIT MENUNGGU REVIEW ===== --}}
    <div class="table-card">

        {{-- Header section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 md:px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Permit Menunggu Approval ({{ $roleName }})</h3>
                <p class="text-sm text-gray-400 mt-0.5">
                    Urutan berdasarkan waktu pengajuan terlama
                    @if($role === 'staff' && auth()->user()->site)
                        &middot; Site {{ auth()->user()->site }}
                    @elseif($role === 'manager')
                        &middot; Semua site
                    @endif
                </p>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 sm:gap-3">
                <a href="/admin/approvals" class="text-base font-semibold text-inka-navy hover:underline">
                    Lihat Semua
                </a>
                <a href="/admin/approvals"
                   class="inline-flex items-center justify-center gap-2 bg-inka-navy text-white text-base font-semibold px-4 py-2 rounded-xl hover:opacity-90 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Review Permit
                </a>
            </div>
        </div>

        @if($permits->isEmpty())
            {{-- ===== EMPTY STATE ===== --}}
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center mb-5">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-base font-semibold text-gray-700">Tidak ada permit yang menunggu review.</p>
                <p class="text-sm text-gray-400 mt-1 mb-6">Permit akan muncul di sini bila perlu ditindaklanjuti.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="data-table min-w-[880px]">
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
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permits as $permit)
                        <tr class="transition-colors cursor-pointer"
                            onclick="window.location='/admin/approvals/{{ $permit->id }}'">
                            <td class="whitespace-nowrap">
                                <a href="/admin/approvals/{{ $permit->id }}"
                                   class="font-semibold text-inka-navy hover:underline"
                                   onclick="event.stopPropagation()">
                                    {{ $permit->no_permit }}
                                </a>
                            </td>
                            <td class="whitespace-nowrap"><x-permit-site-badge :site="$permit->site" /></td>
                            <td class="whitespace-nowrap"><x-permit-tipe-badge :tipe="$permit->tipe" /></td>
                            <td><x-permit-klasifikasi-badges :permit="$permit" /></td>
                            <td class="text-gray-700 font-medium whitespace-nowrap">{{ optional($permit->user)->name ?? $permit->divisi_pengaju ?? '—' }}</td>
                            <td class="text-gray-600">{{ $permit->kontraktor }}</td>
                            <td class="text-gray-600">{{ $permit->lokasi ?? '—' }}</td>
                            <td>
                                <x-permit-tanggal :permit="$permit" />
                            </td>
                            <td class="text-right whitespace-nowrap" onclick="event.stopPropagation()">
                                <a href="/admin/approvals/{{ $permit->id }}"
                                   class="inline-flex items-center gap-1.5 bg-inka-navy text-white text-[11px] font-semibold px-3.5 py-2 rounded-lg shadow-sm hover:opacity-90 transition-colors">
                                    Review
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M13 6l6 6-6 6"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</x-app-layout>
