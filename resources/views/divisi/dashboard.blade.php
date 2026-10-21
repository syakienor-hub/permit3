<x-app-layout>
    <x-slot name="title">Dashboard — Divisi</x-slot>

    {{-- ===== FLASH SUCCESS MESSAGE ===== --}}
    @if(session('success'))
    <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-base font-medium px-5 py-3 rounded-2xl">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- ===== WIDGET RINGKASAN (4 KARTU KLIKABLE) ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- Draft --}}
        <a href="/divisi/history?status=Draft"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-gray-200 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Draft</p>
                <div class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-bold text-gray-700">{{ $counts['draft'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Belum diajukan</p>
        </a>

        {{-- Submitted --}}
        <a href="/divisi/history?status=Review Staff"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-yellow-200 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Submitted</p>
                <div class="w-8 h-8 rounded-xl bg-yellow-50 flex items-center justify-center group-hover:bg-yellow-100 transition-colors">
                    <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-bold text-yellow-500">{{ $counts['submitted'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Dalam proses review</p>
        </a>

        {{-- Active --}}
        <a href="/divisi/history?status=Active"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-green-200 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Active</p>
                <div class="w-8 h-8 rounded-xl bg-green-50 flex items-center justify-center group-hover:bg-green-100 transition-colors">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-bold text-green-500">{{ $counts['active'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Permit berlaku</p>
        </a>

        {{-- Closed --}}
        <a href="/divisi/history?status=Closed"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-gray-200 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Closed</p>
                <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center group-hover:bg-slate-200 transition-colors">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-bold text-slate-400">{{ $counts['closed'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Pekerjaan selesai</p>
        </a>

    </div>

    {{-- ===== SECTION: PERMIT TERBARU ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">

        {{-- Header section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 md:px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Permit Terbaru</h3>
                <p class="text-sm text-gray-400 mt-0.5">5 pengajuan terakhir milik divisi Anda</p>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 sm:gap-5">
                <a href="/divisi/history" class="text-base font-semibold text-inka-navy hover:underline">
                    Lihat Semua
                </a>
                <a href="/divisi/permits/create"
                   class="inline-flex items-center justify-center gap-2 bg-inka-navy text-white text-base font-semibold px-4 py-2 rounded-xl hover:opacity-90 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Permit
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
                <p class="text-base font-semibold text-gray-700">Belum ada pengajuan permit.</p>
                <p class="text-sm text-gray-400 mt-1 mb-6">Mulai dengan membuat permit baru.</p>
                <a href="/divisi/permits/create"
                   class="inline-flex items-center gap-2 bg-inka-navy text-white text-base font-semibold px-5 py-2.5 rounded-xl hover:opacity-90 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Permit
                </a>
            </div>

        @else
            {{-- Desktop/laptop: tabel tetap --}}
            <div class="overflow-x-auto hidden md:block">
                <table class="w-full text-left text-base min-w-[880px]">
                    <thead>
                        <tr class="text-sm text-gray-400 uppercase tracking-wide border-b border-gray-100 bg-gray-50/60">
                            <th class="px-6 py-3 font-semibold">No. Permit</th>
                            <th class="px-6 py-3 font-semibold">Site</th>
                            <th class="px-6 py-3 font-semibold">Tipe</th>
                            <th class="px-6 py-3 font-semibold">Klasifikasi Pekerjaan</th>
                            <th class="px-6 py-3 font-semibold">Divisi</th>
                            <th class="px-6 py-3 font-semibold">Kontraktor</th>
                            <th class="px-6 py-3 font-semibold">Lokasi</th>
                            <th class="px-6 py-3 font-semibold">Tanggal Permit</th>
                            <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($permits as $permit)
                        @php
                            $statusMap = [
                                'Draft'                    => 'bg-gray-100 text-gray-600',
                                'Submitted'                => 'bg-blue-100 text-blue-700',
                                'Review Staff'             => 'bg-orange-100 text-orange-700',
                                'Review Manager'           => 'bg-orange-100 text-orange-700',
                                'Revision'                 => 'bg-red-100 text-red-700',
                                'Active'                   => 'bg-green-100 text-green-700',
                                'Closed'                   => 'bg-slate-100 text-slate-600',
                            ];
                            $badge = $statusMap[$permit->status] ?? 'bg-gray-100 text-gray-600';
                            $canEdit = in_array($permit->status, ['Draft', 'Revision']);
                        @endphp
                        <tr class="hover:bg-blue-50/30 transition-colors cursor-pointer"
                            onclick="window.location='/divisi/permits/{{ $permit->id }}'">
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <a href="/divisi/permits/{{ $permit->id }}"
                                   class="font-semibold text-inka-navy hover:underline block"
                                   onclick="event.stopPropagation()">
                                    {{ $permit->no_permit }}
                                </a>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $badge }} mt-1">
                                    {{ str_starts_with($permit->status, 'Review') ? 'Menunggu ' . $permit->status : $permit->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap"><x-permit-site-badge :site="$permit->site" /></td>
                            <td class="px-6 py-3.5 whitespace-nowrap"><x-permit-tipe-badge :tipe="$permit->tipe" /></td>
                            <td class="px-6 py-3.5"><x-permit-klasifikasi-badges :permit="$permit" /></td>
                            <td class="px-6 py-3.5 text-gray-700 font-medium whitespace-nowrap">{{ optional($permit->user)->name ?? Auth::user()->name }}</td>
                            <td class="px-6 py-3.5 text-gray-600 text-sm">{{ $permit->kontraktor }}</td>
                            <td class="px-6 py-3.5 text-gray-600 text-sm">{{ $permit->lokasi ?? '—' }}</td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <x-permit-tanggal :permit="$permit" />
                            </td>
                            <td class="px-6 py-3.5 text-right whitespace-nowrap" onclick="event.stopPropagation()">
                                <a href="/divisi/permits/{{ $permit->id }}"
                                   class="inline-flex items-center gap-1 text-sm font-semibold text-inka-navy hover:underline">
                                    {{ $canEdit ? 'Edit' : 'Lihat Detail' }}
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- HP saja: card vertikal, tanpa tabel lebar --}}
            <div class="md:hidden divide-y divide-gray-50">
                @foreach($permits as $permit)
                @php
                    $mBadge2 = $statusMap[$permit->status] ?? 'bg-gray-100 text-gray-600';
                    $mCanEdit2 = in_array($permit->status, ['Draft', 'Revision']);
                @endphp
                <a href="/divisi/permits/{{ $permit->id }}" class="block px-4 py-4 active:bg-blue-50/50">
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="font-semibold text-inka-navy text-sm">{{ $permit->no_permit }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $mBadge2 }} shrink-0">
                            {{ str_starts_with($permit->status, 'Review') ? 'Menunggu ' . $permit->status : $permit->status }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-800 font-medium truncate">{{ $permit->nama_pekerjaan ?? $permit->kontraktor }}</p>
                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $permit->kontraktor }} &middot; {{ $permit->lokasi ?? '—' }}</p>
                    <div class="flex items-center justify-between mt-2.5">
                        <x-permit-tanggal :permit="$permit" />
                        <span class="text-xs font-semibold text-inka-navy">{{ $mCanEdit2 ? 'Edit ›' : 'Lihat Detail ›' }}</span>
                    </div>
                </a>
                @endforeach
            </div>
        @endif
    </div>

</x-app-layout>
