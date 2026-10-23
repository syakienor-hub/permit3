<x-app-layout>
    <x-slot name="title">Daftar Periksa Izin</x-slot>

    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-base font-medium px-5 py-3 rounded-2xl">
            <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 text-sm font-medium px-5 py-3 rounded-2xl">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <a href="/admin/work-inspections?status={{ \App\Models\WorkInspection::STATUS_PENDING }}"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-orange-200 transition-all">
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Menunggu Verifikasi</p>
            <p class="text-3xl font-bold text-orange-500 mt-2">{{ $counts['pending'] }}</p>
        </a>
        <a href="/admin/work-inspections?status={{ \App\Models\WorkInspection::STATUS_APPROVED }}"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-green-200 transition-all">
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Disetujui</p>
            <p class="text-3xl font-bold text-green-500 mt-2">{{ $counts['approved'] }}</p>
        </a>
        <a href="/admin/work-inspections?status={{ \App\Models\WorkInspection::STATUS_REJECTED }}"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-red-200 transition-all">
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Ditolak</p>
            <p class="text-3xl font-bold text-red-500 mt-2">{{ $counts['rejected'] }}</p>
        </a>
    </div>

    <form method="GET" action="/admin/work-inspections" class="bg-white rounded-2xl border border-gray-100 shadow-sm px-4 md:px-6 py-4 mb-5 flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari no. periksa, pekerjaan, kontraktor..."
                class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy">
        </div>

        <select name="status" class="border border-gray-200 rounded-xl text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy text-gray-600">
            <option value="">Status: Menunggu Verifikasi</option>
            <option value="Semua" @selected(request('status') === 'Semua')>Semua Status</option>
            @foreach([\App\Models\WorkInspection::STATUS_PENDING, \App\Models\WorkInspection::STATUS_APPROVED, \App\Models\WorkInspection::STATUS_REJECTED] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
            @endforeach
        </select>

        <select name="site" class="border border-gray-200 rounded-xl text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy text-gray-600">
            <option value="">Site: Semua</option>
            @foreach(\App\Models\WorkInspection::SITES as $site)
                <option value="{{ $site }}" @selected(request('site') === $site)>Site {{ $site }}</option>
            @endforeach
        </select>

        <button type="submit" class="px-4 py-2 bg-inka-navy text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity">Cari</button>

        @if(request('search') || request('status') || request('site'))
            <a href="/admin/work-inspections" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">Reset</a>
        @endif
    </form>

    <div class="table-card">
        <div class="flex items-center justify-between px-4 md:px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Daftar Periksa Izin Pekerjaan Berisiko Tinggi</h3>
                <p class="text-xs text-gray-400 mt-0.5">Form K3LH IV-01.013 — prasyarat sebelum Safety Permit</p>
            </div>
            <a href="/admin/work-inspections/history" class="text-xs font-semibold text-inka-navy hover:underline">Lihat History</a>
        </div>

        @if($inspections->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center mb-5">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-base font-semibold text-gray-700">Belum ada daftar periksa.</p>
                <p class="text-sm text-gray-400 mt-1">Pengajuan baru akan muncul di sini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="data-table min-w-[900px]">
                    <thead>
                        <tr>
                            <th>No. Periksa</th>
                            <th>Tanggal</th>
                            <th>Pekerjaan</th>
                            <th>Kontraktor</th>
                            <th>Site</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inspections as $inspection)
                            @php
                                $badge = match($inspection->status) {
                                    \App\Models\WorkInspection::STATUS_APPROVED => 'bg-green-100 text-green-700',
                                    \App\Models\WorkInspection::STATUS_REJECTED => 'bg-red-100 text-red-700',
                                    default => 'bg-orange-100 text-orange-700',
                                };
                                $label = match($inspection->status) {
                                    \App\Models\WorkInspection::STATUS_APPROVED => 'Disetujui',
                                    \App\Models\WorkInspection::STATUS_REJECTED => 'Ditolak',
                                    default => 'Menunggu Verifikasi',
                                };
                            @endphp
                            <tr class="cursor-pointer transition-colors" onclick="window.location='/admin/work-inspections/{{ $inspection->id }}'">
                                <td class="whitespace-nowrap">
                                    <p class="font-bold text-inka-navy text-sm">{{ $inspection->no_periksa }}</p>
                                    @if($inspection->permit)
                                        <p class="text-[10px] text-gray-400">Permit: {{ $inspection->permit->no_permit }}</p>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap text-sm text-gray-600">{{ ($inspection->submitted_at ?? $inspection->created_at)?->format('d/m/Y') }}</td>
                                <td class="text-sm text-gray-700">{{ $inspection->nama_pekerjaan }}</td>
                                <td class="text-sm text-gray-600">{{ $inspection->kontraktor }}</td>
                                <td class="text-sm text-gray-600">{{ $inspection->site }}</td>
                                <td><span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $badge }}">{{ $label }}</span></td>
                                <td class="text-right">
                                    <a href="/admin/work-inspections/{{ $inspection->id }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-inka-navy text-white hover:opacity-90" onclick="event.stopPropagation()">Lihat</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($inspections->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $inspections->links() }}</div>
            @endif
        @endif
    </div>
</x-app-layout>
