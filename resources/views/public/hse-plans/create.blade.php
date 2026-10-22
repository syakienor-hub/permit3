<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan HSE Plan — INKA Madiun</title>
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
        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 18px 18px;
            padding-right: 38px;
            cursor: pointer;
        }
        input[type="file"].form-input { padding: 7px 10px; cursor: pointer; }
        input[type="file"].form-input::file-selector-button {
            margin-right: 12px; padding: 7px 16px;
            border: 1px solid #e5e7eb; border-radius: 9999px;
            background: #f3f4f6; color: #374151;
            font-size: 0.85rem; font-weight: 600; font-family: inherit;
            cursor: pointer; transition: background-color 0.15s, border-color 0.15s;
        }
        input[type="file"].form-input::file-selector-button:hover { background: #e5e7eb; border-color: #d1d5db; }
        input[type="file"].form-input::-webkit-file-upload-button {
            margin-right: 12px; padding: 7px 16px;
            border: 1px solid #e5e7eb; border-radius: 9999px;
            background: #f3f4f6; color: #374151;
            font-size: 0.85rem; font-weight: 600; font-family: inherit;
            cursor: pointer;
        }
        .form-input:focus { outline: none; border-color: #111d33; box-shadow: 0 0 0 3px rgba(17,29,51,0.08); }
        .form-input:disabled { background: #f9fafb; color: #9ca3af; }
    </style>
</head>
<body class="bg-slate-50 text-gray-900 min-h-screen">
    @php
        $personilRows = old('personil', [['nama' => '', 'tugas' => '']]);
        if (! is_array($personilRows) || empty($personilRows)) {
            $personilRows = [['nama' => '', 'tugas' => '']];
        }
        $peralatanRows = old('peralatan', [['nama' => '', 'jumlah' => '']]);
        if (! is_array($peralatanRows) || empty($peralatanRows)) {
            $peralatanRows = [['nama' => '', 'jumlah' => '']];
        }
        $apdOptions = [
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
        $pernyataan = [
            [
                'field' => 'bahan_b3',
                'label' => 'Apakah pekerjaan menggunakan bahan-bahan B3?',
                'note' => 'Jika Ya, wajib melampirkan MSDS.',
                'file' => 'msds_file',
                'file_id' => 'msds-field',
                'file_label' => 'MSDS Bahan B3',
            ],
            [
                'field' => 'limbah_b3',
                'label' => 'Apakah pekerjaan menghasilkan limbah B3?',
                'note' => 'Jika Ya, wajib melampirkan rencana pengelolaan limbah B3.',
                'file' => 'limbah_b3_file',
                'file_id' => 'limbah-field',
                'file_label' => 'Rencana Pengelolaan Limbah B3',
            ],
            [
                'field' => 'prosedur_kerja',
                'label' => 'Memiliki prosedur / instruksi kerja yang terkait?',
                'note' => 'Jika Ya, wajib melampirkan prosedur / instruksi kerja.',
                'file' => 'prosedur_kerja_file',
                'file_id' => 'prosedur-field',
                'file_label' => 'Prosedur / Instruksi Kerja',
            ],
            [
                'field' => 'rencana_inspeksi',
                'label' => 'Memiliki rencana pemantauan / inspeksi selama proses pekerjaan berlangsung?',
                'note' => 'Jika Ya, wajib melampirkan rencana pemantauan / inspeksi.',
                'file' => 'inspeksi_file',
                'file_id' => 'inspeksi-field',
                'file_label' => 'Rencana Pemantauan / Inspeksi',
            ],
            [
                'field' => 'tanggap_darurat',
                'label' => 'Menyediakan fasilitas peralatan tanggap darurat seperti APAR dan lain sebagainya?',
                'note' => null,
                'file' => null,
                'file_id' => null,
                'file_label' => null,
            ],
            [
                'field' => 'struktur_organisasi',
                'label' => 'Memiliki struktur organisasi proyek?',
                'note' => 'Jika Ya, wajib melampirkan struktur organisasi proyek.',
                'file' => 'struktur_file',
                'file_id' => 'struktur-field',
                'file_label' => 'Struktur Organisasi Proyek',
            ],
        ];
    @endphp

    <header class="bg-white border-b border-gray-100 sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 md:px-6 h-16 flex items-center gap-2.5 sm:gap-3">
            <a href="/" class="flex items-center gap-2.5 mr-auto min-w-0">
                <img src="{{ asset('assets/images/logoinka.svg') }}" alt="INKA" class="h-6 sm:h-7 w-auto shrink-0">
                <div class="border-l border-gray-200 pl-2.5 sm:pl-3 whitespace-nowrap">
                    <span class="text-[13px] sm:text-sm font-bold">HSE Plan</span>
                    <span class="block text-[9px] sm:text-[10px] font-semibold text-gray-500 tracking-wide">INKA MADIUN</span>
                </div>
            </a>
            <a href="/" class="text-sm font-semibold text-gray-500 hover:text-gray-800 transition-colors whitespace-nowrap">Beranda</a>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 md:px-6 py-6 md:py-8">
        <div class="mb-6">
            <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">Ajukan HSE Plan</h1>
            <p class="text-sm text-gray-500 mt-1">Tanpa login. Isi rencana keselamatan kerja, lalu kirim untuk persetujuan <span class="font-semibold text-gray-700">Manager HSE</span>.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl px-5 py-4">
                <p class="text-sm font-semibold text-red-700 mb-1">Ada yang perlu diperbaiki:</p>
                <ul class="text-sm text-red-600 list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/ajukan-hse-plan" method="POST" enctype="multipart/form-data" id="hse-plan-form" class="space-y-5">
            @csrf

            {{-- DATA PENGAJUAN (sesuai Form K3LH IV-01.053) --}}
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">Data Pengajuan</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Sesuai Form K3LH IV-01.053 — HSE Plan</p>
                </div>
                <div class="px-4 md:px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="form-label">1. Nomor PO</label>
                        <input type="text" name="no_po" class="form-input" value="{{ old('no_po') }}" placeholder="Nomor purchase order">
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label">2. Deskripsi Pekerjaan <span class="text-red-500">*</span></label>
                        <textarea name="deskripsi_pekerjaan" rows="3" class="form-input" placeholder="Uraikan lingkup dan tahapan pekerjaan" required>{{ old('deskripsi_pekerjaan') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <p class="form-label mb-2">3. Lama Pekerjaan <span class="text-red-500">*</span></p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">a. Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-input" value="{{ old('tanggal_mulai') }}" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-input" value="{{ old('tanggal_selesai') }}" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">b. Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="form-input" value="{{ old('jam_mulai') }}" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="form-input" value="{{ old('jam_selesai') }}" required>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">4. Nama Kontraktor <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kontraktor" class="form-input" value="{{ old('nama_kontraktor') }}" placeholder="Nama perusahaan pelaksana" required>
                    </div>
                    <div>
                        <label class="form-label">5. Nama Pimpinan Proyek <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pimpinan_proyek" class="form-input" value="{{ old('nama_pimpinan_proyek') }}" placeholder="Penanggung jawab proyek" required>
                    </div>
                </div>
            </section>

            {{-- INFORMASI TAMBAHAN --}}
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">Informasi Pengajuan</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Dipakai untuk penempatan review dan pengecekan status</p>
                </div>
                <div class="px-4 md:px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="form-label">Site <span class="text-red-500">*</span></label>
                        <select name="site" class="form-input" required>
                            <option value="Madiun" @selected(old('site', 'Madiun') === 'Madiun')>Madiun</option>
                            <option value="Banyuwangi" @selected(old('site') === 'Banyuwangi')>Banyuwangi</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Tipe Pekerjaan <span class="text-red-500">*</span></label>
                        <select name="tipe" class="form-input" required>
                            <option value="Internal" @selected(old('tipe', 'Internal') === 'Internal')>Internal</option>
                            <option value="Eksternal" @selected(old('tipe') === 'Eksternal')>Eksternal</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Divisi / Unit Pengaju <span class="text-red-500">*</span></label>
                        <input type="text" name="divisi_pengaju" class="form-input" value="{{ old('divisi_pengaju') }}" placeholder="Contoh: Divisi Teknik" required>
                    </div>
                    <div>
                        <label class="form-label">Nomor Telepon <span class="text-red-500">*</span></label>
                        <input type="text" name="telepon" class="form-input" value="{{ old('telepon') }}" placeholder="Nomor untuk cek status" required>
                    </div>
                </div>
            </section>

            {{-- PERSONIL --}}
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-gray-800">6. Nama &amp; Tugas Setiap Personil</h2>
                        <p class="text-sm text-gray-400 mt-0.5">Lampirkan sertifikat keahlian setiap personil pada bagian dokumen</p>
                    </div>
                    <button type="button" onclick="addPersonil()" class="text-sm font-semibold text-inka-navy hover:underline whitespace-nowrap">+ Tambah</button>
                </div>
                <div class="px-4 md:px-6 py-5 space-y-2" id="personil-list">
                    @foreach($personilRows as $i => $row)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 personil-row">
                            <input type="text" name="personil[{{ $i }}][nama]" value="{{ $row['nama'] ?? '' }}" placeholder="Nama personil" class="form-input" required>
                            <div class="flex gap-2">
                                <input type="text" name="personil[{{ $i }}][tugas]" value="{{ $row['tugas'] ?? '' }}" placeholder="Tugas personil" class="form-input" required>
                                <button type="button" onclick="removeRow(this, 'personil-row')" class="shrink-0 px-3 rounded-xl border border-gray-200 text-gray-400 hover:text-red-500 hover:border-red-200 transition-colors" aria-label="Hapus baris">&times;</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- APD --}}
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">7. Jenis APD yang Digunakan</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Pilih minimal satu APD</p>
                </div>
                <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($apdOptions as $key => $label)
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-inka-navy/30 hover:bg-blue-50/30 cursor-pointer transition-colors">
                            <input type="checkbox" name="apd[]" value="{{ $key }}" @checked(in_array($key, (array) old('apd', []), true))
                                class="w-4 h-4 rounded text-inka-navy border-gray-300 focus:ring-inka-navy">
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                    <div class="col-span-full flex items-center gap-2 mt-1">
                        <span class="text-sm text-gray-500 shrink-0">Lainnya:</span>
                        <input type="text" name="apd_lainnya" class="form-input" value="{{ old('apd_lainnya') }}" placeholder="Sebutkan APD tambahan...">
                    </div>
                </div>
            </section>

            {{-- PERALATAN --}}
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-gray-800">8. Daftar Peralatan Kerja</h2>
                        <p class="text-sm text-gray-400 mt-0.5">Lampirkan sertifikat peralatan yang dipersyaratkan</p>
                    </div>
                    <button type="button" onclick="addPeralatan()" class="text-sm font-semibold text-inka-navy hover:underline whitespace-nowrap">+ Tambah</button>
                </div>
                <div class="px-4 md:px-6 py-5 space-y-2" id="peralatan-list">
                    @foreach($peralatanRows as $i => $row)
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 peralatan-row">
                            <input type="text" name="peralatan[{{ $i }}][nama]" value="{{ $row['nama'] ?? '' }}" placeholder="Nama alat" class="form-input sm:col-span-2" required>
                            <div class="flex gap-2">
                                <input type="text" name="peralatan[{{ $i }}][jumlah]" value="{{ $row['jumlah'] ?? '' }}" placeholder="Jumlah" class="form-input" required>
                                <button type="button" onclick="removeRow(this, 'peralatan-row')" class="shrink-0 px-3 rounded-xl border border-gray-200 text-gray-400 hover:text-red-500 hover:border-red-200 transition-colors" aria-label="Hapus baris">&times;</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- DOKUMEN & PERNYATAAN --}}
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">9. Job Safety Analysis &amp; Dokumen Pendukung</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Format PDF, DOC, XLS, atau gambar. Maksimal 10MB per file.</p>
                </div>
                <div class="px-4 md:px-6 py-5 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Job Safety Analysis / JSA <span class="text-red-500">*</span></label>
                            <input type="file" name="jsa_file" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" required>
                        </div>
                        <div>
                            <label class="form-label">Sertifikat Keahlian Personil <span class="text-red-500">*</span></label>
                            <input type="file" name="sertifikat_personil[]" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" multiple required>
                        </div>
                        <div>
                            <label class="form-label">Sertifikat Peralatan <span class="text-red-500">*</span></label>
                            <input type="file" name="sertifikat_peralatan[]" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" multiple required>
                        </div>
                    </div>

                    <div class="space-y-5 border-t border-gray-100 pt-4">
                        @foreach($pernyataan as $item)
                            @php
                                $hasAnswer = old($item['field']) !== null;
                                $isYes = filter_var(old($item['field'], false), FILTER_VALIDATE_BOOLEAN);
                                $number = 10 + $loop->index;
                            @endphp
                            <div>
                                <p class="text-sm font-semibold text-gray-700">{{ $number }}. {{ $item['label'] }} <span class="text-red-500">*</span></p>
                                <div class="mt-2 flex items-center gap-5">
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="radio" name="{{ $item['field'] }}" value="1" @checked($hasAnswer && $isYes) @if($item['file']) onchange="toggleFile('{{ $item['file_id'] }}', true)" @endif required
                                            class="w-4 h-4 text-inka-navy border-gray-300 focus:ring-inka-navy">
                                        Ya
                                    </label>
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="radio" name="{{ $item['field'] }}" value="0" @checked($hasAnswer && ! $isYes) @if($item['file']) onchange="toggleFile('{{ $item['file_id'] }}', false)" @endif
                                            class="w-4 h-4 text-inka-navy border-gray-300 focus:ring-inka-navy">
                                        Tidak
                                    </label>
                                </div>
                                @if($item['note'])
                                    <p class="text-xs text-gray-400 mt-1.5">{{ $item['note'] }}</p>
                                @endif
                                @if($item['file'])
                                    <div id="{{ $item['file_id'] }}" class="{{ $isYes ? '' : 'hidden' }} mt-2">
                                        <label class="block text-xs font-semibold text-gray-500 mb-1">{{ $item['file_label'] }}</label>
                                        <input type="file" name="{{ $item['file'] }}" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- PENGESAHAN / TANDA TANGAN --}}
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-gray-800">Pengesahan</h2>
                        <p class="text-sm text-gray-400 mt-0.5">Dibuat oleh Kontraktor dan disetujui oleh Manager HSE.</p>
                    </div>
                    <button type="button" onclick="clearSignature()" class="text-sm font-semibold text-gray-400 hover:text-red-500 border border-gray-200 hover:border-red-300 px-3 py-1.5 rounded-lg transition-colors">Hapus</button>
                </div>
                <div class="px-4 md:px-6 py-5">
                    <p class="text-sm text-gray-600 mb-5">Madiun, ................................</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Dibuat oleh,<br>Kontraktor</p>
                            <div id="signature-container" class="relative mt-3 border-2 border-dashed border-gray-300 rounded-xl overflow-hidden bg-white hover:border-inka-navy/40 transition-colors cursor-crosshair w-full aspect-[2/1]">
                                <canvas id="signature-canvas" class="block w-full h-full" style="touch-action: none;"></canvas>
                                <span id="signature-placeholder" class="absolute inset-0 flex items-center justify-center text-sm text-gray-300 pointer-events-none select-none">Tanda tangan di sini</span>
                            </div>
                            <input type="hidden" name="tanda_tangan" id="tanda-tangan-input">
                            <p id="signature-error" class="text-sm text-red-500 mt-1.5 hidden">Tanda tangan wajib diisi sebelum mengirim.</p>
                            <p class="mt-2 text-sm font-semibold text-gray-700">({{ old('nama_pimpinan_proyek') ?: 'Pimpinan Proyek' }})</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Disetujui oleh,<br>Manager HSE</p>
                            <div class="mt-3 w-full aspect-[2/1] rounded-xl border border-dashed border-gray-200 bg-gray-50 flex items-center justify-center px-4 text-center">
                                <p class="text-xs text-gray-400">Diisi oleh Manager HSE setelah HSE Plan direview.</p>
                            </div>
                            <p class="mt-2 text-sm font-semibold text-gray-400">(..............................)</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pb-8">
                <a href="/" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 text-center hover:bg-gray-50 transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-inka-navy text-white text-sm font-semibold hover:opacity-90 transition-opacity">Kirim HSE Plan</button>
            </div>
        </form>
    </main>

    <script>
        let personilCount = {{ count($personilRows) }};
        let peralatanCount = {{ count($peralatanRows) }};

        function addPersonil() {
            const list = document.getElementById('personil-list');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 sm:grid-cols-2 gap-2 personil-row';
            row.innerHTML = '<input type="text" name="personil[' + personilCount + '][nama]" placeholder="Nama personil" class="form-input" required>'
                + '<div class="flex gap-2"><input type="text" name="personil[' + personilCount + '][tugas]" placeholder="Tugas personil" class="form-input" required>'
                + '<button type="button" onclick="removeRow(this, \'personil-row\')" class="shrink-0 px-3 rounded-xl border border-gray-200 text-gray-400 hover:text-red-500 hover:border-red-200 transition-colors" aria-label="Hapus baris">&times;</button></div>';
            list.appendChild(row);
            personilCount++;
        }

        function addPeralatan() {
            const list = document.getElementById('peralatan-list');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 sm:grid-cols-3 gap-2 peralatan-row';
            row.innerHTML = '<input type="text" name="peralatan[' + peralatanCount + '][nama]" placeholder="Nama alat" class="form-input sm:col-span-2" required>'
                + '<div class="flex gap-2"><input type="text" name="peralatan[' + peralatanCount + '][jumlah]" placeholder="Jumlah" class="form-input" required>'
                + '<button type="button" onclick="removeRow(this, \'peralatan-row\')" class="shrink-0 px-3 rounded-xl border border-gray-200 text-gray-400 hover:text-red-500 hover:border-red-200 transition-colors" aria-label="Hapus baris">&times;</button></div>';
            list.appendChild(row);
            peralatanCount++;
        }

        function removeRow(button, className) {
            const rows = document.querySelectorAll('.' + className);
            if (rows.length <= 1) return;
            button.closest('.' + className).remove();
        }

        function toggleFile(id, show) {
            const element = document.getElementById(id);
            if (!element) return;
            element.classList.toggle('hidden', !show);
        }

        (function () {
            const canvas = document.getElementById('signature-canvas');
            const placeholder = document.getElementById('signature-placeholder');
            const hiddenInput = document.getElementById('tanda-tangan-input');
            const container = document.getElementById('signature-container');
            const ctx = canvas.getContext('2d');
            let drawing = false, hasDrawn = false;

            function resizeCanvas() {
                const rect = container.getBoundingClientRect();
                if (rect.width === 0) return;
                const isMobile = window.matchMedia('(max-width: 767px)').matches;
                const rawDpr = window.devicePixelRatio || 1;
                const dpr = isMobile ? Math.min(rawDpr, 1.5) : rawDpr;
                canvas.width = rect.width * dpr;
                canvas.height = rect.height * dpr;
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
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
                hiddenInput.value = window.matchMedia('(max-width: 767px)').matches ? canvas.toDataURL('image/jpeg', 0.7) : canvas.toDataURL('image/png');
            }

            canvas.addEventListener('mousedown', startDraw);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDraw);
            canvas.addEventListener('mouseleave', stopDraw);
            canvas.addEventListener('touchstart', startDraw, { passive: false });
            canvas.addEventListener('touchmove', draw, { passive: false });
            canvas.addEventListener('touchend', stopDraw);

            window.clearSignature = function () {
                ctx.setTransform(1, 0, 0, 1, 0, 0);
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                const rect = container.getBoundingClientRect();
                const rawDpr = window.devicePixelRatio || 1;
                const dpr = window.matchMedia('(max-width: 767px)').matches ? Math.min(rawDpr, 1.5) : rawDpr;
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                ctx.strokeStyle = '#111d33';
                ctx.lineWidth = 2;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                hasDrawn = false;
                hiddenInput.value = '';
                placeholder.classList.remove('hidden');
            };

            document.getElementById('hse-plan-form').addEventListener('submit', function (e) {
                if (!hasDrawn) {
                    e.preventDefault();
                    document.getElementById('signature-error').classList.remove('hidden');
                    container.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        })();
    </script>
</body>
</html>
