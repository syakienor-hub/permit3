<x-app-layout>
    <x-slot name="title">Detail HSE Plan</x-slot>

    @php
        $statusBadge = match($hsePlan->status) {
            \App\Models\HsePlan::STATUS_APPROVED => 'bg-green-100 text-green-700',
            \App\Models\HsePlan::STATUS_REJECTED => 'bg-red-100 text-red-700',
            default => 'bg-orange-100 text-orange-700',
        };
        $statusLabel = match($hsePlan->status) {
            \App\Models\HsePlan::STATUS_APPROVED => 'Disetujui',
            \App\Models\HsePlan::STATUS_REJECTED => 'Ditolak',
            default => 'Menunggu Persetujuan Manager HSE',
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
    @endphp

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

    <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <a href="/admin/hse-plans" class="text-sm font-semibold text-gray-500 hover:text-inka-navy">&larr; Kembali ke Daftar HSE Plan</a>
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-800 mt-1 font-mono">{{ $hsePlan->no_hse_plan }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $hsePlan->nama_kontraktor }} &middot; {{ $hsePlan->site }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 self-start">
            <a href="/admin/hse-plans/{{ $hsePlan->id }}/edit"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-inka-navy rounded-xl hover:opacity-90 transition-opacity">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit HSE Plan
            </a>
            <a href="/admin/hse-plans/{{ $hsePlan->id }}/pdf" target="_blank"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:border-gray-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Lihat PDF
            </a>
            <a href="/admin/hse-plans/{{ $hsePlan->id }}/pdf/download"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Unduh PDF
            </a>
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold {{ $statusBadge }}">{{ $statusLabel }}</span>
        </div>
    </div>

    @if($hsePlan->status === \App\Models\HsePlan::STATUS_REJECTED && $hsePlan->rejection_reason)
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
            <p class="text-sm font-bold text-red-800">Alasan Penolakan</p>
            <p class="text-sm text-red-700 mt-1">{{ $hsePlan->rejection_reason }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">Informasi Proyek</h2>
                </div>
                <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><p class="text-gray-400">No. PO</p><p class="font-semibold text-gray-700">{{ $hsePlan->no_po ?: '-' }}</p></div>
                    <div><p class="text-gray-400">Tipe Pekerjaan</p><p class="font-semibold text-gray-700">{{ $hsePlan->tipe }}</p></div>
                    <div><p class="text-gray-400">Divisi / Unit Pengaju</p><p class="font-semibold text-gray-700">{{ $hsePlan->divisi_pengaju }}</p></div>
                    <div><p class="text-gray-400">Pimpinan Proyek</p><p class="font-semibold text-gray-700">{{ $hsePlan->nama_pimpinan_proyek }}</p></div>
                    <div><p class="text-gray-400">Telepon</p><p class="font-semibold text-gray-700">{{ $hsePlan->telepon }}</p></div>
                    <div class="sm:col-span-2">
                        <p class="text-gray-400">Lama Pekerjaan</p>
                        <p class="font-semibold text-gray-700">
                            Tanggal: {{ $hsePlan->tanggal_mulai?->format('d/m/Y') ?? '-' }} s/d {{ $hsePlan->tanggal_selesai?->format('d/m/Y') ?? '-' }}
                        </p>
                        <p class="font-semibold text-gray-700">
                            Jam: {{ $hsePlan->jam_mulai ?: '-' }} s/d {{ $hsePlan->jam_selesai ?: '-' }}
                        </p>
                    </div>
                    <div class="sm:col-span-2"><p class="text-gray-400">Deskripsi Pekerjaan</p><p class="font-semibold text-gray-700 whitespace-pre-line">{{ $hsePlan->deskripsi_pekerjaan }}</p></div>
                    <div><p class="text-gray-400">Diajukan</p><p class="font-semibold text-gray-700">{{ ($hsePlan->submitted_at ?? $hsePlan->created_at)?->format('d/m/Y H:i') }}</p></div>
                </div>
            </section>

            @if($hsePlan->contractor)
                @php $contractor = $hsePlan->contractor; @endphp
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                        <h2 class="text-base font-semibold text-gray-800">Data Kontraktor</h2>
                    </div>
                    <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div><p class="text-gray-400">Vendor / Perusahaan</p><p class="font-semibold text-gray-700">{{ $contractor->vendor }}</p></div>
                        <div><p class="text-gray-400">PIC</p><p class="font-semibold text-gray-700">{{ $contractor->pic }}</p></div>
                        <div><p class="text-gray-400">Jenis Pekerjaan</p><p class="font-semibold text-gray-700">{{ \App\Models\Classification::where('code', $contractor->jenis_pekerjaan)->value('name') ?? $contractor->jenis_pekerjaan }}</p></div>
                        <div><p class="text-gray-400">No. Telepon</p><p class="font-semibold text-gray-700">{{ $contractor->telepon }}</p></div>
                        <div><p class="text-gray-400">Jumlah Pekerja</p><p class="font-semibold text-gray-700">{{ $contractor->jumlah_pekerja }}</p></div>
                        <div><p class="text-gray-400">Lokasi</p><p class="font-semibold text-gray-700">{{ $contractor->lokasi }}</p></div>
                        <div><p class="text-gray-400">Tanggal Pekerjaan</p><p class="font-semibold text-gray-700">{{ $contractor->tanggal_mulai?->format('d/m/Y') }} s/d {{ $contractor->tanggal_selesai?->format('d/m/Y') }}</p></div>
                        <div><p class="text-gray-400">Site</p><p class="font-semibold text-gray-700">{{ $contractor->site }}</p></div>
                    </div>
                </section>
            @endif

            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">Personil &amp; Tugas</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table min-w-[500px]">
                        <thead>
                            <tr><th>No</th><th>Nama Personil</th><th>Tugas</th></tr>
                        </thead>
                        <tbody>
                            @forelse(($hsePlan->personil ?? []) as $i => $person)
                                <tr>
                                    <td class="text-gray-500">{{ $i + 1 }}</td>
                                    <td class="font-medium text-gray-700">{{ $person['nama'] ?? '-' }}</td>
                                    <td class="text-gray-600">{{ $person['tugas'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-gray-400 py-6">Tidak ada data personil.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">APD &amp; Peralatan</h2>
                </div>
                <div class="px-4 md:px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-2">Jenis APD</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse(($hsePlan->apd ?? []) as $apd)
                                <span class="inline-flex px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold">{{ $apdLabels[$apd] ?? $apd }}</span>
                            @empty
                                <span class="text-sm text-gray-400">-</span>
                            @endforelse
                            @if($hsePlan->apd_lainnya)
                                <span class="inline-flex px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600 text-xs font-semibold">{{ $hsePlan->apd_lainnya }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-2">Peralatan Kerja</p>
                        <ul class="space-y-1.5">
                            @forelse(($hsePlan->peralatan ?? []) as $alat)
                                <li class="flex items-center justify-between gap-3 text-sm">
                                    <span class="text-gray-700">{{ $alat['nama'] ?? '-' }}</span>
                                    <span class="font-semibold text-gray-500">{{ $alat['jumlah'] ?? '-' }}</span>
                                </li>
                            @empty
                                <li class="text-sm text-gray-400">-</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </section>

            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">Dokumen Pendukung</h2>
                </div>
                <div class="px-4 md:px-6 py-5 space-y-2">
                    @forelse($hsePlan->documents as $document)
                        <a href="/admin/hse-plans/{{ $hsePlan->id }}/documents/{{ $document->id }}/view" target="_blank" rel="noopener"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-gray-100 hover:border-inka-navy/30 hover:bg-blue-50/30 transition-colors">
                            <svg class="w-5 h-5 text-inka-navy shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-700 truncate">{{ $document->nama_dokumen }}</p>
                                <p class="text-xs text-gray-400">{{ $docLabels[$document->jenis] ?? $document->jenis }} &middot; {{ number_format($document->file_size / 1024, 0) }} KB</p>
                            </div>
                            <span class="text-xs font-semibold text-inka-navy shrink-0">Lihat</span>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400">Tidak ada dokumen.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="space-y-5">
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">Pernyataan K3LH</h2>
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
                            <span class="text-gray-600">{{ $label }}</span>
                            <span class="font-semibold {{ $hsePlan->{$field} ? 'text-green-600' : 'text-gray-400' }}">{{ $hsePlan->{$field} ? 'Ya' : 'Tidak' }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">Tanda Tangan Kontraktor</h2>
                </div>
                <div class="px-4 md:px-6 py-4">
                    <img src="{{ $hsePlan->tanda_tangan }}" alt="Tanda tangan kontraktor" class="w-full max-w-[260px] mx-auto border border-gray-100 rounded-xl bg-white">
                </div>
            </section>

            @if($hsePlan->status === \App\Models\HsePlan::STATUS_REVIEW)
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                        <h2 class="text-base font-semibold text-gray-800">Keputusan Manager HSE</h2>
                        <p class="text-sm text-gray-400 mt-0.5">Setujui dengan tanda tangan, atau tolak dengan alasan.</p>
                    </div>
                    <div class="px-4 md:px-6 py-4 space-y-5">
                        <form method="POST" action="/admin/hse-plans/{{ $hsePlan->id }}/approve" id="approve-form">
                            @csrf
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <p class="text-sm font-semibold text-gray-600">Tanda tangan persetujuan</p>
                                <button type="button" onclick="clearSignature()" class="text-xs font-semibold text-gray-400 hover:text-red-500">Hapus</button>
                            </div>
                            <div id="signature-container" class="relative border-2 border-dashed border-gray-300 rounded-xl overflow-hidden bg-white cursor-crosshair w-full aspect-[2/1]">
                                <canvas id="signature-canvas" class="block w-full h-full" style="touch-action: none;"></canvas>
                                <span id="signature-placeholder" class="absolute inset-0 flex items-center justify-center text-sm text-gray-300 pointer-events-none select-none">Tanda tangan di sini</span>
                            </div>
                            <input type="hidden" name="tanda_tangan" id="tanda-tangan-input">
                            <p id="signature-error" class="text-sm text-red-500 mt-1.5 hidden">Tanda tangan wajib diisi.</p>
                            <button type="submit" class="mt-3 w-full px-4 py-2.5 rounded-xl bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition-colors">Setujui HSE Plan</button>
                        </form>

                        <form method="POST" action="/admin/hse-plans/{{ $hsePlan->id }}/reject" class="border-t border-gray-100 pt-4">
                            @csrf
                            <label class="block text-sm font-semibold text-gray-600 mb-1.5">Alasan penolakan</label>
                            <textarea name="rejection_reason" rows="3" required class="form-input !text-sm" placeholder="Tuliskan alasan agar kontraktor dapat memperbaiki...">{{ old('rejection_reason') }}</textarea>
                            <button type="submit" class="mt-2.5 w-full px-4 py-2.5 rounded-xl border border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 transition-colors">Tolak HSE Plan</button>
                        </form>
                    </div>
                </section>
            @else
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                        <h2 class="text-base font-semibold text-gray-800">Riwayat Keputusan</h2>
                    </div>
                    <div class="px-4 md:px-6 py-4 text-sm space-y-2">
                        <p class="text-gray-400">Diproses oleh</p>
                        <p class="font-semibold text-gray-700">{{ optional($hsePlan->reviewer)->name ?? 'Manager HSE' }}</p>
                        <p class="text-gray-400">Pada</p>
                        <p class="font-semibold text-gray-700">{{ $hsePlan->reviewed_at?->format('d/m/Y H:i') ?? '-' }}</p>
                        @if($hsePlan->manager_signature)
                            <p class="text-gray-400 pt-2">Tanda tangan Manager HSE</p>
                            <img src="{{ $hsePlan->manager_signature }}" alt="Tanda tangan Manager HSE" class="w-full max-w-[260px] border border-gray-100 rounded-xl bg-white">
                        @endif
                    </div>
                </section>
            @endif
        </div>
    </div>

    @if($hsePlan->status === \App\Models\HsePlan::STATUS_REVIEW)
        <script>
            (function () {
                const canvas = document.getElementById('signature-canvas');
                const placeholder = document.getElementById('signature-placeholder');
                const hiddenInput = document.getElementById('tanda-tangan-input');
                const container = document.getElementById('signature-container');
                const form = document.getElementById('approve-form');
                const ctx = canvas.getContext('2d');
                let drawing = false, hasDrawn = false;

                function resizeCanvas() {
                    const rect = container.getBoundingClientRect();
                    if (rect.width === 0) return;
                    canvas.width = rect.width;
                    canvas.height = rect.height;
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, rect.width, rect.height);
                    ctx.strokeStyle = '#111d33';
                    ctx.lineWidth = 2;
                    ctx.lineCap = 'round';
                    ctx.lineJoin = 'round';
                }

                window.addEventListener('resize', resizeCanvas);
                resizeCanvas();

                function getPos(e) {
                    const rect = canvas.getBoundingClientRect();
                    if (e.touches) return { x: e.touches[0].clientX - rect.left, y: e.touches[0].clientY - rect.top };
                    return { x: e.clientX - rect.left, y: e.clientY - rect.top };
                }

                function startDraw(e) { e.preventDefault(); drawing = true; const pos = getPos(e); ctx.beginPath(); ctx.moveTo(pos.x, pos.y); }
                function draw(e) {
                    if (!drawing) return;
                    e.preventDefault();
                    const pos = getPos(e);
                    ctx.lineTo(pos.x, pos.y);
                    ctx.stroke();
                    if (!hasDrawn) {
                        hasDrawn = true;
                        placeholder.classList.add('hidden');
                        document.getElementById('signature-error').classList.add('hidden');
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
                canvas.addEventListener('touchstart', startDraw, { passive: false });
                canvas.addEventListener('touchmove', draw, { passive: false });
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
                };

                form.addEventListener('submit', function (e) {
                    if (!hasDrawn) {
                        e.preventDefault();
                        document.getElementById('signature-error').classList.remove('hidden');
                    }
                });
            })();
        </script>
    @endif
</x-app-layout>
