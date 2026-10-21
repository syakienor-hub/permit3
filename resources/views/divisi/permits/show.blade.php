<x-app-layout>
    <x-slot name="title">Detail Permit — {{ $permit->no_permit }}</x-slot>

    @php
        $canEdit = in_array($permit->status, ['Draft', 'Revision']);
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
    @endphp

    {{-- Breadcrumb + aksi --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <div class="flex items-center gap-2 text-sm text-gray-400 min-w-0">
            <a href="/divisi/dashboard" class="hover:text-gray-600 transition-colors shrink-0">Dashboard</a>
            <span>/</span>
            <a href="/divisi/history" class="hover:text-gray-600 transition-colors shrink-0">History</a>
            <span>/</span>
            <span class="font-semibold text-gray-700 truncate">{{ $permit->no_permit }}</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 sm:gap-3">
            <a href="/divisi/history"
               class="px-4 py-2 text-sm font-semibold text-center text-gray-600 border border-gray-200 rounded-xl hover:border-gray-300 transition-colors">
                ← Kembali
            </a>
            <a href="/divisi/permits/{{ $permit->id }}/pdf" target="_blank"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:border-gray-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Lihat PDF
            </a>
            <a href="/divisi/permits/{{ $permit->id }}/pdf/download"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Unduh PDF
            </a>
            @if($canEdit)
            <a href="/divisi/permits/{{ $permit->id }}/edit"
               class="inline-flex items-center justify-center gap-2 bg-inka-navy text-white text-sm font-semibold px-4 py-2 rounded-xl hover:opacity-90 transition-opacity">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Permit
            </a>
            @endif
        </div>
    </div>

    {{-- Header kartu --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-4 md:px-6 py-5 mb-5 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-xs text-gray-400 mb-1">Nomor Permit</p>
            <div class="flex flex-wrap items-center gap-2">
                <p class="text-xl font-bold text-gray-800">{{ $permit->no_permit }}</p>
                <x-permit-tipe-badge :tipe="$permit->tipe" />
                <x-permit-site-badge :site="$permit->site" />
            </div>
        </div>
        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold {{ $badge }}">
            {{ str_starts_with($permit->status, 'Review') ? 'Menunggu ' . $permit->status : $permit->status }}
        </span>
    </div>

    @if($permit->status === 'Revision' && $permit->catatan_revisi)
    <div class="bg-red-50 border border-red-200 rounded-2xl px-4 md:px-6 py-4 mb-5 flex gap-3">
        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-red-700">Catatan Revisi</p>
            <p class="text-sm text-red-600 mt-1">{{ $permit->catatan_revisi }}</p>
        </div>
    </div>
    @endif

    @if($permit->status === 'Cancelled')
    <div class="bg-red-50 border border-red-200 rounded-2xl px-4 md:px-6 py-5 mb-5">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
            <h3 class="text-sm font-bold text-red-800">Izin Kerja Dibatalkan</h3>
        </div>
        <p class="text-sm text-red-700 mb-1"><strong>Alasan:</strong> {{ $permit->cancellation_reason }}</p>
        <p class="text-xs text-red-600 mb-4">Dibatalkan pada: {{ $permit->cancelled_at ? $permit->cancelled_at->format('d/m/Y H:i') : '—' }}</p>
        
        @if($permit->cancellation_signatures && count($permit->cancellation_signatures) > 0)
        <div class="mt-4 border-t border-red-200 pt-4">
            <p class="text-xs font-semibold text-red-800 mb-3">Ditandatangani oleh:</p>
            <div class="flex flex-wrap gap-4">
                @foreach($permit->cancellation_signatures as $sig)
                <div class="bg-white border border-red-200 rounded-xl p-3 w-48 text-center">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">{{ $sig['role'] }}</p>
                    <img src="{{ $sig['signature'] }}" alt="TTD" class="h-16 mx-auto mb-1">
                    <p class="text-xs font-semibold text-gray-800">{{ $sig['name'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Grid detail --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- A. Klasifikasi --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">A</span>
                <h3 class="text-sm font-semibold text-gray-800 mt-0.5">Klasifikasi Pekerjaan</h3>
            </div>
            <div class="px-4 md:px-6 py-4">
                @if($permit->klasifikasi_pekerjaan && count($permit->klasifikasi_pekerjaan))
                    <div class="flex flex-wrap gap-2">
                        @foreach($permit->klasifikasi_pekerjaan as $k)
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-lg capitalize">{{ str_replace('_', ' ', $k) }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">Tidak ada klasifikasi dipilih.</p>
                @endif
            </div>
        </div>

        {{-- B. Informasi Pekerjaan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">B</span>
                <h3 class="text-sm font-semibold text-gray-800 mt-0.5">Informasi Pekerjaan</h3>
            </div>
            <div class="px-4 md:px-6 py-4 space-y-3 text-sm">
                @php
                    $info = [
                        'Nama Pekerjaan'    => $permit->nama_pekerjaan,
                        'Lokasi'            => $permit->lokasi,
                        'Kontraktor'        => $permit->kontraktor,
                        'Penanggung Jawab'  => $permit->penanggung_jawab,
                        'No. Telepon'       => $permit->telepon,
                        'Tanggal Mulai'     => $permit->tanggal_mulai?->format('d/m/Y'),
                        'Tanggal Selesai'   => $permit->tanggal_selesai?->format('d/m/Y'),
                    ];
                @endphp
                @foreach($info as $label => $value)
                <div class="flex gap-3">
                    <span class="text-xs text-gray-400 w-28 sm:w-36 shrink-0 pt-0.5">{{ $label }}</span>
                    <span class="text-gray-700 font-medium">{{ $value ?: '—' }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- C. Bahaya Pekerjaan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">C</span>
                <h3 class="text-sm font-semibold text-gray-800 mt-0.5">Bahaya Pekerjaan</h3>
            </div>
            <div class="px-4 md:px-6 py-4">
                @if($permit->bahaya_pekerjaan && count($permit->bahaya_pekerjaan))
                    <div class="flex flex-wrap gap-2">
                        @foreach($permit->bahaya_pekerjaan as $b)
                            <span class="px-2.5 py-1 bg-orange-50 text-orange-700 text-xs font-semibold rounded-lg capitalize">{{ str_replace('_', ' ', $b) }}</span>
                        @endforeach
                    </div>
                    @if($permit->bahaya_lainnya)
                        <p class="text-xs text-gray-500 mt-2">Lainnya: {{ $permit->bahaya_lainnya }}</p>
                    @endif
                @else
                    <p class="text-sm text-gray-400 italic">Tidak ada bahaya dipilih.</p>
                @endif
            </div>
        </div>

        {{-- D. Tindakan Pencegahan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">D</span>
                <h3 class="text-sm font-semibold text-gray-800 mt-0.5">Tindakan Pencegahan</h3>
            </div>
            <div class="px-4 md:px-6 py-4">
                @if($permit->tindakan_pencegahan && count($permit->tindakan_pencegahan))
                    <div class="flex flex-wrap gap-2">
                        @foreach($permit->tindakan_pencegahan as $t)
                            <span class="px-2.5 py-1 bg-green-50 text-green-700 text-xs font-semibold rounded-lg capitalize">{{ str_replace('_', ' ', $t) }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">Tidak ada tindakan pencegahan dipilih.</p>
                @endif
            </div>
        </div>

        {{-- E. APD --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm md:col-span-2">
            <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">E</span>
                <h3 class="text-sm font-semibold text-gray-800 mt-0.5">Alat Pelindung Diri (APD)</h3>
            </div>
            <div class="px-4 md:px-6 py-4">
                @if($permit->apd && count($permit->apd))
                    <div class="flex flex-wrap gap-2">
                        @foreach($permit->apd as $a)
                            <span class="px-2.5 py-1 bg-purple-50 text-purple-700 text-xs font-semibold rounded-lg capitalize">{{ str_replace('_', ' ', $a) }}</span>
                        @endforeach
                    </div>
                    @if($permit->apd_lainnya)
                        <p class="text-xs text-gray-500 mt-2">Lainnya: {{ $permit->apd_lainnya }}</p>
                    @endif
                @else
                    <p class="text-sm text-gray-400 italic">Tidak ada APD dipilih.</p>
                @endif
            </div>
        </div>

    </div>

    {{-- F. Dokumen Pendukung --}}
    @if($permit->documents && count($permit->documents) > 0)
    <div class="mt-5 bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="px-4 md:px-6 py-4 border-b border-gray-100">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">F</span>
            <h3 class="text-sm font-semibold text-gray-800 mt-0.5">Dokumen Pendukung</h3>
        </div>
        <div class="px-4 md:px-6 py-4">
            <div class="space-y-3">
                @foreach($permit->documents as $doc)
                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $doc->nama_dokumen }}</p>
                        @if($doc->deskripsi)
                            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $doc->deskripsi }}</p>
                        @endif
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ strtoupper(pathinfo($doc->file_path, PATHINFO_EXTENSION)) }} &middot; {{ number_format($doc->file_size / 1024, 1) }} KB</p>
                    </div>
                    <a href="{{ route('permits.documents.download', [$permit->id, $doc->id]) }}"
                       class="shrink-0 p-2 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($permit->approval_signatures && count($permit->approval_signatures) > 0)
    <div class="mt-5 bg-white rounded-2xl border border-gray-100 shadow-sm px-4 md:px-6 py-5">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Riwayat Persetujuan (Approval)</h3>
        <div class="flex flex-wrap gap-4">
            @foreach($permit->approval_signatures as $sig)
            <div class="bg-gray-50 border border-gray-100 rounded-xl p-3 w-48 text-center">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">{{ $sig['role'] }}</p>
                <img src="{{ $sig['signature'] }}" alt="TTD" class="h-16 mx-auto mb-1">
                <p class="text-xs font-semibold text-gray-800">{{ $sig['name'] }}</p>
                <p class="text-[10px] text-gray-400 mt-1">{{ \Carbon\Carbon::parse($sig['date'])->format('d/m/Y H:i') }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Timeline status --}}
    <div class="mt-5 bg-white rounded-2xl border border-gray-100 shadow-sm px-4 md:px-6 py-5">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Informasi Waktu</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-400">Dibuat</p>
                <p class="font-semibold text-gray-700 mt-1">{{ $permit->created_at->format('d/m/Y, H:i') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Diajukan</p>
                <p class="font-semibold text-gray-700 mt-1">{{ $permit->submitted_at ? $permit->submitted_at->format('d/m/Y, H:i') : '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Ditutup</p>
                <p class="font-semibold text-gray-700 mt-1">{{ $permit->closed_at ? $permit->closed_at->format('d/m/Y, H:i') : '—' }}</p>
            </div>
        </div>
    </div>

</x-app-layout>
