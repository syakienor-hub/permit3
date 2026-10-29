<x-app-layout>
    <x-slot name="title">Buat Daftar Periksa</x-slot>

    @php
        $forSite = $site;
    @endphp

    <div class="mb-5">
        <a href="/admin/work-inspections" class="text-sm text-gray-500 hover:text-inka-navy">&larr; Kembali ke Daftar Periksa</a>
        <p class="text-xs font-bold uppercase tracking-wider text-orange-500 mt-3">Form K3LH IV-01.013</p>
        <h1 class="text-xl sm:text-2xl font-extrabold text-gray-800 mt-1">Daftar Periksa Izin Pekerjaan Berisiko Tinggi</h1>
        <p class="text-sm text-gray-500 mt-1">
            Diisi melalui dashboard Staff HSE site <span class="font-semibold text-gray-700">{{ $forSite }}</span>.
            Tanda tangan pelaksana dan Safety Officer diambil pada satu perangkat yang sama.
        </p>
    </div>

    <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        Bila Daftar Periksa Izin Kerja telah dilakukan dengan baik, pekerjaan boleh dilanjutkan sesuai ketentuan HSE.
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

    <form method="POST" action="/admin/work-inspections" id="inspection-form" class="space-y-5">
        @csrf

        {{-- DATA PEKERJAAN --}}
        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-800">1. Data Pekerjaan</h2>
                <p class="text-xs text-gray-400 mt-0.5">Identitas pekerjaan yang akan dilakukan.</p>
            </div>
            <div class="px-4 sm:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1.5">Site</label>
                    <p class="form-input bg-gray-50">{{ $forSite }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1.5">Tipe Pekerjaan <span class="text-red-500">*</span></label>
                    <select name="tipe" class="form-input" required>
                        <option value="Internal" @selected(old('tipe') === 'Internal')>Internal</option>
                        <option value="Eksternal" @selected(old('tipe') === 'Eksternal')>Eksternal (Kontraktor)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1.5">Divisi / Unit Pengaju <span class="text-red-500">*</span></label>
                    <input type="text" name="divisi_pengaju" value="{{ old('divisi_pengaju') }}" class="form-input" placeholder="Contoh: Divisi Teknik" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1.5">Nama Kontraktor / Pelaksana <span class="text-red-500">*</span></label>
                    <input type="text" name="kontraktor" value="{{ old('kontraktor') }}" class="form-input" placeholder="Nama perusahaan / pelaksana" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-500 mb-1.5">Pekerjaan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pekerjaan" value="{{ old('nama_pekerjaan') }}" class="form-input" placeholder="Contoh: Perbaikan pipa steam di Area Produksi" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-500 mb-1.5">Jenis Peralatan / Material</label>
                    <input type="text" name="jenis_peralatan" value="{{ old('jenis_peralatan') }}" class="form-input" placeholder="Contoh: Mesin las, gerinda, pipa steam">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-500 mb-1.5">Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="lokasi" value="{{ old('lokasi') }}" class="form-input" placeholder="Contoh: Area Produksi, Gedung B" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1.5">No. Telepon <span class="text-red-500">*</span></label>
                    <input type="text" name="telepon" value="{{ old('telepon') }}" class="form-input" placeholder="08xxxxxxxxxx" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1.5">Mulai Tanggal <span class="text-red-500">*</span></label>
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
                    <label class="block text-sm font-semibold text-gray-500 mb-1.5">Bila YA, izin apa yang telah dimintakan?</label>
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
                    <label class="block text-sm font-semibold text-gray-500 mb-1.5">Sebutkan APD khusus yang digunakan</label>
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
                        <label class="block text-sm font-semibold text-gray-500 mb-1.5">Nama Pelaksana LOTO <span class="text-red-500">*</span></label>
                        <input type="text" name="loto_nama" value="{{ old('loto_nama') }}" class="form-input" placeholder="Nama lengkap" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-500 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="loto_tanggal" value="{{ old('loto_tanggal') }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-500 mb-1.5">Jam <span class="text-red-500">*</span></label>
                        <input type="time" name="loto_jam" value="{{ old('loto_jam') }}" class="form-input" required>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-semibold text-gray-500">Tanda Tangan Pelaksana / User <span class="text-red-500">*</span></label>
                        <button type="button" onclick="clearSignature('pelaksana')" class="text-xs font-semibold text-gray-400 hover:text-red-500">Hapus</button>
                    </div>
                    <div id="signature-container-pelaksana" class="relative border-2 border-dashed border-gray-300 rounded-xl overflow-hidden bg-white cursor-crosshair w-full aspect-[3/1]">
                        <canvas id="signature-canvas-pelaksana" class="block w-full h-full" style="touch-action: none;"></canvas>
                        <span id="signature-placeholder-pelaksana" class="absolute inset-0 flex items-center justify-center text-sm text-gray-300 pointer-events-none select-none">Tanda tangan pelaksana di sini</span>
                    </div>
                    <input type="hidden" name="tanda_tangan" id="tanda-tangan-input">
                    <p id="signature-error-pelaksana" class="text-sm text-red-500 mt-1.5 hidden">Tanda tangan pelaksana wajib diisi.</p>
                </div>

                <div class="rounded-xl bg-gray-50 border border-gray-100 px-4 py-3 text-sm text-gray-600 mt-2">
                    Diperiksa kembali oleh Supervisor (opsional):
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-semibold text-gray-500 mb-1.5">Nama Supervisor</label>
                        <input type="text" name="supervisor_nama" value="{{ old('supervisor_nama') }}" class="form-input" placeholder="Nama supervisor">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-500 mb-1.5">Tanggal</label>
                        <input type="date" name="supervisor_tanggal" value="{{ old('supervisor_tanggal') }}" class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-500 mb-1.5">Jam</label>
                        <input type="time" name="supervisor_jam" value="{{ old('supervisor_jam') }}" class="form-input">
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <p class="text-sm font-semibold text-gray-700 mb-3">Tanda Tangan Safety Officer</p>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-500 mb-1.5">Nama Safety Officer <span class="text-red-500">*</span></label>
                            <input type="text" name="safety_officer_nama" value="{{ old('safety_officer_nama', auth()->user()->name) }}" class="form-input" required>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold text-gray-500">Tanda Tangan Safety Officer <span class="text-red-500">*</span></label>
                                <button type="button" onclick="clearSignature('staff')" class="text-xs font-semibold text-gray-400 hover:text-red-500">Hapus</button>
                            </div>
                            <div id="signature-container-staff" class="relative border-2 border-dashed border-gray-300 rounded-xl overflow-hidden bg-white cursor-crosshair w-full aspect-[3/1]">
                                <canvas id="signature-canvas-staff" class="block w-full h-full" style="touch-action: none;"></canvas>
                                <span id="signature-placeholder-staff" class="absolute inset-0 flex items-center justify-center text-sm text-gray-300 pointer-events-none select-none">Tanda tangan Safety Officer di sini</span>
                            </div>
                            <input type="hidden" name="staff_signature" id="staff-signature-input">
                            <p id="signature-error-staff" class="text-sm text-red-500 mt-1.5 hidden">Tanda tangan Safety Officer wajib diisi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="sticky bottom-0 -mx-4 px-4 py-3 bg-white/95 backdrop-blur border-t border-gray-100 sm:static sm:bg-transparent sm:border-0 sm:px-0 sm:py-0">
            <button type="submit" class="w-full sm:w-auto sm:float-right inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-inka-navy text-white text-sm font-bold hover:opacity-90 transition-opacity">
                Simpan Daftar Periksa
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
            <p class="text-xs text-gray-400 text-center sm:text-left mt-2 sm:mt-3 sm:clear-both">Daftar periksa langsung tersimpan setelah kedua tanda tangan diisi.</p>
        </div>
    </form>

    <script>
        (function () {
            const forms = [
                { key: 'pelaksana', input: 'tanda-tangan-input' },
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
                        ctx.strokeStyle = '#111d33';
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
                ['pelaksana', 'staff'].forEach(function (key) {
                    if (!signatures[key].hasDrawn()) {
                        e.preventDefault();
                        signatures[key].showError();
                    }
                });
            });
        })();
    </script>
</x-app-layout>
