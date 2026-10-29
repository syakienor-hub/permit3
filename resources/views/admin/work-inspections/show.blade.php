<x-app-layout>
    <x-slot name="title">Detail Daftar Periksa</x-slot>

    @php
        $items = \App\Models\WorkInspection::ITEMS;
        $options = \App\Models\WorkInspection::ANSWER_OPTIONS;
        $badge = match($inspection->status) {
            \App\Models\WorkInspection::STATUS_APPROVED => 'bg-green-100 text-green-700',
            \App\Models\WorkInspection::STATUS_REJECTED => 'bg-red-100 text-red-700',
            default => 'bg-orange-100 text-orange-700',
        };
        $label = \App\Models\WorkInspection::statusLabel($inspection->status);
        $isStaff = auth()->user()->role === 'staff';
        $isManager = auth()->user()->role === 'manager';
    @endphp

    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-2xl border border-green-200 bg-green-50 px-5 py-3 text-base font-medium text-green-800">
            <svg class="h-5 w-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-medium text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="mx-auto max-w-5xl">
        <div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
            <div>
                <a href="/admin/work-inspections" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#5b6b8c] hover:text-[#122040]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali ke Daftar Periksa
                </a>
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <h1 class="text-xl font-extrabold tracking-[-.01em] text-[#141a2b] sm:text-2xl">{{ $inspection->no_periksa }}</h1>
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $badge }}">{{ $label }}</span>
                </div>
                <p class="mt-1 text-sm text-[#7a849d]">Form K3LH IV - Rev.0 — Daftar Periksa Izin Pekerjaan Berisiko Tinggi</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 self-start">
                <a href="/admin/work-inspections/{{ $inspection->id }}/pdf" target="_blank"
                   class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#e4e7ef] bg-white px-4 py-2 text-sm font-semibold text-[#4b5470] transition-colors hover:border-[#c9d1e2]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Lihat PDF
                </a>
                <a href="/admin/work-inspections/{{ $inspection->id }}/pdf/download"
                   class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 transition-colors hover:bg-red-100">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Unduh PDF
                </a>
            </div>
        </div>

        @if($inspection->status === \App\Models\WorkInspection::STATUS_REJECTED && $inspection->rejection_reason)
            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
                <p class="text-sm font-semibold text-red-700">Alasan ditolak</p>
                <p class="mt-1 text-sm text-red-700">{{ $inspection->rejection_reason }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <div class="space-y-5 lg:col-span-2">
                <section class="wi-card">
                    <div class="wi-card-head">
                        <div>
                            <p class="wi-eyebrow">Identitas Dokumen</p>
                            <h2 class="wi-title">Data Pekerjaan</h2>
                        </div>
                        <span class="wi-step">01</span>
                    </div>
                    <div class="wi-card-body">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div><p class="wi-label">No. Dokumen</p><p class="wi-readonly">FORM K3LH IV - REV.0</p></div>
                            <div><p class="wi-label">Tgl. Terbit</p><p class="wi-readonly">{{ ($inspection->tanggal_terbit ?? $inspection->created_at)?->translatedFormat('d F Y') }}</p></div>
                            <div><p class="wi-label">Hal</p><p class="wi-readonly">1 dari 1</p></div>
                            <div><p class="wi-label">Otorisasi</p><p class="wi-readonly">{{ $inspection->otorisasi ?: '—' }}</p></div>
                            <div class="sm:col-span-2"><p class="wi-label">Pekerjaan</p><p class="wi-readonly !min-h-0">{{ $inspection->nama_pekerjaan }}</p></div>
                            <div><p class="wi-label">Peralatan</p><p class="wi-readonly !min-h-0">{{ $inspection->jenis_peralatan ?: '—' }}</p></div>
                            <div><p class="wi-label">Material</p><p class="wi-readonly !min-h-0">{{ $inspection->material ?: '—' }}</p></div>
                            <div><p class="wi-label">Lokasi</p><p class="wi-readonly !min-h-0">{{ $inspection->lokasi }}</p></div>
                            <div><p class="wi-label">Mulai tanggal</p><p class="wi-readonly !min-h-0">{{ $inspection->tanggal_mulai?->format('d/m/Y') ?? '—' }}</p></div>
                            <div class="sm:col-span-2"><p class="wi-label">Petugas pemeriksa</p><p class="wi-readonly !min-h-0">{{ $inspection->petugas_pemeriksa ?: '—' }}</p></div>
                        </div>
                    </div>
                </section>

                <section class="wi-card">
                    <div class="wi-card-head">
                        <div>
                            <p class="wi-eyebrow">Pemeriksaan Lapangan</p>
                            <h2 class="wi-title">Item Pengecekan</h2>
                        </div>
                        <span class="wi-step">02</span>
                    </div>
                    <div class="!p-0">
                        <div class="hidden border-b border-[#eef1f7] bg-[#f8fafc] px-5 py-3 text-[11px] font-bold uppercase tracking-[.06em] text-[#7a849d] sm:grid sm:grid-cols-[52px_1fr_88px] sm:gap-4">
                            <span>No.</span>
                            <span>Item Pengecekan</span>
                            <span class="text-center">Hasil</span>
                        </div>
                        <div class="divide-y divide-[#eef1f7]">
                            @foreach($items as $key => $item)
                                @php
                                    $value = $inspection->answer($key);
                                    $chip = match($value) {
                                        'ok' => 'bg-[#e8f7f1] text-[#0f6b5d] ring-[#1f9d8a]/30',
                                        'nok' => 'bg-[#fdecea] text-[#b03a2e] ring-[#dc5a4b]/30',
                                        'na' => 'bg-[#eef1f7] text-[#4b5470] ring-[#9aa6c1]/30',
                                        default => 'bg-gray-100 text-gray-500 ring-gray-200',
                                    };
                                @endphp
                                <div class="px-5 py-4 sm:grid sm:grid-cols-[52px_1fr_88px] sm:items-center sm:gap-4">
                                    <span class="mb-2 inline-flex h-6 min-w-6 items-center justify-center rounded-lg bg-[#f2f5fa] px-1.5 text-[11px] font-bold text-[#5b6b8c] sm:mb-0">{{ $loop->iteration }}</span>
                                    <p class="mb-2 text-[13.5px] leading-relaxed text-[#2a3247] sm:mb-0">{{ $item }}</p>
                                    <div class="sm:text-center">
                                        <span class="inline-flex min-w-[54px] justify-center rounded-lg px-2.5 py-1 text-[11.5px] font-bold ring-1 {{ $chip }}">
                                            {{ $value ? $options[$value] : '—' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section class="wi-card">
                    <div class="wi-card-head">
                        <div>
                            <p class="wi-eyebrow">Pengesahan</p>
                            <h2 class="wi-title">Tanda Tangan</h2>
                        </div>
                        <span class="wi-step">03</span>
                    </div>
                    <div class="wi-card-body grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <p class="wi-label">Mengetahui — Plt. Manager HSE PT INKA</p>
                            <p class="mb-2 text-sm font-bold text-[#141a2b]">{{ $inspection->manager_nama ?: '—' }}</p>
                            @if($inspection->tanda_tangan)
                                <img src="{{ $inspection->tanda_tangan }}" alt="Tanda tangan manager" class="w-full rounded-2xl border border-[#e4e7ef] bg-white">
                            @else
                                <p class="text-sm text-[#9aa6c1]">Menunggu tanda tangan Manager HSE.</p>
                            @endif
                        </div>
                        <div>
                            <p class="wi-label">Staff HSE PT INKA</p>
                            <p class="mb-2 text-sm font-bold text-[#141a2b]">{{ $inspection->safety_officer_nama ?: '—' }}</p>
                            @if($inspection->staff_signature)
                                <img src="{{ $inspection->staff_signature }}" alt="Tanda tangan staff" class="w-full rounded-2xl border border-[#e4e7ef] bg-white">
                            @else
                                <p class="text-sm text-[#9aa6c1]">Belum ada tanda tangan.</p>
                            @endif
                        </div>
                    </div>
                </section>
            </div>

            <div class="space-y-5">
                <div class="wi-card">
                    <div class="wi-card-body text-sm">
                        <h2 class="mb-3 text-base font-extrabold tracking-[-.01em] text-[#141a2b]">Ringkasan</h2>
                        <div class="space-y-3">
                            <div><p class="text-[#9aa6c1]">Status</p><p class="font-semibold text-[#2a3247]">{{ $label }}</p></div>
                            <div><p class="text-[#9aa6c1]">Site</p><p class="font-semibold text-[#2a3247]">{{ $inspection->site }}</p></div>
                            <div><p class="text-[#9aa6c1]">Diajukan</p><p class="font-semibold text-[#2a3247]">{{ ($inspection->submitted_at ?? $inspection->created_at)?->format('d/m/Y H:i') }}</p></div>
                            <div><p class="text-[#9aa6c1]">Diperiksa oleh</p><p class="font-semibold text-[#2a3247]">{{ $inspection->reviewer->name ?? '—' }}</p></div>
                            <div><p class="text-[#9aa6c1]">Waktu verifikasi</p><p class="font-semibold text-[#2a3247]">{{ $inspection->reviewed_at?->format('d/m/Y H:i') ?? '—' }}</p></div>
                        </div>
                    </div>
                </div>

                @if($inspection->status === \App\Models\WorkInspection::STATUS_PENDING)
                    @if($isStaff)
                        <div class="wi-card">
                            <div class="wi-card-body">
                                <h2 class="mb-1 text-base font-extrabold tracking-[-.01em] text-[#141a2b]">Verifikasi Staff HSE</h2>
                                <p class="mb-4 text-xs text-[#7a849d]">Isi nama dan tanda tangan untuk menyetujui daftar periksa lama ini.</p>

                                <form method="POST" action="/admin/work-inspections/{{ $inspection->id }}/approve" id="approve-form" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="wi-label">Nama Safety Officer</label>
                                        <input type="text" name="safety_officer_nama" value="{{ old('safety_officer_nama', auth()->user()->name) }}" class="wi-input" required>
                                    </div>
                                    <div>
                                        <div class="mb-1.5 flex items-center justify-between">
                                            <label class="wi-label !mb-0">Tanda Tangan</label>
                                            <button type="button" onclick="clearSignature()" class="text-xs font-semibold text-[#9aa6c1] hover:text-red-500">Hapus</button>
                                        </div>
                                        <div id="signature-container" class="relative aspect-[2/1] w-full cursor-crosshair overflow-hidden rounded-xl border-2 border-dashed border-[#c9d1e2] bg-white">
                                            <canvas id="signature-canvas" class="block h-full w-full" style="touch-action: none;"></canvas>
                                            <span id="signature-placeholder" class="pointer-events-none absolute inset-0 flex select-none items-center justify-center text-sm text-gray-300">Tanda tangan di sini</span>
                                        </div>
                                        <input type="hidden" name="tanda_tangan" id="tanda-tangan-input">
                                        <p id="signature-error" class="mt-1.5 hidden text-sm text-red-500">Tanda tangan wajib diisi.</p>
                                    </div>
                                    <button type="submit" class="w-full rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-green-700">
                                        Setujui Daftar Periksa
                                    </button>
                                </form>

                                <form method="POST" action="/admin/work-inspections/{{ $inspection->id }}/reject" class="mt-4 space-y-2 border-t border-[#eef1f7] pt-4">
                                    @csrf
                                    <label class="wi-label">Tolak dengan alasan</label>
                                    <textarea name="rejection_reason" rows="3" required class="wi-input" placeholder="Tuliskan alasan agar pengaju dapat memperbaiki...">{{ old('rejection_reason') }}</textarea>
                                    <button type="submit" class="w-full rounded-xl border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-600 transition-colors hover:bg-red-50">
                                        Tolak Daftar Periksa
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
                            Menunggu verifikasi Staff HSE. Hanya Staff HSE yang dapat menyetujui daftar periksa ini.
                        </div>
                    @endif
                @endif

                @if($inspection->status === \App\Models\WorkInspection::STATUS_REVIEW_MANAGER)
                    @if($isManager)
                        <div class="wi-card">
                            <div class="wi-card-body">
                                <h2 class="mb-1 text-base font-extrabold tracking-[-.01em] text-[#141a2b]">Persetujuan Manager HSE</h2>
                                <p class="mb-4 text-xs text-[#7a849d]">Staff HSE sudah menandatangani. Isi nama dan tanda tangan untuk menyetujui daftar periksa ini.</p>

                                <form method="POST" action="/admin/work-inspections/{{ $inspection->id }}/approve" id="approve-form" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="wi-label">Nama Manager HSE</label>
                                        <input type="text" name="manager_nama" value="{{ old('manager_nama', auth()->user()->name) }}" class="wi-input" required>
                                    </div>
                                    <div>
                                        <div class="mb-1.5 flex items-center justify-between">
                                            <label class="wi-label !mb-0">Tanda Tangan Manager HSE</label>
                                            <button type="button" onclick="clearSignature()" class="text-xs font-semibold text-[#9aa6c1] hover:text-red-500">Hapus</button>
                                        </div>
                                        <div id="signature-container" class="relative aspect-[2/1] w-full cursor-crosshair overflow-hidden rounded-xl border-2 border-dashed border-[#c9d1e2] bg-white">
                                            <canvas id="signature-canvas" class="block h-full w-full" style="touch-action: none;"></canvas>
                                            <span id="signature-placeholder" class="pointer-events-none absolute inset-0 flex select-none items-center justify-center text-sm text-gray-300">Tanda tangan manager di sini</span>
                                        </div>
                                        <input type="hidden" name="tanda_tangan" id="tanda-tangan-input">
                                        <p id="signature-error" class="mt-1.5 hidden text-sm text-red-500">Tanda tangan manager wajib diisi.</p>
                                    </div>
                                    <button type="submit" class="w-full rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-green-700">
                                        Setujui Daftar Periksa
                                    </button>
                                </form>

                                <form method="POST" action="/admin/work-inspections/{{ $inspection->id }}/reject" class="mt-4 space-y-2 border-t border-[#eef1f7] pt-4">
                                    @csrf
                                    <label class="wi-label">Tolak dengan alasan</label>
                                    <textarea name="rejection_reason" rows="3" required class="wi-input" placeholder="Tuliskan alasan agar Staff HSE dapat memperbaiki...">{{ old('rejection_reason') }}</textarea>
                                    <button type="submit" class="w-full rounded-xl border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-600 transition-colors hover:bg-red-50">
                                        Tolak Daftar Periksa
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
                            Menunggu persetujuan Manager HSE. Daftar periksa sudah ditandatangani Staff HSE.
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <style>
        .wi-card {
            overflow: hidden;
            border: 1px solid #e9edf5;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 18px 50px -34px rgba(13,21,38,.35);
        }

        .wi-card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid #eef1f7;
            padding: 22px 24px 18px;
        }

        .wi-card-body { padding: 24px; }

        .wi-eyebrow {
            margin-bottom: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #d9861a;
        }

        .wi-title {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -.01em;
            color: #141a2b;
        }

        .wi-step {
            display: inline-flex;
            height: 34px;
            min-width: 34px;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #f2f5fa;
            font-size: 12px;
            font-weight: 800;
            color: #5b6b8c;
        }

        .wi-label {
            display: block;
            margin-bottom: 7px;
            font-size: 12.5px;
            font-weight: 600;
            color: #4b5470;
        }

        .wi-readonly {
            display: flex;
            min-height: 44px;
            align-items: center;
            border: 1px solid #eef1f7;
            border-radius: 13px;
            background: #f8fafc;
            padding: 10px 14px;
            font-size: 14px;
            line-height: 1.5;
            color: #2a3247;
            overflow-wrap: anywhere;
        }

        .wi-input {
            display: block;
            width: 100%;
            min-height: 44px;
            border: 1px solid #e4e7ef;
            border-radius: 13px;
            background: #fbfcfe;
            padding: 10px 14px;
            font-size: 14px;
            line-height: 1.5;
            color: #141a2b;
            transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .wi-input:focus {
            outline: none;
            border-color: #122040;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(18,32,64,.08);
        }

        @media (max-width: 639px) {
            .wi-card-head { padding: 18px 18px 16px; }
            .wi-card-body { padding: 18px; }
        }
    </style>

    @if(($inspection->status === \App\Models\WorkInspection::STATUS_PENDING && $isStaff) || ($inspection->status === \App\Models\WorkInspection::STATUS_REVIEW_MANAGER && $isManager))
        <script>
            (function () {
                const canvas = document.getElementById('signature-canvas');
                const placeholder = document.getElementById('signature-placeholder');
                const container = document.getElementById('signature-container');
                const hiddenInput = document.getElementById('tanda-tangan-input');
                const form = document.getElementById('approve-form');
                const ctx = canvas.getContext('2d');
                let drawing = false;
                let hasDrawn = false;

                function resizeCanvas() {
                    const rect = container.getBoundingClientRect();
                    canvas.width = rect.width;
                    canvas.height = rect.height;
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.strokeStyle = '#122040';
                    ctx.lineWidth = 2;
                    ctx.lineCap = 'round';
                    ctx.lineJoin = 'round';
                }

                function position(e) {
                    const rect = canvas.getBoundingClientRect();
                    const point = e.touches ? e.touches[0] : e;
                    return { x: point.clientX - rect.left, y: point.clientY - rect.top };
                }

                function startDraw(e) {
                    e.preventDefault();
                    drawing = true;
                    hasDrawn = true;
                    placeholder.classList.add('hidden');
                    const p = position(e);
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                }

                function draw(e) {
                    if (!drawing) return;
                    e.preventDefault();
                    const p = position(e);
                    ctx.lineTo(p.x, p.y);
                    ctx.stroke();
                }

                function stopDraw() {
                    if (!drawing) return;
                    drawing = false;
                    ctx.beginPath();
                    hiddenInput.value = canvas.toDataURL('image/png');
                }

                window.addEventListener('resize', resizeCanvas);
                resizeCanvas();

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
                    ctx.strokeStyle = '#122040';
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
