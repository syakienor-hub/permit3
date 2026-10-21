<x-app-layout>
    <x-slot name="title">HSE Plan</x-slot>

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
        <a href="/admin/hse-plans?status={{ \App\Models\HsePlan::STATUS_REVIEW }}"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-orange-200 transition-all">
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Menunggu Review</p>
            <p class="text-3xl font-bold text-orange-500 mt-2">{{ $counts['review'] }}</p>
        </a>
        <a href="/admin/hse-plans?status={{ \App\Models\HsePlan::STATUS_APPROVED }}"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-green-200 transition-all">
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Disetujui</p>
            <p class="text-3xl font-bold text-green-500 mt-2">{{ $counts['approved'] }}</p>
        </a>
        <a href="/admin/hse-plans?status={{ \App\Models\HsePlan::STATUS_REJECTED }}"
           class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-red-200 transition-all">
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Ditolak</p>
            <p class="text-3xl font-bold text-red-500 mt-2">{{ $counts['rejected'] }}</p>
        </a>
    </div>

    <div class="table-card">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 px-4 md:px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Daftar HSE Plan</h3>
                <p class="text-sm text-gray-400 mt-0.5">Pengajuan HSE Plan dari kontraktor dan divisi pengaju</p>
            </div>
            <form method="GET" action="/admin/hse-plans" class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor, kontraktor..."
                    class="form-input !py-2 !text-sm sm:w-56">
                <select name="site" class="form-input !py-2 !text-sm sm:w-36">
                    <option value="">Semua Site</option>
                    @foreach(\App\Models\HsePlan::SITES as $site)
                        <option value="{{ $site }}" @selected(request('site') === $site)>{{ $site }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-input !py-2 !text-sm sm:w-40">
                    <option value="">Menunggu Review</option>
                    <option value="Semua" @selected(request('status') === 'Semua')>Semua Status</option>
                    @foreach([\App\Models\HsePlan::STATUS_REVIEW, \App\Models\HsePlan::STATUS_APPROVED, \App\Models\HsePlan::STATUS_REJECTED] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-inka-navy text-white text-sm font-semibold hover:opacity-90 transition-opacity">Cari</button>
            </form>
        </div>

        @if($hsePlans->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center mb-5">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-base font-semibold text-gray-700">Belum ada HSE Plan.</p>
                <p class="text-sm text-gray-400 mt-1">Pengajuan baru akan muncul di sini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="data-table min-w-[900px]">
                    <thead>
                        <tr>
                            <th>No. HSE Plan</th>
                            <th>Tanggal</th>
                            <th>Kontraktor</th>
                            <th>Pekerjaan</th>
                            <th>Site</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hsePlans as $hsePlan)
                            @php
                                $badge = match($hsePlan->status) {
                                    \App\Models\HsePlan::STATUS_APPROVED => 'bg-green-100 text-green-700',
                                    \App\Models\HsePlan::STATUS_REJECTED => 'bg-red-100 text-red-700',
                                    default => 'bg-orange-100 text-orange-700',
                                };
                                $label = match($hsePlan->status) {
                                    \App\Models\HsePlan::STATUS_APPROVED => 'Disetujui',
                                    \App\Models\HsePlan::STATUS_REJECTED => 'Ditolak',
                                    default => 'Menunggu Review',
                                };
                            @endphp
                            <tr class="cursor-pointer transition-colors" onclick="window.location='/admin/hse-plans/{{ $hsePlan->id }}'">
                                <td class="whitespace-nowrap">
                                    <span class="font-semibold text-inka-navy font-mono">{{ $hsePlan->no_hse_plan }}</span>
                                </td>
                                <td class="whitespace-nowrap text-gray-600">{{ ($hsePlan->submitted_at ?? $hsePlan->created_at)?->format('d/m/Y H:i') }}</td>
                                <td class="text-gray-700">{{ $hsePlan->nama_kontraktor }}</td>
                                <td class="text-gray-600 max-w-[260px] truncate">{{ $hsePlan->deskripsi_pekerjaan }}</td>
                                <td class="whitespace-nowrap"><x-permit-site-badge :site="$hsePlan->site" /></td>
                                <td class="whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ $label }}</span>
                                </td>
                                <td class="text-right whitespace-nowrap" onclick="event.stopPropagation()">
                                    <a href="/admin/hse-plans/{{ $hsePlan->id }}"
                                       class="inline-flex items-center gap-1.5 bg-inka-navy text-white text-[11px] font-semibold px-3.5 py-2 rounded-lg shadow-sm hover:opacity-90 transition-colors">
                                        {{ $hsePlan->status === \App\Models\HsePlan::STATUS_REVIEW ? 'Review' : 'Lihat' }}
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
            <div class="px-4 md:px-6 py-4 border-t border-gray-100">
                {{ $hsePlans->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
