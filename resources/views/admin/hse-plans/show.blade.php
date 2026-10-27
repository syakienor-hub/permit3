
<x-app-layout>
    <x-slot name="title">Detail HSE Plan</x-slot>

    @php
        $statusBadge = match ($hsePlan->status) {
            \App\Models\HsePlan::STATUS_APPROVED => 'bg-green-100 text-green-700',
            \App\Models\HsePlan::STATUS_REVISION => 'bg-amber-100 text-amber-700',
            \App\Models\HsePlan::STATUS_REJECTED => 'bg-red-100 text-red-700',
            default => 'bg-orange-100 text-orange-700',
        };

        $statusLabel = match ($hsePlan->status) {
            \App\Models\HsePlan::STATUS_APPROVED => 'Disetujui',
            \App\Models\HsePlan::STATUS_REVISION => 'Perlu Revisi',
            \App\Models\HsePlan::STATUS_REJECTED => 'Ditolak',
            default => 'Menunggu Review Manager',
        };

        $apdLabels = [
            'helm' => 'Helm Keselamatan',
            'sepatu' => 'Sepatu Keselamatan',
            'pelindung_muka' => 'Pelindung Muka',
            'kacamata' => 'Kacamata Keselamatan',
            'rompi' => 'Rompi Keselamatan',
            'masker' => 'Masker / Respirator',
            'sarung_tangan' => 'Sarung Tangan',
            'ear_plug' => 'Ear Plug / Ear Muff',
            'baju_pelindung' => 'Baju Pelindung',
            'safety_harness' => 'Safety Harness',
        ];

        $docLabels = [
            'jsa' => 'Job Safety Analysis (JSA)',
            'sertifikat_personil' => 'Sertifikat Keahlian Personil',
            'sertifikat_peralatan' => 'Sertifikat Peralatan',
            'msds' => 'MSDS Bahan B3',
            'limbah_b3' => 'Rencana Pengelolaan Limbah B3',
            'prosedur_kerja' => 'Prosedur / Instruksi Kerja',
            'inspeksi' => 'Rencana Pemantauan / Inspeksi',
            'struktur_organisasi' => 'Struktur Organisasi Proyek',
            'hse_plan_existing' => 'Dokumen HSE Plan',
            'hse_plan_generated' => 'HSE Plan Disetujui',
            'dokumen_lainnya' => 'Dokumen Pendukung Lainnya',
        ];

        $isRevision = $hsePlan->status === \App\Models\HsePlan::STATUS_REVISION;
        $isRejected = $hsePlan->status === \App\Models\HsePlan::STATUS_REJECTED;

        $showDecisionNote = $isRevision || $isRejected;
    @endphp

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-5 py-3 text-sm font-medium text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-medium text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-5 py-3">
            <p class="mb-2 text-sm font-semibold text-red-700">
                Ada yang perlu diperbaiki:
            </p>

            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="mb-6 flex flex-col xl:flex-row xl:items-end justify-between gap-4">
        <div class="min-w-0">
            <a href="/admin/hse-plans"
               class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-inka-navy">
                &larr; Kembali ke Review HSE
            </a>

            <h1 class="mt-3 text-xl sm:text-2xl font-bold text-gray-900 font-mono">
                {{ $hsePlan->no_hse_plan }}
            </h1>

            <div class="mt-2 flex flex-wrap items-center gap-2">
                <span class="text-sm text-gray-500">
                    {{ $hsePlan->nama_kontraktor }} &middot; {{ $hsePlan->site }}
                </span>

                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusBadge }}">
                    {{ $statusLabel }}
                </span>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="/admin/hse-plans/{{ $hsePlan->id }}/edit"
               class="inline-flex items-center rounded-lg bg-inka-navy px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-white hover:opacity-90">
                Edit HSE Plan
            </a>

            <a href="/admin/hse-plans/{{ $hsePlan->id }}/pdf"
               target="_blank"
               rel="noopener"
               class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Lihat PDF
            </a>

            <a href="/admin/hse-plans/{{ $hsePlan->id }}/pdf/download"
               class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Unduh PDF
            </a>
        </div>
    </div>

    {{-- GRID UTAMA --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ================================= --}}
        {{-- KOLOM KIRI --}}
        {{-- ================================= --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- INFORMASI PROYEK --}}
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">
                        Informasi Proyek
                    </h2>
                </div>

                <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400">No. PO</p>
                        <p class="font-semibold text-gray-700">
                            {{ $hsePlan->no_po ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-400">Tipe Pekerjaan</p>
                        <p class="font-semibold text-gray-700">
                            {{ $hsePlan->tipe }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-400">Divisi / Unit Pengaju</p>
                        <p class="font-semibold text-gray-700">
                            {{ $hsePlan->divisi_pengaju }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-400">Pimpinan Proyek</p>
                        <p class="font-semibold text-gray-700">
                            {{ $hsePlan->nama_pimpinan_proyek }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-400">Telepon</p>
                        <p class="font-semibold text-gray-700">
                            {{ $hsePlan->telepon }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-gray-400">Lama Pekerjaan</p>

                        <p class="font-semibold text-gray-700">
                            Tanggal:
                            {{ $hsePlan->tanggal_mulai?->format('d/m/Y') ?? '-' }}
                            s/d
                            {{ $hsePlan->tanggal_selesai?->format('d/m/Y') ?? '-' }}
                        </p>

                        <p class="font-semibold text-gray-700">
                            Jam:
                            {{ $hsePlan->jam_mulai ?: '-' }}
                            s/d
                            {{ $hsePlan->jam_selesai ?: '-' }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-gray-400">Deskripsi Pekerjaan</p>

                        <p class="font-semibold text-gray-700 whitespace-pre-line">
                            {{ $hsePlan->deskripsi_pekerjaan }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-400">Diajukan</p>
                        <p class="font-semibold text-gray-700">
                            {{ ($hsePlan->submitted_at ?? $hsePlan->created_at)?->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- DATA KONTRAKTOR --}}
            @if($hsePlan->contractor)
                @php
                    $contractor = $hsePlan->contractor;

                    $approvalStatus = $contractor->approval_status ?? 'pending';

                    $approvalLabels = [
                        'pending' => 'On Hold',
                        'approved' => 'Approved',
                        'on_hold' => 'On Hold',
                        'inactive' => 'Nonaktif',
                    ];

                    $approvalColors = [
                        'pending' => 'bg-amber-100 text-amber-700',
                        'approved' => 'bg-green-100 text-green-700',
                        'on_hold' => 'bg-orange-100 text-orange-700',
                        'inactive' => 'bg-gray-100 text-gray-600',
                    ];
                @endphp

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h2 class="text-base font-semibold text-gray-800">
                                Data Kontraktor
                            </h2>

                            <span class="inline-flex items-center rounded-full px-3 py-1.5 text-xs font-semibold {{ $approvalColors[$approvalStatus] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $approvalLabels[$approvalStatus] ?? $approvalStatus }}
                            </span>
                        </div>
                    </div>

                    <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-400">Vendor / Perusahaan</p>
                            <p class="font-semibold text-gray-700">
                                {{ $contractor->vendor }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-400">PIC</p>
                            <p class="font-semibold text-gray-700">
                                {{ $contractor->pic }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-400">Jenis Pekerjaan</p>
                            <p class="font-semibold text-gray-700">
                                @if($contractor->jenis_pekerjaan === 'umum')
                                    Pekerjaan Umum
                                @elseif($contractor->jenis_pekerjaan === 'risiko_tinggi')
                                    Pekerjaan Risiko Tinggi
                                @else
                                    {{ \App\Models\Classification::where('code', $contractor->jenis_pekerjaan)->value('name') ?? $contractor->jenis_pekerjaan }}
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-400">No. Telepon</p>
                            <p class="font-semibold text-gray-700">
                                {{ $contractor->telepon }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-400">Jumlah Pekerja</p>
                            <p class="font-semibold text-gray-700">
                                {{ $contractor->jumlah_pekerja }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-400">Lokasi</p>
                            <p class="font-semibold text-gray-700">
                                {{ $contractor->lokasi }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-400">Tanggal Pekerjaan</p>
                            <p class="font-semibold text-gray-700">
                                {{ $contractor->tanggal_mulai?->format('d/m/Y') ?? '-' }}
                                s/d
                                {{ $contractor->tanggal_selesai?->format('d/m/Y') ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-400">Site</p>
                            <p class="font-semibold text-gray-700">
                                {{ $contractor->site }}
                            </p>
                        </div>
                    </div>
                </section>
            @endif

            {{-- PERSONIL DAN TUGAS --}}
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">
                        Personil &amp; Tugas
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table min-w-[500px]">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Personil</th>
                                <th>Tugas</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse(($hsePlan->personil ?? []) as $i => $person)
                                <tr>
                                    <td class="text-gray-500">
                                        {{ $i + 1 }}
                                    </td>

                                    <td class="font-medium text-gray-700">
                                        {{ $person['nama'] ?? '-' }}
                                    </td>

                                    <td class="text-gray-600">
                                        {{ $person['tugas'] ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3"
                                        class="text-center text-gray-400 py-6">
                                        Tidak ada data personil.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- APD DAN PERALATAN --}}
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">
                        APD &amp; Peralatan
                    </h2>
                </div>

                <div class="px-4 md:px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-2">
                            Jenis APD
                        </p>

                        <div class="flex flex-wrap gap-2">
                            @forelse(($hsePlan->apd ?? []) as $apd)
                                <span class="inline-flex px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold">
                                    {{ $apdLabels[$apd] ?? $apd }}
                                </span>
                            @empty
                                <span class="text-sm text-gray-400">-</span>
                            @endforelse

                            @if($hsePlan->apd_lainnya)
                                <span class="inline-flex px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600 text-xs font-semibold">
                                    {{ $hsePlan->apd_lainnya }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-2">
                            Peralatan Kerja
                        </p>

                        <ul class="space-y-1.5">
                            @forelse(($hsePlan->peralatan ?? []) as $alat)
                                <li class="flex items-center justify-between gap-3 text-sm">
                                    <span class="text-gray-700">
                                        {{ $alat['nama'] ?? '-' }}
                                    </span>

                                    <span class="font-semibold text-gray-500">
                                        {{ $alat['jumlah'] ?? '-' }}
                                    </span>
                                </li>
                            @empty
                                <li class="text-sm text-gray-400">-</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </section>

            {{-- DOKUMEN PENDUKUNG --}}
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">
                        Dokumen Pendukung
                    </h2>
                </div>

                <div class="px-4 md:px-6 py-5 space-y-2">
                    @forelse($hsePlan->documents as $document)
                        <a href="/admin/hse-plans/{{ $hsePlan->id }}/documents/{{ $document->id }}/view"
                           target="_blank"
                           rel="noopener"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-gray-100 hover:border-inka-navy/30 hover:bg-blue-50/30 transition-colors">

                            <svg class="w-5 h-5 text-inka-navy shrink-0"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-700 truncate">
                                    {{ $document->nama_dokumen }}
                                </p>

                                <p class="text-xs text-gray-400">
                                    {{ $docLabels[$document->jenis] ?? $document->jenis }}
                                    &middot;
                                    {{ number_format(($document->file_size ?? 0) / 1024, 0) }} KB
                                </p>
                            </div>

                            <span class="text-xs font-semibold text-inka-navy shrink-0">
                                Lihat
                            </span>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400">
                            Tidak ada dokumen.
                        </p>
                    @endforelse
                </div>
            </section>

        </div>

        {{-- ================================= --}}
        {{-- KOLOM KANAN --}}
        {{-- ================================= --}}
        <div class="space-y-5">

            {{-- PERNYATAAN K3LH --}}
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">
                        Pernyataan K3LH
                    </h2>
                </div>

                <div class="px-4 md:px-6 py-4 space-y-2.5 text-sm">
                    @foreach([
                        'bahan_b3' => 'Menggunakan bahan B3',
                        'limbah_b3' => 'Menghasilkan limbah B3',
                        'prosedur_kerja' => 'Memiliki prosedur / instruksi kerja',
                        'rencana_inspeksi' => 'Memiliki rencana inspeksi',
                        'tanggap_darurat' => 'Menyediakan fasilitas tanggap darurat',
                        'struktur_organisasi' => 'Memiliki struktur organisasi proyek',
                    ] as $field => $label)
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-600">
                                {{ $label }}
                            </span>

                            <span class="font-semibold {{ $hsePlan->{$field} ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $hsePlan->{$field} ? 'Ya' : 'Tidak' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- TANDA TANGAN KONTRAKTOR --}}
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">
                        Tanda Tangan Kontraktor
                    </h2>
                </div>

                <div class="px-4 md:px-6 py-4">
                    @if(filled($hsePlan->tanda_tangan))
                        <img
                            src="{{ $hsePlan->tanda_tangan }}"
                            alt="Tanda tangan kontraktor"
                            class="w-full max-w-[260px] mx-auto border border-gray-100 rounded-xl bg-white"
                        >
                    @else
                        <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-200 bg-gray-50 px-4 py-8 text-center">
                            <svg class="w-8 h-8 text-gray-300 mb-2"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M3 17c2.5-3 4-6 4-6s1.5 4 3.5 4S14 9 14 9s1.5 4 3.5 4S21 9 21 9M4 21h16"/>
                            </svg>

                            <p class="text-sm font-medium text-gray-500">
                                Tanda tangan kontraktor tidak tersedia.
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                Data HSE Plan ini tidak menyertakan tanda tangan.
                            </p>
                        </div>
                    @endif
                </div>
            </section>

            {{-- KEPUTUSAN MANAGER HSE --}}
            @if(
                $hsePlan->status === \App\Models\HsePlan::STATUS_REVIEW
                && auth()->user()->role === 'manager'
            )
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-base font-semibold text-gray-800">
                            Keputusan Manager HSE
                        </h2>

                        <p class="text-xs text-gray-500 mt-1">
                            Periksa kelengkapan HSE Plan sebelum memberikan persetujuan atau catatan revisi.
                        </p>
                    </div>

                    <div class="p-5 space-y-6">

                        {{-- FORM APPROVE --}}
                        <form
                            method="POST"
                            action="/admin/hse-plans/{{ $hsePlan->id }}/approve"
                            id="approve-form"
                        >
                            @csrf

                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-800">
                                        Persetujuan HSE Plan
                                    </h3>

                                    <p class="text-xs text-gray-400 mt-1">
                                        Tanda tangan Manager HSE
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    onclick="clearSignature()"
                                    class="text-xs font-medium text-gray-500 hover:text-red-600"
                                >
                                    Hapus
                                </button>
                            </div>

                            <div
                                id="signature-container"
                                class="relative w-full aspect-[2/1] border-2 border-dashed border-gray-200 rounded-xl overflow-hidden bg-white cursor-crosshair"
                            >
                                <canvas
                                    id="signature-canvas"
                                    class="block w-full h-full"
                                    style="touch-action: none;"
                                ></canvas>

                                <span
                                    id="signature-placeholder"
                                    class="absolute inset-0 flex items-center justify-center text-xs text-gray-300 pointer-events-none select-none"
                                >
                                    Tanda tangan di sini
                                </span>
                            </div>

                            <input
                                type="hidden"
                                name="tanda_tangan"
                                id="tanda-tangan-input"
                            >

                            <p
                                id="signature-error"
                                class="text-xs text-red-500 mt-2 hidden"
                            >
                                Tanda tangan wajib diisi sebelum menyetujui.
                            </p>

                            <button
                                type="submit"
                                class="mt-4 w-full rounded-lg bg-inka-navy px-4 py-3 text-sm font-semibold text-white hover:opacity-90"
                            >
                                Setujui HSE Plan
                            </button>
                        </form>

                        {{-- PEMISAH --}}
                        <div class="flex items-center gap-3">
                            <div class="h-px bg-gray-100 flex-1"></div>

                            <span class="text-xs font-medium text-gray-400">
                                ATAU
                            </span>

                            <div class="h-px bg-gray-100 flex-1"></div>
                        </div>

                        {{-- FORM CATATAN REVISI --}}
                        <form
                            method="POST"
                            action="/admin/hse-plans/{{ $hsePlan->id }}/revision"
                        >
                            @csrf

                            <h3 class="text-sm font-semibold text-gray-800">
                                Catatan Revisi
                            </h3>

                            <p class="text-xs text-gray-500 mt-1 mb-3 leading-relaxed">
                                Berikan catatan mengenai data atau dokumen yang perlu diperbaiki sebelum mendapatkan persetujuan.
                            </p>

                            <textarea
                                name="rejection_reason"
                                rows="4"
                                maxlength="1000"
                                required
                                class="form-input w-full !text-sm"
                                placeholder="Tuliskan data atau dokumen yang perlu diperbaiki..."
                            >{{ old('rejection_reason') }}</textarea>

                            <button
                                type="submit"
                                class="mt-3 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:border-gray-400 hover:bg-gray-50"
                            >
                                Kirim Catatan Revisi
                            </button>
                        </form>

                    </div>
                </section>

            @elseif($hsePlan->status !== \App\Models\HsePlan::STATUS_REVIEW)

                {{-- RIWAYAT KEPUTUSAN --}}
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-base font-semibold text-gray-800">
                            Riwayat Keputusan
                        </h2>
                    </div>

                    <div class="px-5 py-4 text-sm space-y-3">
                        <div>
                            <p class="text-xs text-gray-400">
                                Diproses oleh
                            </p>

                            <p class="font-semibold text-gray-700 mt-1">
                                {{ optional($hsePlan->reviewer)->name ?? 'Manager HSE' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400">
                                Tanggal keputusan
                            </p>

                            <p class="font-semibold text-gray-700 mt-1">
                                {{ $hsePlan->reviewed_at?->format('d/m/Y H:i') ?? '-' }}
                            </p>
                        </div>

                        @if($hsePlan->manager_signature)
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-xs text-gray-400 mb-3">
                                    Tanda tangan Manager HSE
                                </p>

                                <img
                                    src="{{ $hsePlan->manager_signature }}"
                                    alt="Tanda tangan Manager HSE"
                                    class="w-full max-w-[260px] rounded-xl border border-gray-100 bg-white"
                                >
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            {{-- =========================================== --}}
            {{-- CATATAN REVISI / PENOLAKAN MANAGER HSE --}}
            {{-- TEPAT DI BAWAH RIWAYAT KEPUTUSAN --}}
            {{-- =========================================== --}}
            @if($showDecisionNote)
                <section class="rounded-2xl border {{ $isRevision ? 'border-amber-200 bg-amber-50' : 'border-red-200 bg-red-50' }} shadow-sm overflow-hidden">

                    <div class="px-5 py-4 border-b {{ $isRevision ? 'border-amber-200' : 'border-red-200' }}">
                        <h2 class="text-sm font-bold {{ $isRevision ? 'text-amber-900' : 'text-red-800' }}">
                            {{ $isRevision ? 'Catatan Revisi Manager HSE' : 'Alasan Penolakan Manager HSE' }}
                        </h2>
                    </div>

                    <div class="px-5 py-4">
                        <p class="text-sm leading-relaxed whitespace-pre-line break-words {{ $isRevision ? 'text-amber-800' : 'text-red-700' }}">
                            {{ $hsePlan->rejection_reason ?: 'Tidak ada catatan yang tercatat.' }}
                        </p>

                        @if($hsePlan->reviewed_at)
                            <p class="mt-3 text-xs {{ $isRevision ? 'text-amber-700' : 'text-red-600' }}">
                                Tanggal keputusan:
                                {{ $hsePlan->reviewed_at->format('d/m/Y H:i') }}
                            </p>
                        @endif

                        @if($hsePlan->reviewer)
                            <p class="mt-1 text-xs {{ $isRevision ? 'text-amber-700' : 'text-red-600' }}">
                                Oleh: {{ $hsePlan->reviewer->name }}
                            </p>
                        @endif
                    </div>

                </section>
            @endif

        </div>
        {{-- AKHIR KOLOM KANAN --}}

    </div>
    {{-- AKHIR GRID UTAMA --}}

    {{-- JAVASCRIPT TANDA TANGAN MANAGER --}}
    @if(
        $hsePlan->status === \App\Models\HsePlan::STATUS_REVIEW
        && auth()->user()->role === 'manager'
    )
        <script>
            (function () {
                const canvas = document.getElementById('signature-canvas');
                const placeholder = document.getElementById('signature-placeholder');
                const hiddenInput = document.getElementById('tanda-tangan-input');
                const container = document.getElementById('signature-container');
                const form = document.getElementById('approve-form');
                const error = document.getElementById('signature-error');

                if (!canvas || !container || !form) return;

                const ctx = canvas.getContext('2d');

                let drawing = false;
                let hasDrawn = false;

                function resizeCanvas() {
                    const rect = container.getBoundingClientRect();

                    if (rect.width === 0 || rect.height === 0) return;

                    canvas.width = rect.width;
                    canvas.height = rect.height;

                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, rect.width, rect.height);

                    ctx.strokeStyle = '#111d33';
                    ctx.lineWidth = 2;
                    ctx.lineCap = 'round';
                    ctx.lineJoin = 'round';

                    hasDrawn = false;
                    hiddenInput.value = '';
                    placeholder.classList.remove('hidden');
                }

                window.addEventListener('resize', resizeCanvas);
                resizeCanvas();

                function getPos(e) {
                    const rect = canvas.getBoundingClientRect();

                    if (e.touches) {
                        return {
                            x: e.touches[0].clientX - rect.left,
                            y: e.touches[0].clientY - rect.top
                        };
                    }

                    return {
                        x: e.clientX - rect.left,
                        y: e.clientY - rect.top
                    };
                }

                function startDraw(e) {
                    e.preventDefault();
                    drawing = true;

                    const pos = getPos(e);

                    ctx.beginPath();
                    ctx.moveTo(pos.x, pos.y);
                }

                function draw(e) {
                    if (!drawing) return;

                    e.preventDefault();

                    const pos = getPos(e);

                    ctx.lineTo(pos.x, pos.y);
                    ctx.stroke();

                    if (!hasDrawn) {
                        hasDrawn = true;
                        placeholder.classList.add('hidden');
                        error.classList.add('hidden');
                    }
                }

                function stopDraw() {
                    if (!drawing) return;

                    drawing = false;
                    ctx.beginPath();

                    hiddenInput.value = canvas.toDataURL('image/png');
                }

                canvas.addEventListener('mousedown', startDraw);
                canvas.addEventListener('mousemove', draw);
                canvas.addEventListener('mouseup', stopDraw);
                canvas.addEventListener('mouseleave', stopDraw);

                canvas.addEventListener('touchstart', startDraw, {
                    passive: false
                });

                canvas.addEventListener('touchmove', draw, {
                    passive: false
                });

                canvas.addEventListener('touchend', stopDraw);

                window.clearSignature = function () {
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);

                    ctx.strokeStyle = '#111d33';
                    ctx.lineWidth = 2;
                    ctx.lineCap = 'round';
                    ctx.lineJoin = 'round';

                    hasDrawn = false;
                    hiddenInput.value = '';

                    placeholder.classList.remove('hidden');
                    error.classList.add('hidden');
                };

                form.addEventListener('submit', function (e) {
                    if (!hasDrawn || !hiddenInput.value) {
                        e.preventDefault();
                        error.classList.remove('hidden');
                    }
                });
            })();
        </script>
    @endif

</x-app-layout>
