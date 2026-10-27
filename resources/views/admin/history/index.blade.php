<x-app-layout>
    <x-slot name="title">History Permit ({{ $roleName }})</x-slot>

    {{-- ===== FILTER BAR ===== --}}
    <form method="GET" action="/admin/history" class="bg-white rounded-2xl border border-gray-100 shadow-sm px-4 md:px-6 py-4 mb-5 flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. permit, divisi, kontraktor..." class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy">
        </div>
        <select name="status" class="border border-gray-200 rounded-xl text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy text-gray-600">
            <option value="">Semua Status</option>
            @foreach(['Draft','Menunggu Daftar Periksa','Submitted','Review Staff','Review Manager','Revision','Active','Closed','Cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
        <select name="tipe" class="border border-gray-200 rounded-xl text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy text-gray-600">
            <option value="">Semua Tipe</option>
            <option value="Internal" {{ request('tipe') === 'Internal' ? 'selected' : '' }}>Internal</option>
            <option value="Eksternal" {{ request('tipe') === 'Eksternal' ? 'selected' : '' }}>Eksternal</option>
        </select>
        @if(auth()->user()->role === 'manager')
        <select name="site" class="border border-gray-200 rounded-xl text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy text-gray-600">
            <option value="">Semua Site</option>
            @foreach(\App\Models\Permit::SITES as $s)
                <option value="{{ $s }}" {{ request('site') === $s ? 'selected' : '' }}>Site {{ $s }}</option>
            @endforeach
        </select>
        @elseif(auth()->user()->site)
        <span class="inline-flex items-center text-xs font-semibold text-gray-500 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2">
            Site: {{ auth()->user()->site }}
        </span>
        @endif
        <button type="submit" class="px-4 py-2 bg-inka-navy text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity">Cari</button>
        @if(request('search') || request('status') || request('tipe') || request('site'))
        <a href="/admin/history" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">Reset</a>
        @endif
    </form>

    {{-- ===== TABEL HISTORY ===== --}}
    <div class="table-card">
        <div class="flex items-center justify-between px-4 md:px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Semua Data Permit (Global)</h3>
        </div>
        @if($permits->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                <div class="w-16 h-16 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center mb-5">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-700">Tidak ada permit ditemukan.</p>
            </div>
        @else
            {{-- Desktop/laptop: tabel tetap --}}
            <div class="overflow-x-auto hidden md:block">
                <table class="data-table min-w-[1020px]">
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
                            <th class="text-right">PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permits as $permit)
                        @php
                            $statusMap = [
                                'Draft'                    => 'bg-gray-100 text-gray-600',
                                'Menunggu Daftar Periksa'  => 'bg-amber-100 text-amber-700',
                                'Submitted'                => 'bg-blue-100 text-blue-700',
                                'Review Staff'             => 'bg-orange-100 text-orange-700',
                                'Review Manager'           => 'bg-orange-100 text-orange-700',
                                'Revision'                 => 'bg-red-100 text-red-700',
                                'Active'                   => 'bg-green-100 text-green-700',
                                'Closed'                   => 'bg-slate-100 text-slate-600',
                                'Cancelled'                => 'bg-red-600 text-white',
                            ];
                            $badge = $statusMap[$permit->status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="transition-colors">
                            <td class="whitespace-nowrap"><span class="font-semibold text-inka-navy">{{ $permit->no_permit }}</span></td>
                            <td class="whitespace-nowrap"><x-permit-site-badge :site="$permit->site" /></td>
                            <td class="whitespace-nowrap"><x-permit-tipe-badge :tipe="$permit->tipe" /></td>
                            <td><x-permit-klasifikasi-badges :permit="$permit" /></td>
                            <td class="text-gray-700 font-medium whitespace-nowrap">{{ optional($permit->user)->name ?? $permit->divisi_pengaju ?? '—' }}</td>
                            <td class="text-gray-600">{{ $permit->kontraktor }}</td>
                            <td class="text-gray-600">{{ $permit->lokasi ?? '—' }}</td>
                            <td class="whitespace-nowrap"><x-permit-status-badge :status="$permit->status" /></td>
                            <td class="whitespace-nowrap">
                                <x-permit-tanggal :permit="$permit" />
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="/admin/approvals/{{ $permit->id }}" class="inline-block px-3 py-1.5 border border-gray-200 text-gray-600 text-[11px] font-bold rounded-lg hover:border-inka-navy hover:text-inka-navy transition-colors ml-1">Detail</a>
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="/admin/permits/{{ $permit->id }}/pdf" target="_blank"
                                   class="inline-block px-3 py-1.5 border border-red-200 text-red-600 text-[11px] font-bold rounded-lg hover:border-red-400 hover:text-red-700 transition-colors">PDF</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- HP saja: card vertikal pengganti tabel 1020px --}}
            <div class="md:hidden divide-y divide-gray-50">
                @foreach($permits as $permit)
                @php $mBadgeH = $statusMap[$permit->status] ?? 'bg-gray-100 text-gray-600'; @endphp
                <a href="/admin/approvals/{{ $permit->id }}" class="block px-4 py-4 active:bg-blue-50/50">
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="font-semibold text-gray-800 text-sm">{{ $permit->no_permit }}</span>
                        <x-permit-status-badge :status="$permit->status" class="shrink-0" />
                    </div>
                    <p class="text-sm text-gray-800 font-medium truncate">{{ $permit->nama_pekerjaan }}</p>
                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ optional($permit->user)->name ?? $permit->divisi_pengaju ?? '—' }} &middot; {{ $permit->lokasi ?? '—' }}</p>
                    <div class="flex items-center justify-between mt-2.5">
                        <x-permit-tanggal :permit="$permit" />
                        <span class="text-xs font-semibold text-inka-navy">Detail ›</span>
                    </div>
                </a>
                @endforeach
            </div>
            @if($permits->hasPages())
            <div class="px-4 md:px-6 py-4 border-t border-gray-100">{{ $permits->links() }}</div>
            @endif
        @endif
    </div>
</x-app-layout>
