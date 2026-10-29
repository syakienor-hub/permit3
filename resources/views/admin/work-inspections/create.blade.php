<x-app-layout>
    <x-slot name="title">Buat Daftar Periksa</x-slot>

    @php
        $items = \App\Models\WorkInspection::ITEMS;
        $options = \App\Models\WorkInspection::ANSWER_OPTIONS;
        $today = now()->toDateString();
    @endphp

    <div class="mx-auto max-w-5xl">
        <div class="mb-5 flex items-center justify-between gap-3">
            <a href="/admin/work-inspections" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#5b6b8c] hover:text-[#122040]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Daftar Periksa
            </a>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-[11px] font-bold uppercase tracking-[.06em] text-[#1f9d8a] ring-1 ring-[#e4e7ef]">
                <span class="h-1.5 w-1.5 rounded-full bg-[#1f9d8a]"></span>
                Site {{ $site }}
            </span>
        </div>

        <div class="wi-hero relative mb-6 overflow-hidden rounded-[28px] px-6 py-8 text-white sm:px-10 sm:py-11">
            <div class="relative z-10 max-w-3xl">
                <p class="mb-3 inline-flex items-center gap-2 text-[11.5px] font-bold uppercase tracking-[.08em] text-[#f5a623]">
                    <span class="h-px w-4 bg-[#f5a623]"></span>
                    Form K3LH IV - Rev.0
                </p>
                <h1 class="text-[30px] font-extrabold leading-[1.15] tracking-[-.02em] sm:text-[40px]">
                    Daftar Periksa Izin Pekerjaan Berisiko Tinggi
                </h1>
                <p class="mt-3 max-w-2xl text-[14.5px] leading-relaxed text-white/70">
                    Diisi oleh Staff QM&amp;SHE site {{ $site }} sebelum pekerjaan berisiko tinggi dimulai.
                    Pemeriksaan dan tanda tangan dilakukan pada satu perangkat yang sama.
                </p>
                <div class="mt-6 flex flex-wrap gap-2.5 text-[12px] font-semibold text-white/80">
                    <span class="rounded-full bg-white/10 px-3.5 py-1.5 ring-1 ring-white/15">PT INKA (Persero)</span>
                    <span class="rounded-full bg-white/10 px-3.5 py-1.5 ring-1 ring-white/15">QM&amp;SHE Banyuwangi</span>
                    <span class="rounded-full bg-white/10 px-3.5 py-1.5 ring-1 ring-white/15">28 item pemeriksaan</span>
                </div>
            </div>
        </div>

        <div class="mb-6 flex items-start gap-3 rounded-2xl border border-[#f0d090] bg-[#fdf1da] px-4 py-3.5 text-[13px] leading-relaxed text-[#8a5a12]">
            <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <p>Bila daftar periksa telah dilakukan dengan baik, pekerjaan boleh dilanjutkan sesuai ketentuan HSE. Item yang bernilai <span class="font-bold">NOK</span> wajib ditindaklanjuti sebelum pekerjaan dimulai.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                <p class="mb-1 font-bold">Periksa kembali isian berikut:</p>
                <ul class="list-disc space-y-0.5 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/admin/work-inspections" id="inspection-form" class="space-y-6">
            @csrf

            <section class="wi-card">
                <div class="wi-card-head">
                    <div>
                        <p class="wi-eyebrow">Identitas Dokumen</p>
                        <h2 class="wi-title">Data Pekerjaan</h2>
                    </div>
                    <span class="wi-step">01</span>
                </div>

                <div class="wi-card-body">
                    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="wi-label">No. Dokumen</label>
                            <p class="wi-readonly">FORM K3LH IV - REV.0</p>
                        </div>
                        <div>
                            <label class="wi-label">Tgl. Terbit</label>
                            <p class="wi-readonly">{{ \Illuminate\Support\Carbon::parse($today)->translatedFormat('d F Y') }}</p>
                        </div>
                        <div>
                            <label class="wi-label">Hal</label>
                            <p class="wi-readonly">1 dari 1</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="wi-label">Otorisasi</label>
                            <input type="text" name="otorisasi" value="{{ old('otorisasi') }}" class="wi-input" placeholder="Nomor / nama otorisasi">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="wi-label">Pekerjaan <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_pekerjaan" value="{{ old('nama_pekerjaan') }}" class="wi-input @error('nama_pekerjaan') is-error @enderror" placeholder="Contoh: Perbaikan pipa steam di Area Produksi" required>
                        </div>
                        <div>
                            <label class="wi-label">Peralatan</label>
                            <input type="text" name="jenis_peralatan" value="{{ old('jenis_peralatan') }}" class="wi-input" placeholder="Contoh: Mesin las, gerinda">
                        </div>
                        <div>
                            <label class="wi-label">Material</label>
                            <input type="text" name="material" value="{{ old('material') }}" class="wi-input" placeholder="Contoh: Pipa steam, elektroda">
                        </div>
                        <div>
                            <label class="wi-label">Lokasi <span class="text-red-500">*</span></label>
                            <input type="text" name="lokasi" value="{{ old('lokasi') }}" class="wi-input @error('lokasi') is-error @enderror" placeholder="Contoh: Area Produksi, Gedung B" required>
                        </div>
                        <div>
                            <label class="wi-label">Mulai tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $today) }}" class="wi-input @error('tanggal_mulai') is-error @enderror" required>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="wi-label">Petugas pemeriksa <span class="text-red-500">*</span></label>
                            <input type="text" name="petugas_pemeriksa" value="{{ old('petugas_pemeriksa', auth()->user()->name) }}" class="wi-input @error('petugas_pemeriksa') is-error @enderror" placeholder="Nama petugas yang melakukan pemeriksaan" required>
                        </div>
                    </div>
                </div>
            </section>

            <section class="wi-card">
                <div class="wi-card-head">
                    <div>
                        <p class="wi-eyebrow">Pemeriksaan Lapangan</p>
                        <h2 class="wi-title">Item Pengecekan</h2>
                        <p class="mt-1 text-[12.5px] text-[#7a849d]">Pilih OK, NOK, atau N/A sesuai kondisi sebenarnya di lapangan.</p>
                    </div>
                    <span class="wi-step">02</span>
                </div>

                <div class="wi-card-body !p-0 sm:!p-0">
                    <div class="hidden border-b border-[#eef1f7] bg-[#f8fafc] px-5 py-3 text-[11px] font-bold uppercase tracking-[.06em] text-[#7a849d] sm:grid sm:grid-cols-[52px_1fr_196px] sm:gap-4">
                        <span>No.</span>
                        <span>Item Pengecekan</span>
                        <span class="text-center">Hasil</span>
                    </div>

                    <div class="divide-y divide-[#eef1f7]">
                        @foreach($items as $key => $label)
                            @php $selected = old('answers.' . $key); @endphp
                            <div class="px-5 py-4 transition-colors hover:bg-[#fcfdff] sm:grid sm:grid-cols-[52px_1fr_196px] sm:items-center sm:gap-4 @error('answers.' . $key) bg-red-50/40 @enderror">
                                <span class="mb-2 inline-flex h-6 min-w-6 items-center justify-center rounded-lg bg-[#f2f5fa] px-1.5 text-[11px] font-bold text-[#5b6b8c] sm:mb-0">{{ $loop->iteration }}</span>
                                <p class="mb-3 text-[13.5px] leading-relaxed text-[#2a3247] sm:mb-0">{{ $label }}</p>
                                <div class="flex items-center gap-1.5">
                                    @foreach($options as $value => $optionLabel)
                                        <label class="wi-option">
                                            <input type="radio" name="answers[{{ $key }}]" value="{{ $value }}" @checked($selected === $value) required>
                                            <span class="opt-{{ $value }}">{{ $optionLabel }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex flex-wrap gap-x-6 gap-y-2 border-t border-[#eef1f7] bg-[#f8fafc] px-5 py-3.5 text-[11.5px] font-semibold text-[#7a849d]">
                        <span class="inline-flex items-center gap-2"><i class="h-2 w-2 rounded-full bg-[#1f9d8a]"></i>OK — sesuai standar</span>
                        <span class="inline-flex items-center gap-2"><i class="h-2 w-2 rounded-full bg-[#dc5a4b]"></i>NOK — perlu tindak lanjut</span>
                        <span class="inline-flex items-center gap-2"><i class="h-2 w-2 rounded-full bg-[#9aa6c1]"></i>N/A — tidak relevan</span>
                    </div>
                </div>
            </section>

            <section class="wi-card">
                <div class="wi-card-head">
                    <div>
                        <p class="wi-eyebrow">Pengesahan</p>
                        <h2 class="wi-title">Tanda Tangan</h2>
                        <p class="mt-1 text-[12.5px] text-[#7a849d]">Ditandatangani oleh Manager dan Staff QM&amp;SHE pada perangkat yang sama.</p>
                    </div>
                    <span class="wi-step">03</span>
                </div>

                <div class="wi-card-body grid grid-cols-1 gap-7 sm:grid-cols-2">
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <label class="wi-label !mb-0">Mengetahui — Plt. Manager QM&amp;SHE {{ $site }} <span class="text-red-500">*</span></label>
                            <button type="button" onclick="clearSignature('manager')" class="text-[11px] font-bold text-[#9aa6c1] hover:text-red-500">Hapus</button>
                        </div>
                        <input type="text" name="manager_nama" value="{{ old('manager_nama') }}" class="wi-input mb-3 @error('manager_nama') is-error @enderror" placeholder="Nama lengkap manager" required>
                        <div id="signature-container-manager" class="wi-signature">
                            <canvas id="signature-canvas-manager" class="block h-full w-full" style="touch-action: none;"></canvas>
                            <span id="signature-placeholder-manager" class="wi-signature-placeholder">Tanda tangan manager di sini</span>
                        </div>
                        <input type="hidden" name="tanda_tangan" id="tanda-tangan-input">
                        <p id="signature-error-manager" class="mt-1.5 hidden text-[12px] font-semibold text-red-500">Tanda tangan manager wajib diisi.</p>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <label class="wi-label !mb-0">Staff QM&amp;SHE {{ $site }} <span class="text-red-500">*</span></label>
                            <button type="button" onclick="clearSignature('staff')" class="text-[11px] font-bold text-[#9aa6c1] hover:text-red-500">Hapus</button>
                        </div>
                        <p class="wi-readonly mb-3">{{ auth()->user()->name }}</p>
                        <div id="signature-container-staff" class="wi-signature">
                            <canvas id="signature-canvas-staff" class="block h-full w-full" style="touch-action: none;"></canvas>
                            <span id="signature-placeholder-staff" class="wi-signature-placeholder">Tanda tangan staff di sini</span>
                        </div>
                        <input type="hidden" name="staff_signature" id="staff-signature-input">
                        <p id="signature-error-staff" class="mt-1.5 hidden text-[12px] font-semibold text-red-500">Tanda tangan staff wajib diisi.</p>
                    </div>
                </div>
            </section>

            <div class="sticky bottom-0 z-20 -mx-4 border-t border-[#e4e7ef] bg-white/95 px-4 py-3 backdrop-blur sm:static sm:mx-0 sm:rounded-2xl sm:border sm:px-5 sm:py-4 sm:shadow-[0_18px_50px_-30px_rgba(13,21,38,.25)]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-[12px] leading-relaxed text-[#7a849d]">Daftar periksa langsung tersimpan setelah kedua tanda tangan diisi.</p>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#122040] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#0d1526]">
                        Simpan Daftar Periksa
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <style>
        .wi-hero {
            background:
                radial-gradient(1100px 360px at 12% -10%, rgba(245,166,35,.18), transparent 55%),
                linear-gradient(135deg, #0d1526 0%, #122040 55%, #1a2c52 100%);
        }

        .wi-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.045) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: radial-gradient(ellipse 70% 70% at 50% 30%, #000 40%, transparent 90%);
            -webkit-mask-image: radial-gradient(ellipse 70% 70% at 50% 30%, #000 40%, transparent 90%);
            pointer-events: none;
        }

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

        .wi-input,
        .wi-readonly {
            display: block;
            width: 100%;
            min-height: 46px;
            border: 1px solid #e4e7ef;
            border-radius: 14px;
            background: #fbfcfe;
            padding: 11px 14px;
            font-size: 14px;
            line-height: 1.5;
            color: #141a2b;
            transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .wi-readonly {
            display: flex;
            align-items: center;
            background: #f6f8fc;
            color: #5b6b8c;
            font-weight: 600;
        }

        .wi-input::placeholder { color: #a7b1c9; }

        .wi-input:focus {
            outline: none;
            border-color: #122040;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(18,32,64,.08);
        }

        .wi-input.is-error {
            border-color: #fca5a5;
            background: #fff7f7;
        }

        .wi-input.is-error:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239,68,68,.14);
        }

        .wi-option {
            position: relative;
            flex: 1 1 0;
            min-width: 58px;
            cursor: pointer;
        }

        .wi-option input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .wi-option span {
            display: flex;
            height: 38px;
            align-items: center;
            justify-content: center;
            border: 1px solid #e4e7ef;
            border-radius: 10px;
            background: #fff;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: .02em;
            color: #7a849d;
            transition: all .15s ease;
        }

        .wi-option:hover span { border-color: #c9d1e2; }

        .wi-option input:focus-visible + span {
            box-shadow: 0 0 0 3px rgba(245,166,35,.22);
        }

        .wi-option input:checked + .opt-ok {
            border-color: #1f9d8a;
            background: #e8f7f1;
            color: #0f6b5d;
        }

        .wi-option input:checked + .opt-nok {
            border-color: #dc5a4b;
            background: #fdecea;
            color: #b03a2e;
        }

        .wi-option input:checked + .opt-na {
            border-color: #9aa6c1;
            background: #eef1f7;
            color: #4b5470;
        }

        .wi-signature {
            position: relative;
            width: 100%;
            aspect-ratio: 3 / 1;
            overflow: hidden;
            border: 1.5px dashed #c9d1e2;
            border-radius: 16px;
            background: #fbfcfe;
            cursor: crosshair;
            transition: border-color .15s ease, background .15s ease;
        }

        .wi-signature:hover {
            border-color: #9aa6c1;
            background: #fff;
        }

        .wi-signature-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            text-align: center;
            font-size: 12.5px;
            font-weight: 600;
            color: #b6bfd2;
            pointer-events: none;
            user-select: none;
        }

        @media (max-width: 639px) {
            .wi-card-head { padding: 18px 18px 16px; }
            .wi-card-body { padding: 18px; }
        }
    </style>

    <script>
        (function () {
            const forms = [
                { key: 'manager', input: 'tanda-tangan-input' },
                { key: 'staff', input: 'staff-signature-input' },
            ];

            const signatures = {};

            forms.forEach(function (config) {
                const canvas = document.getElementById('signature-canvas-' + config.key);
                const placeholder = document.getElementById('signature-placeholder-' + config.key);
                const container = document.getElementById('signature-container-' + config.key);
                const hiddenInput = document.getElementById(config.input);
                const error = document.getElementById('signature-error-' + config.key);
                const ctx = canvas.getContext('2d');
                let drawing = false;
                let hasDrawn = false;

                function resizeCanvas() {
                    const rect = container.getBoundingClientRect();
                    const dpr = window.devicePixelRatio || 1;
                    const snapshot = hasDrawn ? canvas.toDataURL('image/png') : null;
                    canvas.width = rect.width * dpr;
                    canvas.height = rect.height * dpr;
                    canvas.style.width = rect.width + 'px';
                    canvas.style.height = rect.height + 'px';
                    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, rect.width, rect.height);
                    ctx.strokeStyle = '#122040';
                    ctx.lineWidth = 2;
                    ctx.lineCap = 'round';
                    ctx.lineJoin = 'round';
                    if (snapshot) {
                        const img = new Image();
                        img.onload = function () { ctx.drawImage(img, 0, 0, rect.width, rect.height); };
                        img.src = snapshot;
                    }
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
                    error.classList.add('hidden');
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

                signatures[config.key] = {
                    clear: function () {
                        const rect = container.getBoundingClientRect();
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, rect.width, rect.height);
                        ctx.strokeStyle = '#122040';
                        ctx.lineWidth = 2;
                        ctx.lineCap = 'round';
                        ctx.lineJoin = 'round';
                        hasDrawn = false;
                        hiddenInput.value = '';
                        placeholder.classList.remove('hidden');
                    },
                    hasDrawn: function () { return hasDrawn; },
                    showError: function () { error.classList.remove('hidden'); },
                };
            });

            window.clearSignature = function (key) {
                if (signatures[key]) signatures[key].clear();
            };

            document.getElementById('inspection-form').addEventListener('submit', function (e) {
                ['manager', 'staff'].forEach(function (key) {
                    if (!signatures[key].hasDrawn()) {
                        e.preventDefault();
                        signatures[key].showError();
                    }
                });
            });
        })();
    </script>
</x-app-layout>
