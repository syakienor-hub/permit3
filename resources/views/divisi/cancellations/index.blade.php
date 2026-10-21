<x-app-layout>
    <x-slot name="title">Pembatalan Permit</x-slot>

    {{-- ===== WIDGET BANTUAN ===== --}}
    <div class="bg-red-50 rounded-2xl border border-red-100 shadow-sm px-4 md:px-6 py-4 mb-5 flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center shrink-0 mt-1">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <h3 class="text-red-800 font-bold text-sm">Pembatalan Izin Kerja</h3>
            <p class="text-xs text-red-700 mt-1">Semua permit yang belum berstatus <strong>Closed</strong> (Selesai) dapat dibatalkan di sini. Jika Anda mendapati pelanggaran K3 di lapangan atau ingin menarik pengajuan, Anda dapat langsung mencabut/membatalkan izin kerja tersebut.</p>
        </div>
    </div>

    {{-- ===== TABEL ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between px-4 md:px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Daftar Permit Aktif</h3>
            </div>
        </div>

        @if($permits->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-700">Tidak ada permit yang dapat dibatalkan.</p>
                <p class="text-xs text-gray-400 mt-1">Permit yang sudah ditutup tidak dapat dibatalkan kembali.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm min-w-[720px]">
                    <thead>
                        <tr class="text-xs text-gray-400 uppercase tracking-wide border-b border-gray-100 bg-gray-50/60">
                            <th class="px-6 py-3 font-semibold">No. Permit</th>
                            <th class="px-6 py-3 font-semibold">Nama Pekerjaan</th>
                            <th class="px-6 py-3 font-semibold">Kontraktor</th>
                            <th class="px-6 py-3 font-semibold">Status</th>
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
                            <td class="px-6 py-3.5 text-gray-700">{{ $permit->nama_pekerjaan }}</td>
                            <td class="px-6 py-3.5 text-gray-500 text-xs">{{ $permit->kontraktor }}</td>
                            <td class="px-6 py-3.5">
                                @php
                                    $statusMap = [
                                        'Draft'                    => 'bg-gray-100 text-gray-600',
                                        'Submitted'                => 'bg-blue-100 text-blue-700',
                                        'Review Staff'             => 'bg-orange-100 text-orange-700',
                                        'Review Manager'           => 'bg-orange-100 text-orange-700',
                                        'Revision'                 => 'bg-red-100 text-red-700',
                                        'Active'                   => 'bg-green-100 text-green-700',
                                    ];
                                    $badge = $statusMap[$permit->status] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $badge }}">{{ str_starts_with($permit->status, 'Review') ? 'Menunggu ' . $permit->status : $permit->status }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-gray-400 text-xs">
                                {{ $permit->updated_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                <a href="/divisi/cancellations/{{ $permit->id }}"
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
