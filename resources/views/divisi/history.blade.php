<x-app-layout>
    <x-slot name="title">History Permit</x-slot>

    {{-- ===== FILTER BAR ===== --}}
    <form method="GET" action="/divisi/history" class="bg-white rounded-2xl border border-gray-100 shadow-sm px-4 md:px-6 py-4 mb-5 flex flex-wrap items-center gap-3">

        {{-- Search --}}
        <div class="relative flex-1 min-w-[200px]">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari no. permit, nama pekerjaan, kontraktor..."
                class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy">
        </div>

        {{-- Status Filter --}}
        <select name="status" class="border border-gray-200 rounded-xl text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy text-gray-600">
            <option value="">Semua Status</option>
            @foreach(['Draft','Submitted','Review Staff','Review Manager','Revision','Active','Closed','Cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>

        {{-- Tipe Filter --}}
        <select name="tipe" class="border border-gray-200 rounded-xl text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy text-gray-600">
            <option value="">Semua Tipe</option>
            <option value="Internal" {{ request('tipe') === 'Internal' ? 'selected' : '' }}>Internal</option>
            <option value="Eksternal" {{ request('tipe') === 'Eksternal' ? 'selected' : '' }}>Eksternal</option>
        </select>

        <button type="submit"
            class="px-4 py-2 bg-inka-navy text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity">
            Cari
        </button>

        @if(request('search') || request('status') || request('tipe'))
        <a href="/divisi/history" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
            Reset
        </a>
        @endif

        <div class="ml-auto">
            <a href="/divisi/permits/create"
               class="inline-flex items-center gap-2 bg-inka-navy text-white text-sm font-semibold px-4 py-2 rounded-xl hover:opacity-90 transition-opacity">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Permit
            </a>
        </div>
    </form>

    {{-- ===== TABEL HISTORY ===== --}}
    <div class="table-card">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 md:px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Seluruh Permit</h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    @if($permits->total() > 0)
                        Menampilkan {{ $permits->firstItem() }}–{{ $permits->lastItem() }} dari {{ $permits->total() }} permit
                    @else
                        Tidak ada permit ditemukan
                    @endif
                </p>
            </div>
        </div>

        @if($permits->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-700">Belum ada pengajuan permit.</p>
                <p class="text-xs text-gray-400 mt-1 mb-5">Mulai dengan membuat permit baru.</p>
                <a href="/divisi/permits/create"
                   class="inline-flex items-center gap-2 bg-inka-navy text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:opacity-90 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Permit
                </a>
            </div>
        @else
            {{-- Desktop/laptop: tabel tetap seperti semula --}}
            <div class="overflow-x-auto hidden md:block">
                <table class="data-table min-w-[880px]">
                    <thead>
                        <tr>
                            <th>No. Permit</th>
                            <th>Site</th>
                            <th>Tipe</th>
                            <th>Klasifikasi Pekerjaan</th>
                            <th>Kontraktor</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Tanggal Permit</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
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
                                'Cancelled'                => 'bg-red-600 text-white',
                            ];
                            $badge = $statusMap[$permit->status] ?? 'bg-gray-100 text-gray-600';
                            $canEdit = in_array($permit->status, ['Draft', 'Revision']);
                        @endphp
                        <tr class="transition-colors cursor-pointer"
                            onclick="window.location='/divisi/permits/{{ $permit->id }}'">
                            <td class="whitespace-nowrap">
                                <a href="/divisi/permits/{{ $permit->id }}"
                                   class="font-semibold text-inka-navy hover:underline"
                                   onclick="event.stopPropagation()">
                                    {{ $permit->no_permit }}
                                </a>
                            </td>
                            <td class="whitespace-nowrap"><x-permit-site-badge :site="$permit->site" /></td>
                            <td class="whitespace-nowrap"><x-permit-tipe-badge :tipe="$permit->tipe" /></td>
                            <td><x-permit-klasifikasi-badges :permit="$permit" /></td>
                            <td class="text-gray-600">{{ $permit->kontraktor }}</td>
                            <td class="text-gray-600">{{ $permit->lokasi ?? '—' }}</td>
                            <td class="whitespace-nowrap">
                                <x-permit-status-badge :status="$permit->status" />
                            </td>
                            <td class="whitespace-nowrap">
                                <x-permit-tanggal :permit="$permit" />
                            </td>
                            <td class="text-right whitespace-nowrap" onclick="event.stopPropagation()">
                                <a href="/divisi/permits/{{ $permit->id }}"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-inka-navy hover:underline">
                                    {{ $canEdit ? 'Edit' : 'Lihat Detail' }}
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                                <a href="/divisi/permits/{{ $permit->id }}/pdf" target="_blank"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-red-500 hover:underline ml-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    PDF
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- HP saja: card vertikal pengganti tabel lebar (tanpa scroll horizontal, layout ringan).
                 Laptop/desktop tetap pakai tabel di atas. --}}
            <div class="md:hidden divide-y divide-gray-50">
                @foreach($permits as $permit)
                @php
                    $mBadge = $statusMap[$permit->status] ?? 'bg-gray-100 text-gray-600';
                    $mCanEdit = in_array($permit->status, ['Draft', 'Revision']);
                @endphp
                <a href="/divisi/permits/{{ $permit->id }}" class="block px-4 py-4 active:bg-blue-50/50">
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="font-semibold text-inka-navy text-sm">{{ $permit->no_permit }}</span>
                        <x-permit-status-badge :status="$permit->status" class="shrink-0" />
                    </div>
                    <p class="text-sm text-gray-800 font-medium truncate">{{ $permit->nama_pekerjaan ?? $permit->kontraktor }}</p>
                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $permit->kontraktor }} &middot; {{ $permit->lokasi ?? '—' }}</p>
                    <div class="flex items-center gap-1.5 mt-2">
                        <x-permit-site-badge :site="$permit->site" />
                        <x-permit-tipe-badge :tipe="$permit->tipe" />
                    </div>
                    <div class="flex items-center justify-between mt-2.5">
                        <x-permit-tanggal :permit="$permit" />
                        <span class="text-xs font-semibold text-inka-navy">{{ $mCanEdit ? 'Edit ›' : 'Lihat Detail ›' }}</span>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($permits->hasPages())
            <div class="px-4 md:px-6 py-4 border-t border-gray-100">
                {{ $permits->links() }}
            </div>
            @endif
        @endif
    </div>

</x-app-layout>
