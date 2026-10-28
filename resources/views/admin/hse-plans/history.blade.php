<x-app-layout>
    <x-slot name="title">History HSE Plan</x-slot>

    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-base font-medium px-5 py-3 rounded-2xl">
            <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <a href="/admin/hse-plans/history?status={{ \App\Models\HsePlan::STATUS_APPROVED }}"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-green-200 transition-all">
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Disetujui</p>
            <p class="text-3xl font-bold text-green-500 mt-2">{{ $counts['approved'] }}</p>
            <p class="text-xs text-gray-400 mt-1">HSE Plan yang telah disetujui manager</p>
        </a>
        <a href="/admin/hse-plans/history?status={{ \App\Models\HsePlan::STATUS_REJECTED }}"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-red-200 transition-all">
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Ditolak</p>
            <p class="text-3xl font-bold text-red-500 mt-2">{{ $counts['rejected'] }}</p>
            <p class="text-xs text-gray-400 mt-1">HSE Plan yang ditolak beserta alasannya</p>
        </a>
    </div>

    <form method="GET" action="/admin/hse-plans/history" class="bg-white rounded-2xl border border-gray-100 shadow-sm px-4 md:px-6 py-4 mb-5 flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari no. HSE Plan, pekerjaan, kontraktor..."
                class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy">
        </div>


        <select name="status"
        class="text-xs font-semibold border border-gray-200 rounded-lg bg-white text-gray-700 pl-3 pr-10 py-2 min-w-[145px] focus:outline-none focus:border-inka-navy">

    <option value="">Semua Status</option>

    <option value="{{ \App\Models\HsePlan::STATUS_APPROVED }}"
            @selected(request('status') === \App\Models\HsePlan::STATUS_APPROVED)>
        Disetujui
    </option>

    <option value="{{ \App\Models\HsePlan::STATUS_REJECTED }}"
            @selected(request('status') === \App\Models\HsePlan::STATUS_REJECTED)>
        Ditolak
    </option>
</select>


        <select name="site" class="border border-gray-200 rounded-xl text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy text-gray-600">
            <option value="">Semua Site</option>
            @foreach(\App\Models\HsePlan::SITES as $site)
                <option value="{{ $site }}" @selected(request('site') === $site)>Site {{ $site }}</option>
            @endforeach
        </select>

        <button type="submit" class="px-4 py-2 bg-inka-navy text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity">
            Cari
        </button>

        @if(request('search') || request('status') || request('site'))
            <a href="/admin/hse-plans/history" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">Reset</a>
        @endif
    </form>

    <div class="table-card">
        <div class="flex items-center justify-between px-4 md:px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-semibold text-gray-800">HSE Plan yang Sudah Direview</h3>
                <p class="text-xs text-gray-400 mt-0.5">Riwayat persetujuan dan penolakan HSE Plan</p>
            </div>
            <a href="/admin/hse-plans" class="text-sm font-semibold text-inka-navy hover:underline">Review HSE</a>
        </div>
    
{{-- DROPDOWN JUMLAH DATA HISTORY --}}
<div class="px-4 md:px-6 py-3 border-b border-gray-100 bg-white">
    <form method="GET"
          action="{{ url('/admin/hse-plans/history') }}"
          class="flex flex-wrap items-center gap-2">

        @foreach(['search', 'status', 'site'] as $filter)
            @if(request()->filled($filter))
                <input type="hidden"
                       name="{{ $filter }}"
                       value="{{ request($filter) }}">
            @endif
        @endforeach

        <label for="hse-per-page" class="text-xs sm:text-sm text-gray-600">
            Tampilkan
        </label>

        <select id="hse-per-page"
                name="per_page"
                onchange="this.form.submit()"
                class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs sm:text-sm font-semibold text-gray-700 focus:border-inka-navy focus:outline-none">

            @foreach([5, 10, 25, 50] as $jumlah)
                <option value="{{ $jumlah }}"
                        @selected((int) request('per_page', 5) === $jumlah)>
                    {{ $jumlah }}
                </option>
            @endforeach
        </select>

        <span class="text-xs sm:text-sm text-gray-600">
            data per halaman
        </span>
    </form>
</div>

        @if($hsePlans->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center mb-5">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-base font-semibold text-gray-700">Belum ada HSE Plan yang direview.</p>
                <p class="text-sm text-gray-400 mt-1">HSE Plan yang disetujui atau ditolak akan muncul di sini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="data-table min-w-[980px]">
                    <thead>
                        <tr>
                            <th>No. HSE Plan</th>
                            <th>Tanggal Review</th>
                            <th>Kontraktor</th>
                            <th>Pekerjaan</th>
                            <th>Site</th>
                            <th>Status</th>
                            <th>Direview Oleh</th>
                            <th class="text-right">Aksi</th>
                            <th class="text-right">PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hsePlans as $hsePlan)
                            @php
                                $badge = $hsePlan->status === \App\Models\HsePlan::STATUS_APPROVED
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700';
                                $label = $hsePlan->status === \App\Models\HsePlan::STATUS_APPROVED ? 'Disetujui' : 'Ditolak';
                            @endphp
                            <tr class="cursor-pointer transition-colors" onclick="window.location='/admin/hse-plans/{{ $hsePlan->id }}'">
                                <td class="whitespace-nowrap">
                                    <span class="font-semibold text-inka-navy font-mono">{{ $hsePlan->no_hse_plan }}</span>
                                </td>
                                <td class="whitespace-nowrap text-gray-600">
                                    {{ ($hsePlan->reviewed_at ?? $hsePlan->updated_at)?->format('d/m/Y H:i') }}
                                </td>
                                <td class="text-gray-700">{{ $hsePlan->nama_kontraktor }}</td>
                                <td class="text-gray-600 max-w-[260px] truncate">{{ $hsePlan->deskripsi_pekerjaan }}</td>
                                <td class="whitespace-nowrap"><x-permit-site-badge :site="$hsePlan->site" /></td>
                                <td class="whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ $label }}</span>
                                </td>
                                <td class="text-gray-600 whitespace-nowrap">{{ optional($hsePlan->reviewer)->name ?? '-' }}</td>
                                <td class="text-right whitespace-nowrap" onclick="event.stopPropagation()">
                                    <a href="/admin/hse-plans/{{ $hsePlan->id }}"
                                       class="inline-block px-3 py-1.5 border border-gray-200 text-gray-600 text-[11px] font-bold rounded-lg hover:border-inka-navy hover:text-inka-navy transition-colors">
                                        Detail
                                    </a>
                                </td>
                                <td class="text-right whitespace-nowrap" onclick="event.stopPropagation()">
                                    <a href="/admin/hse-plans/{{ $hsePlan->id }}/pdf" target="_blank"
                                       class="inline-block px-3 py-1.5 border border-red-200 text-red-600 text-[11px] font-bold rounded-lg hover:border-red-400 hover:text-red-700 transition-colors">
                                        PDF
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 md:px-6 py-4 border-t border-gray-100">
                {{ $hsePlans->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

