<x-app-layout>
    <x-slot name="title">Review Permit ({{ $config['roleName'] }})</x-slot>

    {{-- ===== FLASH MESSAGES ===== --}}
    @if(session('success'))
    <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm font-medium px-5 py-3 rounded-2xl">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm font-medium px-5 py-3 rounded-2xl">
        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    @php
        $siteQuery = request('site') ? '?site=' . urlencode(request('site')) : '';
        $todayQuery = '?date=today' . (request('site') ? '&site=' . urlencode(request('site')) : '');
    @endphp

    {{-- ===== KARTU STATISTIK ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <a href="/admin/approvals{{ $siteQuery }}"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-blue-200 transition-all">
            <div class="flex items-center justify-between gap-2 mb-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Menunggu Review</p>
                <span class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="text-3xl sm:text-4xl font-bold text-blue-500">{{ $counts['pending'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Permit menunggu persetujuan Anda</p>
        </a>

        <a href="/admin/approvals{{ $todayQuery }}"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-gray-200 transition-all">
            <div class="flex items-center justify-between gap-2 mb-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Masuk Hari Ini</p>
                <span class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </span>
            </div>
            <p class="text-3xl sm:text-4xl font-bold text-gray-700">{{ $counts['today'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Antrean Safety Permit hari ini</p>
        </a>
    </div>

    {{-- ===== FILTER BAR ===== --}}
    <form method="GET" action="/admin/approvals" class="bg-white rounded-2xl border border-gray-100 shadow-sm px-4 md:px-6 py-4 mb-5 flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari no. permit, pekerjaan, kontraktor..."
                class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy">
        </div>

        <select name="status" class="border border-gray-200 rounded-xl text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy text-gray-600">
            <option value="">Status: {{ $config['expectedStatus'] }}</option>
            <option value="Semua" {{ request('status') === 'Semua' ? 'selected' : '' }}>Semua Status</option>
        </select>

        @if(auth()->user()->role === 'manager')
        <select name="site" class="border border-gray-200 rounded-xl text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy text-gray-600">
            <option value="">Site: Semua</option>
            @foreach(\App\Models\Permit::SITES as $s)
                <option value="{{ $s }}" {{ request('site') === $s ? 'selected' : '' }}>Site {{ $s }}</option>
            @endforeach
        </select>
        @elseif(auth()->user()->site)
        <span class="inline-flex items-center text-xs font-semibold text-gray-500 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2">
            Site: {{ auth()->user()->site }}
        </span>
        @endif

        <button type="submit"
            class="px-4 py-2 bg-inka-navy text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity">
            Cari
        </button>

        @if(request('search') || request('status') || request('date') || request('site'))
        <a href="/admin/approvals" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
            Reset
        </a>
        @endif
    </form>

    {{-- ===== TABEL APPROVALS ===== --}}
    <div class="table-card">
        <div class="flex items-center justify-between px-4 md:px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Daftar Antrean Review</h3>
                <p class="text-xs text-gray-400 mt-0.5">Permit yang menunggu persetujuan tingkat {{ $config['roleName'] }}.</p>
            </div>
        </div>

        @if($permits->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center mb-5">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-700">Tidak ada permit yang perlu direview saat ini.</p>
            </div>
        @else
            {{-- Desktop/laptop: tabel tetap --}}
            <div class="overflow-x-auto hidden md:block">
                <table class="data-table min-w-[920px]">
                    <thead>
                        <tr>
                            <th>No. Permit</th>
                            <th>Site</th>
                            <th>Tipe</th>
                            <th>Klasifikasi Pekerjaan</th>
                            <th>Divisi</th>
                            <th>Kontraktor</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Tanggal Permit</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permits as $permit)
                        <tr class="transition-colors cursor-pointer" onclick="window.location='/admin/approvals/{{ $permit->id }}'">
                            <td class="whitespace-nowrap"><a href="/admin/approvals/{{ $permit->id }}" class="font-semibold text-inka-navy hover:underline" onclick="event.stopPropagation()">{{ $permit->no_permit }}</a></td>
                            <td class="whitespace-nowrap"><x-permit-site-badge :site="$permit->site" /></td>
                            <td class="whitespace-nowrap"><x-permit-tipe-badge :tipe="$permit->tipe" /></td>
                            <td><x-permit-klasifikasi-badges :permit="$permit" /></td>
                            <td class="text-gray-700 font-medium whitespace-nowrap">{{ optional($permit->user)->name ?? $permit->divisi_pengaju ?? '—' }}</td>
                            <td class="text-gray-600">{{ $permit->kontraktor }}</td>
                            <td class="text-gray-600">{{ $permit->lokasi ?? '—' }}</td>
                            <td class="whitespace-nowrap">
                                @if($permit->status === $config['expectedStatus'])
                                    <x-permit-status-badge :status="$permit->status" label="Menunggu Anda" />
                                @else
                                    <x-permit-status-badge :status="$permit->status"
                                        badge-class="bg-red-50 text-red-700 border-red-200/70"
                                        :label="str_starts_with($permit->status, 'Review') ? 'Menunggu ' . $permit->status : $permit->status" />
                                @endif
                            </td>
                            <td class="whitespace-nowrap">
                                <x-permit-tanggal :permit="$permit" />
                            </td>
                            <td class="text-right whitespace-nowrap">
                                @if($permit->status === $config['expectedStatus'])
                                <a href="/admin/approvals/{{ $permit->id }}" class="inline-flex items-center gap-1.5 bg-inka-navy text-white text-[11px] font-semibold px-3.5 py-2 rounded-lg shadow-sm hover:opacity-90 transition-colors">
                                    Review
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M13 6l6 6-6 6"/>
                                    </svg>
                                </a>
                                @else
                                <a href="/admin/approvals/{{ $permit->id }}" class="inline-flex items-center text-inka-navy text-[11px] font-semibold hover:underline">
                                    Lihat
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- HP saja: card vertikal pengganti tabel 920px --}}
            <div class="md:hidden divide-y divide-gray-50">
                @foreach($permits as $permit)
                <a href="/admin/approvals/{{ $permit->id }}" class="block px-4 py-4 active:bg-orange-50/50">
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="font-semibold text-inka-navy text-sm">{{ $permit->no_permit }}</span>
                        @if($permit->status === $config['expectedStatus'])
                            <x-permit-status-badge :status="$permit->status" label="Menunggu Anda" class="shrink-0" />
                        @else
                            <x-permit-status-badge :status="$permit->status" class="shrink-0" />
                        @endif
                    </div>
                    <p class="text-sm text-gray-800 font-medium truncate">{{ $permit->nama_pekerjaan }}</p>
                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ optional($permit->user)->name ?? $permit->divisi_pengaju ?? '—' }} &middot; {{ $permit->lokasi ?? '—' }}</p>
                    <div class="flex items-center justify-between mt-2.5">
                        <x-permit-tanggal :permit="$permit" />
                        <span class="text-xs font-semibold text-inka-navy">{{ $permit->status === $config['expectedStatus'] ? 'Review ›' : 'Lihat ›' }}</span>
                    </div>
                </a>
                @endforeach
            </div>
            @if($permits->hasPages())
            <div class="px-4 md:px-6 py-4 border-t border-gray-100">
                {{ $permits->links() }}
            </div>
            @endif
        @endif
    </div>
</x-app-layout>
