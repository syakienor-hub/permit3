<x-app-layout>
    <x-slot name="title">Pembatalan Permit</x-slot>

    <div class="bg-red-50 rounded-2xl border border-red-100 shadow-sm px-4 md:px-6 py-4 mb-5 flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center shrink-0 mt-1">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <h3 class="text-red-800 font-bold text-sm">Pembatalan Izin Kerja oleh HSE</h3>
            <p class="text-xs text-red-700 mt-1">Cabut permit yang sudah <strong>Active</strong> bila ditemukan pelanggaran K3, cuaca ekstrem, atau pekerjaan dihentikan. Pembatalan langsung berlaku dan tercatat di histori.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 md:px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Daftar Permit Aktif</h3>

            <form method="GET" action="/admin/cancellations" class="flex flex-col sm:flex-row gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. permit / pekerjaan..."
                    class="border border-gray-200 rounded-xl text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy text-gray-600">
                @if(auth()->user()->role === 'manager')
                    <select name="site" class="border border-gray-200 rounded-xl text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-inka-navy/20 focus:border-inka-navy text-gray-600">
                        <option value="">Semua Site</option>
                        @foreach(['Madiun', 'Banyuwangi'] as $s)
                            <option value="{{ $s }}" {{ request('site') === $s ? 'selected' : '' }}>Site {{ $s }}</option>
                        @endforeach
                    </select>
                @endif
                <button type="submit" class="px-4 py-2 rounded-xl bg-inka-navy text-white text-sm font-semibold hover:opacity-90">Cari</button>
            </form>
        </div>

        @if(session('error'))
            <div class="mx-4 md:mx-6 mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
        @endif

        @if($permits->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-700">Tidak ada permit aktif yang dapat dibatalkan.</p>
                <p class="text-xs text-gray-400 mt-1">Permit harus berstatus Active sebelum dapat dibatalkan oleh HSE.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm min-w-[760px]">
                    <thead>
                        <tr class="text-xs text-gray-400 uppercase tracking-wide border-b border-gray-100 bg-gray-50/60">
                            <th class="px-6 py-3 font-semibold">No. Permit</th>
                            <th class="px-6 py-3 font-semibold">Nama Pekerjaan</th>
                            <th class="px-6 py-3 font-semibold">Pengaju</th>
                            <th class="px-6 py-3 font-semibold">Site</th>
                            <th class="px-6 py-3 font-semibold">Tgl. Update</th>
                            <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($permits as $permit)
                        <tr class="hover:bg-red-50/30 transition-colors">
                            <td class="px-6 py-3.5">
                                <span class="font-semibold text-inka-navy">{{ $permit->no_permit }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-gray-700">
                                <p class="font-medium">{{ $permit->nama_pekerjaan }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $permit->kontraktor }}</p>
                            </td>
                            <td class="px-6 py-3.5 text-gray-600">{{ optional($permit->user)->name ?? $permit->divisi_pengaju ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-gray-500 text-xs">{{ $permit->site }}</td>
                            <td class="px-6 py-3.5 text-gray-400 text-xs">{{ $permit->updated_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3.5 text-right">
                                <a href="/admin/cancellations/{{ $permit->id }}"
                                   class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-[11px] font-bold px-3 py-1.5 rounded-lg hover:bg-red-600 hover:text-white transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Batalkan Permit
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($permits->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $permits->links() }}
            </div>
            @endif
        @endif
    </div>

</x-app-layout>
