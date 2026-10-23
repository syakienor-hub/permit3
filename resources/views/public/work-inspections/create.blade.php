<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Periksa Izin Pekerjaan Berisiko Tinggi — PT INKA (Persero)</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logok3.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .form-label { display: block; font-size: 0.875rem; font-weight: 600; color: #4b5563; margin-bottom: 6px; }
        .form-input {
            width: 100%; padding: 9px 12px; border: 1px solid #e5e7eb; border-radius: 10px;
            font-size: 0.95rem; color: #1f2937; background-color: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-input:focus { outline: none; border-color: #111d33; box-shadow: 0 0 0 3px rgba(17,29,51,0.08); }
        select.form-input { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; background-size: 16px; padding-right: 34px; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-gray-800">

<header class="bg-white border-b border-gray-100">
    <div class="max-w-5xl mx-auto px-4 py-3 flex items-center gap-3">
        <img src="{{ asset('assets/images/logoinka.svg') }}" alt="INKA" class="h-7 w-auto">
        <div class="border-l border-gray-200 pl-3">
            <p class="text-sm font-bold leading-tight">SIMONIKA</p>
            <p class="text-[10px] font-semibold text-gray-500">PT INKA (Persero)</p>
        </div>
        <div class="ml-auto flex items-center gap-3 text-xs font-semibold">
            <a href="/" class="text-gray-500 hover:text-inka-navy">Beranda</a>
            <a href="/ajukan-permit" class="text-gray-500 hover:text-inka-navy">Safety Permit</a>
            <a href="/cek-daftar-periksa" class="text-inka-navy hover:underline">Cek Status</a>
        </div>
    </div>
</header>

<main class="max-w-5xl mx-auto px-4 py-8 sm:py-10">
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-wider text-orange-500">Form K3LH IV-01.013</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-1">Daftar Periksa Izin Pekerjaan Berisiko Tinggi</h1>
        <p class="text-sm text-gray-500 mt-2 max-w-3xl">
            Isi daftar periksa ini sebelum mengajukan Safety Permit. Setelah disetujui Staff HSE,
            nomor daftar periksa dapat dipakai pada form Safety Permit.
        </p>
    </div>

    <div class="mb-5 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        <svg class="w-5 h-5 shrink-0 text-amber-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p>Bila "Daftar Periksa Izin Kerja" telah dilakukan dengan baik, pekerjaan boleh dilanjutkan setelah mendapat pengesahan Safety Permit.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold mb-1">Periksa kembali isian berikut:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="/daftar-periksa" id="inspection-form" class="space-y-5">
        @csrf

        {{-- DATA PEKERJAAN --}}
        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-800">1. Data Pekerjaan</h2>
                <p class="text-xs text-gray-400 mt-0.5">Identitas pekerjaan yang akan dilakukan.</p>
            </div>
            <div class="px-4 sm:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Site <span class="text-red-500">*</span></label>
                    <select name="site" class="form-input" required>
                        @foreach(\App\Models\WorkInspection::SITES as $site)
                            <option value="{{ $site }}" @selected(old('site', 'Madiun') === $site)>Site {{ $site }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Tipe Pekerjaan <span class="text-red-500">*</span></label>
                    <select name="tipe" class="form-input" required>
                        <option value="Internal" @selected(old('tipe') === 'Internal')>Internal</option>
                        <option value="Eksternal" @selected(old('tipe') === 'Eksternal')>Eksternal (Kontraktor)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Divisi / Unit Pengaju <span class="text-red-500">*</span></label>
                    <input type="text" name="divisi_pengaju" value="{{ old('divisi_pengaju') }}" class="form-input" placeholder="Contoh: Divisi Teknik" required>
                </div>
                <div>
                    <label class="form-label">Nama Kontraktor / Pelaksana <span class="text-red-500">*</span></label>
                    <input type="text" name="kontraktor" value="{{ old('kontraktor') }}" class="form-input" placeholder="Nama perusahaan / pelaksana" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Pekerjaan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pekerjaan" value="{{ old('nama_pekerjaan') }}" class="form-input" placeholder="Contoh: Perbaikan pipa steam di Area Produksi" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Jenis Peralatan / Material</label>
                    <input type="text" name="jenis_peralatan" value="{{ old('jenis_peralatan') }}" class="form-input" placeholder="Contoh: Mesin las, gerinda, pipa steam">
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="lokasi" value="{{ old('lokasi') }}" class="form-input" placeholder="Contoh: Area Produksi, Gedung B" required>
                </div>
                <div>
                    <label class="form-label">No. Telepon <span class="text-red-500">*</span></label>
                    <input type="text" name="telepon" value="{{ old('telepon') }}" class="form-input" placeholder="08xxxxxxxxxx" required>
                    <p class="text-xs text-gray-400 mt-1">Dipakai untuk cek status.</p>
                </div>
                <div>
                    <label class="form-label">Mulai Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="form-input" required>
                </div>
            </div>
        </section>

        {{-- BAGIAN A --}}
        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-800">2. Pemeriksaan Sumber Energi &amp; Area Kerja</h2>
                <p class="text-xs text-gray-400 mt-0.5">Jawab YA atau TIDAK sesuai kondisi sebenarnya di lapangan.</p>
            </div>
            <div class="px-4 sm:px-6">
                <x-yn-question field="izin_lain" label="Apakah ada izin lain yang telah dimintakan?" />
                <div class="pb-4">
                    <label class="form-label">Bila YA, izin apa yang telah dimintakan?</label>
                    <input type="text" name="answers[izin_lain_ket]" value="{{ old('answers.izin_lain_ket') }}" class="form-input" placeholder="Jenis izin lain (opsional)">
                </div>

                <x-yn-question field="listrik_diputus" label="Apakah telah dilakukan pemutusan aliran listrik?" hint="Bila TIDAK, harap lakukan pemutusan terlebih dahulu." />
                <x-yn-question field="keran_ditutup" label="Apakah keran-keran telah ditutup dan pompa telah diputus aliran listriknya?" />
                <x-yn-question field="pipa_dikosongkan" label="Apakah pipa yang mungkin masih berisi fluida berbahaya telah dikosongkan?" hint="Diisi bila jawaban pertanyaan sebelumnya YA." />
                <x-yn-question field="tanda_bahaya" label="Apakah telah diberikan tanda &quot;BERBAHAYA ADA PERBAIKAN/ADA PEKERJAAN&quot;?" />
                <x-yn-question field="karyawan_diberitahu" label="Apakah karyawan di sekitar area kerja telah diberitahu?" hint="Bila tanda belum dipasang, lakukan pemasangan terlebih dahulu." />

                <div class="py-4 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-700 leading-relaxed">Pastikan hal berikut telah dilakukan:</p>
                </div>
                <x-yn-question field="alat_listrik_diputus" label="Seluruh alat yang digerakkan listrik telah diputus aliran listriknya." />
                <x-yn-question field="pipa_fluida_diputus" label="Seluruh pipa masuk fluida yang tersambung telah diputus alirannya." />
                <x-yn-question field="pipa_dikeringkan" label="Seluruh pipa telah dikeringkan dan tidak lagi mengandung fluida berbahaya." />
                <x-yn-question field="energi_dilepas" label="Apakah energi yang tersisa di dalam peralatan atau mesin telah dihilangkan/dilepaskan?" hint="Bila TIDAK, lakukan pelepasan energi." />
            </div>
        </section>

        {{-- BAGIAN B --}}
        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-800">3. Pemeriksaan Lanjutan</h2>
                <p class="text-xs text-gray-400 mt-0.5">Pemeriksaan APD, area kerja, dan kesiapan darurat.</p>
            </div>
            <div class="px-4 sm:px-6">
                <x-yn-question field="tes_energi" label="Apakah dilakukan pengetesan bahwa energi tidak lagi tersisa atau telah dilepaskan?" />
                <x-yn-question field="masih_ada_aliran" label="Apakah masih ada aliran listrik atau saluran lain yang berhubungan dengan mesin atau peralatan?" />
                <x-yn-question field="supervisor_siap" label="Apakah Supervisor telah siap memeriksa bahwa tanda &quot;BERBAHAYA ADA PERBAIKAN/ADA PEKERJAAN&quot; tetap terpasang?" />
                <x-yn-question field="apd_khusus" label="Apakah Alat Pelindung Diri khusus untuk pekerjaan ini telah tersedia dan siap dipakai?" />
                <div class="pb-4">
                    <label class="form-label">Sebutkan APD khusus yang digunakan</label>
                    <input type="text" name="answers[apd_khusus_ket]" value="{{ old('answers.apd_khusus_ket') }}" class="form-input" placeholder="Contoh: full body harness, respirator, sarung tangan tahan panas">
                </div>
                <x-yn-question field="perancah_baik" label="Apakah semua perancah/penyangga/tangga telah terpasang dengan baik?" />
                <x-yn-question field="barikade" label="Apakah telah terpasang barikade/pagar/police line di area kerja yang berbahaya?" />
                <x-yn-question field="apar" label="Apakah telah disediakan APAR dan peralatan keadaan darurat lainnya di lokasi kerja?" />
                <x-yn-question field="evakuasi_diberitahu" label="Apakah seluruh tenaga kerja sudah diberitahu jalur evakuasi, titik kumpul, dan sarana darurat lainnya?" />
                <x-yn-question field="safety_briefing" label="Apakah seluruh tenaga kerja sudah dilakukan safety briefing?" />
            </div>
        </section>

        {{-- BAGIAN C --}}
        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-800">4. LOTO &amp; Tanda Tangan</h2>
                <p class="text-xs text-gray-400 mt-0.5">Lock Out &amp; Tag Out diisi oleh pelaksana pekerjaan.</p>
            </div>
            <div class="px-4 sm:px-6 py-5 space-y-4">
                <div class="rounded-xl bg-gray-50 border border-gray-100 px-4 py-3 text-sm text-gray-600">
                    "LOTO (Lock Out &amp; Tag Out)" telah diisi/dipasang dengan sebenarnya oleh:
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-3">
                        <label class="form-label">Nama Pelaksana LOTO <span class="text-red-500">*</span></label>
                        <input type="text" name="loto_nama" value="{{ old('loto_nama') }}" class="form-input" placeholder="Nama lengkap" required>
                    </div>
                    <div>
                        <label class="form-label">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="loto_tanggal" value="{{ old('loto_tanggal') }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Jam <span class="text-red-500">*</span></label>
                        <input type="time" name="loto_jam" value="{{ old('loto_jam') }}" class="form-input" required>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="form-label !mb-0">Tanda Tangan Pelaksana <span class="text-red-500">*</span></label>
                        <button type="button" onclick="clearSignature()" class="text-xs font-semibold text-gray-400 hover:text-red-500">Hapus</button>
                    </div>
                    <div id="signature-container" class="relative border-2 border-dashed border-gray-300 rounded-xl overflow-hidden bg-white cursor-crosshair w-full aspect-[3/1]">
                        <canvas id="signature-canvas" class="block w-full h-full" style="touch-action: none;"></canvas>
                        <span id="signature-placeholder" class="absolute inset-0 flex items-center justify-center text-sm text-gray-300 pointer-events-none select-none">Tanda tangan di sini</span>
                    </div>
                    <input type="hidden" name="tanda_tangan" id="tanda-tangan-input">
                    <p id="signature-error" class="text-sm text-red-500 mt-1.5 hidden">Tanda tangan wajib diisi.</p>
                </div>

                <div class="rounded-xl bg-gray-50 border border-gray-100 px-4 py-3 text-sm text-gray-600 mt-2">
                    Diperiksa kembali oleh Supervisor (opsional, dapat dilengkapi saat verifikasi):
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-3">
                        <label class="form-label">Nama Supervisor</label>
                        <input type="text" name="supervisor_nama" value="{{ old('supervisor_nama') }}" class="form-input" placeholder="Nama supervisor">
                    </div>
                    <div>
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="supervisor_tanggal" value="{{ old('supervisor_tanggal') }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Jam</label>
                        <input type="time" name="supervisor_jam" value="{{ old('supervisor_jam') }}" class="form-input">
                    </div>
                </div>

                <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                    Bagian <strong>Safety Officer</strong> akan diisi oleh Staff HSE saat menyetujui daftar periksa ini.
                </div>
            </div>
        </section>

        <div class="sticky bottom-0 -mx-4 px-4 py-3 bg-white/95 backdrop-blur border-t border-gray-100 sm:static sm:bg-transparent sm:border-0 sm:px-0 sm:py-0">
            <button type="submit" class="w-full sm:w-auto sm:float-right inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-inka-navy text-white text-sm font-bold hover:opacity-90 transition-opacity">
                Kirim Daftar Periksa
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
            <p class="text-xs text-gray-400 text-center sm:text-left mt-2 sm:mt-3 sm:clear-both">Setelah dikirim, daftar periksa menunggu verifikasi Staff HSE.</p>
        </div>
    </form>
</main>

<script>
    (function () {
        const canvas = document.getElementById('signature-canvas');
        const placeholder = document.getElementById('signature-placeholder');
        const container = document.getElementById('signature-container');
        const hiddenInput = document.getElementById('tanda-tangan-input');
        const form = document.getElementById('inspection-form');
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
            ctx.strokeStyle = '#111d33';
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
            hiddenInput.value = window.matchMedia('(max-width: 767px)').matches
                ? canvas.toDataURL('image/jpeg', 0.7)
                : canvas.toDataURL('image/png');
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
            const rect = container.getBoundingClientRect();
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, rect.width, rect.height);
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
</body>
</html>
