<x-app-layout>
    <x-slot name="title">Edit HSE Plan</x-slot>

    <style>
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
        .form-input:focus { outline: none; border-color: #111d33; box-shadow: 0 0 0 3px rgba(17,29,51,0.08); }
    </style>

    @php
        $personilRows = old('personil', $hsePlan->personil ?? []);
        if (! is_array($personilRows) || empty($personilRows)) {
            $personilRows = [['nama' => '', 'tugas' => '']];
        }
        $peralatanRows = old('peralatan', $hsePlan->peralatan ?? []);
        if (! is_array($peralatanRows) || empty($peralatanRows)) {
            $peralatanRows = [['nama' => '', 'jumlah' => '']];
        }
        $selectedApd = (array) old('apd', $hsePlan->apd ?? []);
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
            ['field' => 'bahan_b3', 'label' => 'Apakah pekerjaan menggunakan bahan-bahan B3?'],
            ['field' => 'limbah_b3', 'label' => 'Apakah pekerjaan menghasilkan limbah B3?'],
            ['field' => 'prosedur_kerja', 'label' => 'Memiliki prosedur / instruksi kerja yang terkait?'],
            ['field' => 'rencana_inspeksi', 'label' => 'Memiliki rencana pemantauan / inspeksi selama proses pekerjaan berlangsung?'],
            ['field' => 'tanggap_darurat', 'label' => 'Menyediakan fasilitas peralatan tanggap darurat seperti APAR dan lain sebagainya?'],
            ['field' => 'struktur_organisasi', 'label' => 'Memiliki struktur organisasi proyek?'],
        ];
    @endphp

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl px-5 py-4">
            <p class="text-sm font-semibold text-red-700 mb-1">Ada yang perlu diperbaiki:</p>
            <ul class="text-sm text-red-600 list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(auth()->user()->role === 'staff' && $hsePlan->status !== \App\Models\HsePlan::STATUS_REVIEW)
        <div class="mb-5 rounded-2xl border border-orange-200 bg-orange-50 px-5 py-4">
            <p class="text-sm font-semibold text-orange-800">Persetujuan akan direset</p>
            <p class="text-sm text-orange-700 mt-1">HSE Plan ini berstatus {{ $hsePlan->status }}. Setelah disimpan, status kembali ke Review Manager dan tanda tangan persetujuan sebelumnya tidak berlaku.</p>
        </div>
    @endif

    <div class="mb-5">
        <a href="/admin/hse-plans/{{ $hsePlan->id }}" class="text-sm font-semibold text-gray-500 hover:text-inka-navy">&larr; Kembali ke Detail HSE Plan</a>
        <h1 class="text-xl sm:text-2xl font-extrabold text-gray-800 mt-1">Edit HSE Plan</h1>
        <p class="text-sm text-gray-500 mt-0.5 font-mono">{{ $hsePlan->no_hse_plan }}</p>
    </div>

    <form action="/admin/hse-plans/{{ $hsePlan->id }}" method="POST" enctype="multipart/form-data" id="hse-plan-edit-form" class="space-y-5">
        @csrf
        @method('PUT')

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Data Pengajuan</h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    @if(auth()->user()->role === 'staff')
                        Perubahan data akan mengembalikan HSE Plan ke Review Manager.
                    @else
                        Perubahan data akan memperbarui file PDF HSE Plan bila sudah disetujui.
                    @endif
                </p>
            </div>
            <div class="px-4 md:px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">1. Nomor PO</label>
                    <input type="text" name="no_po" class="form-input" value="{{ old('no_po', $hsePlan->no_po) }}" placeholder="Nomor purchase order">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">2. Deskripsi Pekerjaan <span class="text-red-500">*</span></label>
                    <textarea name="deskripsi_pekerjaan" rows="3" class="form-input" placeholder="Uraikan lingkup dan tahapan pekerjaan" required>{{ old('deskripsi_pekerjaan', $hsePlan->deskripsi_pekerjaan) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <p class="form-label mb-2">3. Lama Pekerjaan <span class="text-red-500">*</span></p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">a. Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-input" value="{{ old('tanggal_mulai', $hsePlan->tanggal_mulai?->format('Y-m-d')) }}" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-input" value="{{ old('tanggal_selesai', $hsePlan->tanggal_selesai?->format('Y-m-d')) }}" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">b. Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-input" value="{{ old('jam_mulai', $hsePlan->jam_mulai) }}" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-input" value="{{ old('jam_selesai', $hsePlan->jam_selesai) }}" required>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="form-label">4. Nama Kontraktor <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_kontraktor" class="form-input" value="{{ old('nama_kontraktor', $hsePlan->nama_kontraktor) }}" placeholder="Nama perusahaan pelaksana" required>
                </div>
                <div>
                    <label class="form-label">5. Nama Pimpinan Proyek <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pimpinan_proyek" class="form-input" value="{{ old('nama_pimpinan_proyek', $hsePlan->nama_pimpinan_proyek) }}" placeholder="Penanggung jawab proyek" required>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Informasi Pengajuan</h2>
            </div>
            <div class="px-4 md:px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Site <span class="text-red-500">*</span></label>
                    @if(auth()->user()->role === 'staff')
                        <input type="hidden" name="site" value="{{ $hsePlan->site }}">
                        <select class="form-input" disabled>
                            <option selected>Site {{ $hsePlan->site }}</option>
                        </select>
                    @else
                        <select name="site" class="form-input" required>
                            @foreach(\App\Models\HsePlan::SITES as $site)
                                <option value="{{ $site }}" @selected(old('site', $hsePlan->site) === $site)>Site {{ $site }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div>
                    <label class="form-label">Tipe Pekerjaan <span class="text-red-500">*</span></label>
                    <select name="tipe" class="form-input" required>
                        @foreach(['Internal', 'Eksternal'] as $tipe)
                            <option value="{{ $tipe }}" @selected(old('tipe', $hsePlan->tipe) === $tipe)>{{ $tipe }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Divisi / Unit Pengaju <span class="text-red-500">*</span></label>
                    <input type="text" name="divisi_pengaju" class="form-input" value="{{ old('divisi_pengaju', $hsePlan->divisi_pengaju) }}" placeholder="Contoh: Divisi Teknik" required>
                </div>
                <div>
                    <label class="form-label">Nomor Telepon <span class="text-red-500">*</span></label>
                    <input type="text" name="telepon" class="form-input" value="{{ old('telepon', $hsePlan->telepon) }}" placeholder="Nomor untuk cek status" required>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 md:px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-gray-800">6. Nama &amp; Tugas Setiap Personil</h2>
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

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">7. Jenis APD yang Digunakan</h2>
            </div>
            <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($apdOptions as $key => $label)
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-inka-navy/30 hover:bg-blue-50/30 cursor-pointer transition-colors">
                        <input type="checkbox" name="apd[]" value="{{ $key }}" @checked(in_array($key, $selectedApd, true))
                            class="w-4 h-4 rounded text-inka-navy border-gray-300 focus:ring-inka-navy">
                        <span class="text-sm text-gray-700">{{ $label }}</span>
                    </label>
                @endforeach
                <div class="col-span-full flex items-center gap-2 mt-1">
                    <span class="text-sm text-gray-500 shrink-0">Lainnya:</span>
                    <input type="text" name="apd_lainnya" class="form-input" value="{{ old('apd_lainnya', $hsePlan->apd_lainnya) }}" placeholder="Sebutkan APD tambahan...">
                </div>
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 md:px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-gray-800">8. Daftar Peralatan Kerja</h2>
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

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">9. Pernyataan K3LH &amp; Dokumen</h2>
                <p class="text-sm text-gray-400 mt-0.5">Dokumen baru akan ditambahkan; dokumen yang sudah ada tetap tersimpan.</p>
            </div>
            <div class="px-4 md:px-6 py-5 space-y-5">
                @if($hsePlan->documents->isNotEmpty())
                    <div class="space-y-1.5">
                        @foreach($hsePlan->documents as $document)
                            <a href="/admin/hse-plans/{{ $hsePlan->id }}/documents/{{ $document->id }}/view" target="_blank" rel="noopener"
                               class="flex items-center gap-2 text-sm text-gray-600 rounded-lg px-2 py-2 hover:bg-blue-50/40 transition-colors">
                                <svg class="w-4 h-4 text-inka-navy shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span class="truncate">{{ $document->nama_dokumen }}</span>
                                <span class="ml-auto text-xs font-semibold text-inka-navy shrink-0">Lihat</span>
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="border-t border-gray-100 pt-4">
                    <p class="text-sm font-semibold text-gray-700">Tambah dokumen pendukung</p>
                    <p class="text-xs text-gray-400 mt-0.5">Opsional. PDF, DOC, XLS, atau gambar. Maksimal 10MB per file.</p>
                    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Job Safety Analysis / JSA</label>
                            <input type="file" name="jsa_file" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        </div>
                        <div>
                            <label class="form-label">Sertifikat Keahlian Personil</label>
                            <input type="file" name="sertifikat_personil[]" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" multiple>
                        </div>
                        <div>
                            <label class="form-label">Sertifikat Peralatan</label>
                            <input type="file" name="sertifikat_peralatan[]" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" multiple>
                        </div>
                        <div>
                            <label class="form-label">MSDS Bahan B3</label>
                            <input type="file" name="msds_file" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        </div>
                        <div>
                            <label class="form-label">Rencana Pengelolaan Limbah B3</label>
                            <input type="file" name="limbah_b3_file" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        </div>
                        <div>
                            <label class="form-label">Prosedur / Instruksi Kerja</label>
                            <input type="file" name="prosedur_kerja_file" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        </div>
                        <div>
                            <label class="form-label">Rencana Pemantauan / Inspeksi</label>
                            <input type="file" name="inspeksi_file" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        </div>
                        <div>
                            <label class="form-label">Struktur Organisasi Proyek</label>
                            <input type="file" name="struktur_file" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>

                <div class="space-y-5 border-t border-gray-100 pt-4">
                    @foreach($pernyataan as $item)
                        @php
                            $answer = old($item['field'], $hsePlan->{$item['field']});
                            $isYes = filter_var($answer, FILTER_VALIDATE_BOOLEAN);
                        @endphp
                        <div>
                            <p class="text-sm font-semibold text-gray-700">{{ 10 + $loop->index }}. {{ $item['label'] }}</p>
                            <div class="mt-2 flex items-center gap-5">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="{{ $item['field'] }}" value="1" @checked($isYes)
                                        class="w-4 h-4 text-inka-navy border-gray-300 focus:ring-inka-navy">
                                    Ya
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="{{ $item['field'] }}" value="0" @checked(! $isYes)
                                        class="w-4 h-4 text-inka-navy border-gray-300 focus:ring-inka-navy">
                                    Tidak
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Pengesahan</h2>
                <p class="text-sm text-gray-400 mt-0.5">Tanda tangan Manager HSE diisi saat persetujuan.</p>
            </div>
            <div class="px-4 md:px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Dibuat oleh,<br>Kontraktor</p>
                    <div class="mt-3 w-full aspect-[2/1] rounded-xl border border-gray-200 bg-white flex items-center justify-center px-4 text-center overflow-hidden">
                        @if($hsePlan->tanda_tangan)
                            <img src="{{ $hsePlan->tanda_tangan }}" alt="Tanda tangan kontraktor" class="max-h-full max-w-full object-contain">
                        @else
                            <p class="text-xs text-gray-400">Tanda tangan kontraktor tidak tersedia.</p>
                        @endif
                    </div>
                    <p class="mt-2 text-sm font-semibold text-gray-700">({{ old('nama_pimpinan_proyek', $hsePlan->nama_pimpinan_proyek) ?: 'Pimpinan Proyek' }})</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700">Disetujui oleh,<br>Manager HSE</p>
                    <div class="mt-3 w-full aspect-[2/1] rounded-xl border {{ $hsePlan->manager_signature ? 'border-gray-200 bg-white' : 'border-dashed border-gray-200 bg-gray-50' }} flex items-center justify-center px-4 text-center overflow-hidden">
                        @if($hsePlan->manager_signature)
                            <img src="{{ $hsePlan->manager_signature }}" alt="Tanda tangan Manager HSE" class="max-h-full max-w-full object-contain">
                        @else
                            <p class="text-xs text-gray-400">Diisi oleh Manager HSE setelah HSE Plan disetujui.</p>
                        @endif
                    </div>
                    <p class="mt-2 text-sm font-semibold {{ $hsePlan->reviewer ? 'text-gray-700' : 'text-gray-400' }}">({{ $hsePlan->reviewer?->name ?? '..............................' }})</p>
                </div>
            </div>
        </section> -->
        
        {{-- TANDA TANGAN KONTRAKTOR - HANYA DITAMPILKAN --}}
        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">
                    Tanda Tangan Kontraktor
                </h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    Tanda tangan diberikan oleh kontraktor saat pengajuan HSE Plan dan tidak dapat diubah pada halaman Edit.
                </p>
            </div>

            <div class="px-4 md:px-6 py-5">
                <div class="w-full md:max-w-md">
                    <p class="text-sm font-semibold text-gray-700">
                        Dibuat oleh, Kontraktor
                    </p>

                    <div class="mt-3 w-full aspect-[2/1] rounded-xl border border-gray-200 bg-white flex items-center justify-center px-4 text-center overflow-hidden">
                        @if($hsePlan->tanda_tangan)
                            <img
                                src="{{ $hsePlan->tanda_tangan }}"
                                alt="Tanda tangan kontraktor"
                                class="max-h-full max-w-full object-contain"
                            >
                        @else
                            <p class="text-xs text-gray-400">
                                Tanda tangan kontraktor tidak tersedia.
                            </p>
                        @endif
                    </div>

                    <p class="mt-2 text-sm font-semibold text-gray-700">
                        ({{ $hsePlan->nama_pimpinan_proyek ?: 'Pimpinan Proyek' }})
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        Tanda tangan hanya dapat dilihat, bukan diedit.
                    </p>
                </div>
            </div>
        </section>

        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pb-8">
            <a href="/admin/hse-plans/{{ $hsePlan->id }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 text-center hover:bg-gray-50 transition-colors">Batal</a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-inka-navy text-white text-sm font-semibold hover:opacity-90 transition-opacity">Simpan Perubahan</button>
        </div>
    </form>

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
    </script>
</x-app-layout>
