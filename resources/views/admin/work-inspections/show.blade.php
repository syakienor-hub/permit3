<x-app-layout>
    <x-slot name="title">Detail Daftar Periksa</x-slot>

    @php
        $answerKeys = array_keys(\App\Models\WorkInspection::ANSWER_LABELS);
        $bagianA = array_slice($answerKeys, 0, 10);
        $bagianB = array_slice($answerKeys, 10);
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
        $isStaff = auth()->user()->role === 'staff';
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

    <div class="mb-5 flex flex-col sm:flex-row sm:items-start justify-between gap-3">
        <div>
            <a href="/admin/work-inspections" class="text-sm text-gray-500 hover:text-inka-navy">&larr; Kembali ke Daftar Periksa</a>
            <div class="flex flex-wrap items-center gap-3 mt-2">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 font-mono">{{ $inspection->no_periksa }}</h1>
                <span class="text-xs font-bold px-3 py-1 rounded-full {{ $badge }}">{{ $label }}</span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Form K3LH IV-01.013 — Daftar Periksa Izin Pekerjaan Berisiko Tinggi</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 self-start">
            <a href="/admin/work-inspections/{{ $inspection->id }}/pdf" target="_blank"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:border-gray-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Lihat PDF
            </a>
            <a href="/admin/work-inspections/{{ $inspection->id }}/pdf/download"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Unduh PDF
            </a>
        </div>
    </div>

    @if($inspection->status === \App\Models\WorkInspection::STATUS_REJECTED && $inspection->rejection_reason)
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
            <p class="text-sm font-semibold text-red-700">Alasan ditolak</p>
            <p class="text-sm text-red-700 mt-1">{{ $inspection->rejection_reason }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h2 class="text-base font-semibold text-gray-800 mb-4">Data Pekerjaan</h2>
                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div><p class="text-gray-400">Pekerjaan</p><p class="font-semibold text-gray-700">{{ $inspection->nama_pekerjaan }}</p></div>
                    <div><p class="text-gray-400">Kontraktor / Pelaksana</p><p class="font-semibold text-gray-700">{{ $inspection->kontraktor }}</p></div>
                    <div><p class="text-gray-400">Jenis Peralatan / Material</p><p class="font-semibold text-gray-700">{{ $inspection->jenis_peralatan ?: '—' }}</p></div>
                    <div><p class="text-gray-400">Lokasi</p><p class="font-semibold text-gray-700">{{ $inspection->lokasi }}</p></div>
                    <div><p class="text-gray-400">Site</p><p class="font-semibold text-gray-700">{{ $inspection->site }}</p></div>
                    <div><p class="text-gray-400">Tipe</p><p class="font-semibold text-gray-700">{{ $inspection->tipe }}</p></div>
                    <div><p class="text-gray-400">Divisi Pengaju</p><p class="font-semibold text-gray-700">{{ $inspection->divisi_pengaju }}</p></div>
                    <div><p class="text-gray-400">No. Telepon</p><p class="font-semibold text-gray-700">{{ $inspection->telepon }}</p></div>
                    <div><p class="text-gray-400">Mulai Tanggal</p><p class="font-semibold text-gray-700">{{ $inspection->tanggal_mulai?->format('d/m/Y') ?? '—' }}</p></div>
                    <div><p class="text-gray-400">Diajukan</p><p class="font-semibold text-gray-700">{{ ($inspection->submitted_at ?? $inspection->created_at)?->format('d/m/Y H:i') }}</p></div>
                    <div><p class="text-gray-400">Safety Permit Terkait</p><p class="font-semibold text-gray-700">{{ $inspection->permit->no_permit ?? 'Belum ada' }}</p></div>
                    <div><p class="text-gray-400">Diperiksa Oleh</p><p class="font-semibold text-gray-700">{{ $inspection->reviewer->name ?? '—' }}</p></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h2 class="text-base font-semibold text-gray-800 mb-1">Pemeriksaan Sumber Energi &amp; Area Kerja</h2>
                <p class="text-xs text-gray-400 mb-3">Bagian 1</p>
                <div class="space-y-2">
                    @foreach($bagianA as $key)
                        <div class="flex items-start justify-between gap-3 border-b border-gray-50 pb-2">
                            <span class="text-sm text-gray-600">{{ \App\Models\WorkInspection::ANSWER_LABELS[$key] }}</span>
                            <span class="shrink-0 text-xs font-bold px-2.5 py-1 rounded-full {{ $inspection->answer($key) ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                                {{ $inspection->answer($key) ? 'YA' : 'TIDAK' }}
                            </span>
                        </div>
                    @endforeach
                    @if(!empty($inspection->answers['izin_lain_ket']))
                        <p class="text-sm text-gray-500">Izin lain: <span class="font-semibold text-gray-700">{{ $inspection->answers['izin_lain_ket'] }}</span></p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h2 class="text-base font-semibold text-gray-800 mb-1">Pemeriksaan Lanjutan</h2>
                <p class="text-xs text-gray-400 mb-3">Bagian 2</p>
                <div class="space-y-2">
                    @foreach($bagianB as $key)
                        <div class="flex items-start justify-between gap-3 border-b border-gray-50 pb-2">
                            <span class="text-sm text-gray-600">{{ \App\Models\WorkInspection::ANSWER_LABELS[$key] }}</span>
                            <span class="shrink-0 text-xs font-bold px-2.5 py-1 rounded-full {{ $inspection->answer($key) ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                                {{ $inspection->answer($key) ? 'YA' : 'TIDAK' }}
                            </span>
                        </div>
                    @endforeach
                    @if(!empty($inspection->answers['apd_khusus_ket']))
                        <p class="text-sm text-gray-500">APD khusus: <span class="font-semibold text-gray-700">{{ $inspection->answers['apd_khusus_ket'] }}</span></p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h2 class="text-base font-semibold text-gray-800 mb-4">LOTO &amp; Tanda Tangan</h2>
                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div><p class="text-gray-400">Pelaksana LOTO</p><p class="font-semibold text-gray-700">{{ $inspection->loto_nama ?: '—' }}</p></div>
                    <div><p class="text-gray-400">Tanggal / Jam</p><p class="font-semibold text-gray-700">{{ $inspection->loto_tanggal?->format('d/m/Y') ?? '—' }} {{ $inspection->loto_jam ? '/ ' . $inspection->loto_jam : '' }}</p></div>
                    <div><p class="text-gray-400">Supervisor</p><p class="font-semibold text-gray-700">{{ $inspection->supervisor_nama ?: '—' }}</p></div>
                    <div><p class="text-gray-400">Tanggal / Jam Supervisor</p><p class="font-semibold text-gray-700">{{ $inspection->supervisor_tanggal?->format('d/m/Y') ?? '—' }} {{ $inspection->supervisor_jam ? '/ ' . $inspection->supervisor_jam : '' }}</p></div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Tanda tangan pelaksana</p>
                        <img src="{{ $inspection->tanda_tangan }}" alt="Tanda tangan pelaksana" class="w-full max-w-[280px] border border-gray-100 rounded-xl bg-white">
                    </div>
                    @if($inspection->staff_signature)
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Tanda tangan Safety Officer ({{ $inspection->safety_officer_nama }})</p>
                            <img src="{{ $inspection->staff_signature }}" alt="Tanda tangan Safety Officer" class="w-full max-w-[280px] border border-gray-100 rounded-xl bg-white">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-5">
            @if($inspection->status === \App\Models\WorkInspection::STATUS_PENDING)
                @if($isStaff)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <h2 class="text-base font-semibold text-gray-800 mb-1">Verifikasi Staff HSE</h2>
                        <p class="text-xs text-gray-400 mb-4">Isi nama Safety Officer dan tanda tangan untuk menyetujui.</p>

                        <form method="POST" action="/admin/work-inspections/{{ $inspection->id }}/approve" id="approve-form" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Nama Safety Officer</label>
                                <input type="text" name="safety_officer_nama" value="{{ old('safety_officer_nama', auth()->user()->name) }}" class="form-input !text-sm" required>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-xs font-semibold text-gray-500">Tanda Tangan</label>
                                    <button type="button" onclick="clearSignature()" class="text-xs font-semibold text-gray-400 hover:text-red-500">Hapus</button>
                                </div>
                                <div id="signature-container" class="relative border-2 border-dashed border-gray-300 rounded-xl overflow-hidden bg-white cursor-crosshair w-full aspect-[2/1]">
                                    <canvas id="signature-canvas" class="block w-full h-full" style="touch-action: none;"></canvas>
                                    <span id="signature-placeholder" class="absolute inset-0 flex items-center justify-center text-sm text-gray-300 pointer-events-none select-none">Tanda tangan di sini</span>
                                </div>
                                <input type="hidden" name="tanda_tangan" id="tanda-tangan-input">
                                <p id="signature-error" class="text-sm text-red-500 mt-1.5 hidden">Tanda tangan wajib diisi.</p>
                            </div>
                            <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition-colors">
                                Setujui Daftar Periksa
                            </button>
                        </form>

                        <form method="POST" action="/admin/work-inspections/{{ $inspection->id }}/reject" class="border-t border-gray-100 pt-4 mt-4 space-y-2">
                            @csrf
                            <label class="block text-xs font-semibold text-gray-500">Tolak dengan alasan</label>
                            <textarea name="rejection_reason" rows="3" required class="form-input !text-sm" placeholder="Tuliskan alasan agar pengaju dapat memperbaiki...">{{ old('rejection_reason') }}</textarea>
                            <button type="submit" class="w-full px-4 py-2.5 rounded-xl border border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 transition-colors">
                                Tolak Daftar Periksa
                            </button>
                        </form>
                    </div>
                @else
                    <div class="bg-amber-50 rounded-2xl border border-amber-200 p-5 text-sm text-amber-800">
                        Menunggu verifikasi Staff HSE. Hanya Staff HSE yang dapat menyetujui daftar periksa ini.
                    </div>
                @endif
            @else
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-sm">
                    <h2 class="text-base font-semibold text-gray-800 mb-3">Hasil Verifikasi</h2>
                    <div class="space-y-2">
                        <p class="text-gray-400">Status</p>
                        <p class="font-semibold text-gray-700">{{ $label }}</p>
                        <p class="text-gray-400 pt-2">Diproses oleh</p>
                        <p class="font-semibold text-gray-700">{{ $inspection->reviewer->name ?? '—' }}</p>
                        <p class="text-gray-400 pt-2">Waktu</p>
                        <p class="font-semibold text-gray-700">{{ $inspection->reviewed_at?->format('d/m/Y H:i') ?? '—' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($inspection->status === \App\Models\WorkInspection::STATUS_PENDING && $isStaff)
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
                    ctx.strokeStyle = '#111d33';
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
