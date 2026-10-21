<x-app-layout>
    <x-slot name="title">Edit Draft Permit</x-slot>

    @php
        $canChangeTipe = in_array($permit->status, ['Draft', 'Revision']);
    @endphp

    {{-- Step indicator --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-4 md:px-6 py-4 mb-6 overflow-x-auto">
        <div class="flex items-center justify-between gap-1 min-w-[620px]" id="step-indicator">
            @php
                $steps = [
                    0 => 'Site & Tipe',
                    1 => 'Klasifikasi & Info',
                    2 => 'Bahaya & Pencegahan',
                    3 => 'APD',
                    4 => 'Dokumen',
                    5 => 'Validasi Kerja',
                    6 => 'Review & Submit',
                ];
            @endphp
            @foreach($steps as $num => $label)
                <div class="flex items-center {{ $num < 6 ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center">
                        <div class="step-circle w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold border-2 transition-all duration-200
                            {{ $num === 0 ? 'bg-inka-navy text-white border-inka-navy' : 'bg-white text-gray-400 border-gray-200' }}"
                            id="step-circle-{{ $num }}">
                            {{ $num }}
                        </div>
                        <span class="text-xs mt-1 font-medium text-center leading-tight
                            {{ $num === 0 ? 'text-inka-navy' : 'text-gray-400' }}"
                            id="step-label-{{ $num }}">
                            {{ $label }}
                        </span>
                    </div>
                    @if($num < 6)
                        <div class="flex-1 h-px mx-2 mt-[-12px] step-line bg-gray-200 transition-colors duration-200" id="step-line-{{ $num }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Form --}}
    <form action="/divisi/permits/{{ $permit->id }}" method="POST" id="permit-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="idempotency_key" value="{{ Str::random(16) . '_' . time() }}">

        {{-- Error Alert --}}
        <div id="form-error-alert" class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 p-4 rounded-xl transition-opacity duration-300 hidden">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div id="form-error-message" class="text-sm font-medium text-red-800">
                Mohon lengkapi semua form yang wajib diisi (bertanda *) pada langkah ini.
            </div>
        </div>

        {{-- ========================================================
             STEP 0 — SITE & TIPE PERMIT
             ======================================================== --}}
        <div id="step-0" class="space-y-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-800">Site <span class="text-red-500">*</span></h3>
                    <p class="text-sm text-gray-400 mt-0.5">Permit akan direview oleh Staff HSE site yang dipilih</p>
                </div>
                <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach(['Madiun', 'Banyuwangi'] as $s)
                    <button type="button" onclick="selectSite('{{ $s }}')" id="btn-site-{{ strtolower($s) }}"
                        class="site-btn group p-6 rounded-2xl border-2 {{ ($permit->site ?? 'Madiun') === $s ? 'border-inka-navy bg-inka-navy/5' : 'border-gray-200' }} text-left transition-all">
                        <p class="text-lg font-bold text-gray-800">{{ $s }}</p>
                        <p class="text-sm text-gray-400 mt-1">Plant {{ $s }} — direview Staff HSE {{ $s }}</p>
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="site" id="site-input" value="{{ old('site', $permit->site ?? 'Madiun') }}">
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-800">Tipe Permit</h3>
                </div>
                <div class="px-4 md:px-6 py-5">
                    @if($canChangeTipe)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <button type="button" onclick="selectTipe('Internal')" id="btn-internal"
                                class="tipe-btn group p-6 rounded-2xl border-2 {{ $permit->tipe === 'Internal' ? 'border-inka-navy bg-inka-navy/5' : 'border-gray-200' }} text-left transition-all">
                                <div class="w-12 h-12 rounded-xl {{ $permit->tipe === 'Internal' ? 'bg-inka-navy/10' : 'bg-gray-100' }} flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 {{ $permit->tipe === 'Internal' ? 'text-inka-navy' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <p class="text-lg font-bold text-gray-800">Internal</p>
                                <p class="text-sm text-gray-400 mt-1">Pekerjaan internal perusahaan</p>
                            </button>
                            <button type="button" onclick="selectTipe('Eksternal')" id="btn-eksternal"
                                class="tipe-btn group p-6 rounded-2xl border-2 {{ $permit->tipe === 'Eksternal' ? 'border-inka-navy bg-inka-navy/5' : 'border-gray-200' }} text-left transition-all">
                                <div class="w-12 h-12 rounded-xl {{ $permit->tipe === 'Eksternal' ? 'bg-blue-50' : 'bg-gray-100' }} flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 {{ $permit->tipe === 'Eksternal' ? 'text-blue-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <p class="text-lg font-bold text-gray-800">Eksternal</p>
                                <p class="text-sm text-gray-400 mt-1">Pekerjaan oleh kontraktor eksternal</p>
                            </button>
                        </div>
                        <input type="hidden" name="tipe" id="tipe-input" value="{{ $permit->tipe }}">
                    @else
                        <div class="flex items-center gap-3">
                            <x-permit-tipe-badge :tipe="$permit->tipe" />
                            <span class="text-sm text-gray-400">Tipe tidak dapat diubah setelah submit.</span>
                        </div>
                        <input type="hidden" name="tipe" value="{{ $permit->tipe }}">
                    @endif
                </div>
            </div>
        </div>

        {{-- ========================================================
             STEP 4 — DOKUMEN PENDUKUNG
             ======================================================== --}}
        <div id="step-4" class="hidden space-y-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-800 mt-0.5">Dokumen Pendukung</h3>
                    <p class="text-sm text-gray-400 mt-0.5">Opsional untuk permit Internal, wajib untuk Eksternal (max 10MB per file)</p>
                </div>
                <div class="px-4 md:px-6 py-5">
                    {{-- Existing documents --}}
                    @if($permit->documents->count())
                        <div class="space-y-3 mb-4">
                            @foreach($permit->documents as $doc)
                            <div class="existing-doc flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-700">{{ $doc->nama_dokumen }}</p>
                                        <p class="text-xs text-gray-400">{{ strtoupper($doc->file_type) }} • {{ round($doc->file_size / 1024) }} KB</p>
                                    </div>
                                </div>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="hapus_dokumen[]" value="{{ $doc->id }}" class="rounded text-red-500 border-gray-300">
                                    <span class="text-xs text-red-500">Hapus</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    @endif

                    <div id="documents-list" class="space-y-4"></div>
                    <button type="button" onclick="addDocument()" id="btn-add-doc"
                        class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-inka-navy border border-inka-navy rounded-xl hover:bg-inka-navy/5 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Dokumen
                    </button>
                    <p id="doc-required-warning" class="text-sm text-red-500 mt-3 hidden">Minimal upload 1 dokumen pendukung.</p>
                </div>
            </div>
        </div>

        {{-- ========================================================
             STEP 1 — A. KLASIFIKASI PEKERJAAN + B. INFORMASI PEKERJAAN
             ======================================================== --}}
        <div id="step-1" class="space-y-5 hidden">

            {{-- A. Klasifikasi Pekerjaan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">A</span>
                    <h3 class="text-base font-semibold text-gray-800 mt-0.5">Klasifikasi Pekerjaan</h3>
                    <p class="text-sm text-gray-400 mt-0.5">Pilih satu atau lebih jenis pekerjaan berisiko tinggi</p>
                </div>
                <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @php
                        $dbKlasifikasi = \App\Models\Classification::all();
                        if ($dbKlasifikasi->isNotEmpty()) {
                            $klasifikasi = $dbKlasifikasi->pluck('name', 'code')->toArray();
                        } else {
                            $klasifikasi = [
                                'panas'          => 'Pekerjaan Panas',
                                'ketinggian'     => 'Pekerjaan Ketinggian',
                                'ruang_terbatas' => 'Ruang Terbatas',
                                'galian'         => 'Pekerjaan Galian',
                                'tegangan_tinggi'=> 'Pekerjaan Tegangan Tinggi',
                                'radiasi'        => 'Radiasi',
                            ];
                        }
                        $selectedKlasifikasi = old('klasifikasi_pekerjaan', $permit->klasifikasi_pekerjaan ?? []);
                    @endphp
                    @foreach($klasifikasi as $key => $label)
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-inka-navy/30 hover:bg-blue-50/30 cursor-pointer transition-colors">
                            <input type="checkbox" name="klasifikasi_pekerjaan[]" value="{{ $key }}"
                                {{ in_array($key, $selectedKlasifikasi) ? 'checked' : '' }}
                                class="w-4 h-4 rounded text-inka-navy border-gray-300 focus:ring-inka-navy">
                            <span class="text-base text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- B. Informasi Pekerjaan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">B</span>
                    <h3 class="text-base font-semibold text-gray-800 mt-0.5">Informasi Pekerjaan</h3>
                </div>
                <div class="px-4 md:px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="form-label">Pekerjaan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pekerjaan" class="form-input" placeholder="Contoh: Perbaikan Atap Gudang B"
                            value="{{ old('nama_pekerjaan', $permit->nama_pekerjaan) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" name="lokasi" class="form-input" placeholder="Contoh: Gudang B, Area Produksi"
                            value="{{ old('lokasi', $permit->lokasi) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Perusahaan <span class="text-red-500">*</span></label>
                        <input type="text" name="kontraktor" class="form-input" placeholder="Nama perusahaan kontraktor"
                            value="{{ old('kontraktor', $permit->kontraktor) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Manager / Penanggung Jawab</label>
                        <input type="text" name="penanggung_jawab" class="form-input" placeholder="Nama PIC"
                            value="{{ old('penanggung_jawab', $permit->penanggung_jawab) }}">
                    </div>
                    <div>
                        <label class="form-label">No. Telpon</label>
                        <input type="text" name="telepon" class="form-input" placeholder="08xxxxxxxxxx"
                            value="{{ old('telepon', $permit->telepon) }}">
                    </div>
                    <div>
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-input"
                            value="{{ old('tanggal_mulai', $permit->tanggal_mulai?->format('Y-m-d')) }}">
                    </div>
                    <div>
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-input"
                            value="{{ old('tanggal_selesai', $permit->tanggal_selesai?->format('Y-m-d')) }}">
                    </div>
                </div>

                {{-- Daftar Pekerja --}}
                <div class="px-4 md:px-6 pb-5 border-t border-gray-100 pt-4">
                    <p class="text-sm font-semibold text-gray-600 mb-3">Daftar Pekerja</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                        @php
                            $pekerja = ['Engineer','Operator Alat Berat','Teknisi Listrik','Mekanik','Welder','Operator','Tukang Bangunan','Tukang Kayu','Helper'];
                            $existingPekerja = old('daftar_pekerja', $permit->daftar_pekerja ?? []);
                        @endphp
                        @foreach($pekerja as $p)
                            @php $key = strtolower(str_replace(' ', '_', $p)); @endphp
                            <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2 border border-gray-100">
                                <span class="text-sm text-gray-600 flex-1">{{ $p }}</span>
                                <input type="number" name="daftar_pekerja[{{ $key }}]" min="0"
                                    value="{{ old('daftar_pekerja.'.$key, $existingPekerja[$key] ?? 0) }}"
                                    class="w-16 text-center text-base font-semibold border border-gray-200 rounded-lg py-0.5 px-1 focus:ring-1 focus:ring-inka-navy focus:border-inka-navy">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Peralatan Kerja --}}
                <div class="px-4 md:px-6 pb-5 border-t border-gray-100 pt-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-semibold text-gray-600">Peralatan Kerja & Material</p>
                        <button type="button" onclick="addPeralatan()" class="text-sm font-semibold text-inka-navy hover:underline">+ Tambah Baris</button>
                    </div>
                    <div class="hidden sm:grid grid-cols-4 gap-2 mb-1.5">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide px-1">Peralatan Kerja</span>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide px-1">Jumlah</span>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide px-1">Material</span>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide px-1">Jumlah</span>
                    </div>
                    <div id="peralatan-list" class="space-y-2">
                        @php
                            $existingPeralatan = old('peralatan_kerja', $permit->peralatan_kerja ?? []);
                        @endphp
                        @if(count($existingPeralatan) > 0)
                            @foreach($existingPeralatan as $idx => $item)
                                <div class="peralatan-row grid grid-cols-2 lg:grid-cols-4 gap-2 items-center">
                                    <input type="text" name="peralatan_kerja[{{ $idx }}][alat]" placeholder="e.g. Gerinda" class="form-input text-base"
                                        value="{{ $item['alat'] ?? '' }}">
                                    <input type="text" name="peralatan_kerja[{{ $idx }}][jumlah_alat]" placeholder="1" class="form-input text-base"
                                        value="{{ $item['jumlah_alat'] ?? '' }}">
                                    <input type="text" name="peralatan_kerja[{{ $idx }}][material]" placeholder="e.g. Baja" class="form-input text-base"
                                        value="{{ $item['material'] ?? '' }}">
                                    <input type="text" name="peralatan_kerja[{{ $idx }}][jumlah_material]" placeholder="5 kg" class="form-input text-base"
                                        value="{{ $item['jumlah_material'] ?? '' }}">
                                </div>
                            @endforeach
                        @else
                            <div class="peralatan-row grid grid-cols-2 lg:grid-cols-4 gap-2 items-center">
                                <input type="text" name="peralatan_kerja[0][alat]" placeholder="e.g. Gerinda" class="form-input text-base">
                                <input type="text" name="peralatan_kerja[0][jumlah_alat]" placeholder="1" class="form-input text-base">
                                <input type="text" name="peralatan_kerja[0][material]" placeholder="e.g. Baja" class="form-input text-base">
                                <input type="text" name="peralatan_kerja[0][jumlah_material]" placeholder="5 kg" class="form-input text-base">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================
             STEP 2 — C. BAHAYA PEKERJAAN + D. TINDAKAN PENCEGAHAN
             ======================================================== --}}
        <div id="step-2" class="space-y-5 hidden">

            {{-- C. Bahaya Pekerjaan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">C</span>
                    <h3 class="text-base font-semibold text-gray-800 mt-0.5">Bahaya Pekerjaan</h3>
                </div>
                <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @php
                        $bahaya = [
                            'percikan_panas'       => 'Percikan Panas',
                            'bahaya_kebakaran'     => 'Bahaya Kebakaran',
                            'cidera_tulang'        => 'Cidera Tulang Belakang',
                            'pencemaran_lingk'     => 'Pencemaran Lingk.',
                            'terpukul_torbentur'   => 'Terpukul / Torbentur',
                            'penerangan_kurang'    => 'Penerangan Kurang',
                            'bahaya_makhluk_hidup' => 'Bahaya Makhluk Hidup',
                            'jatuh_ketinggian'     => 'Jatuh Dari Ketinggian',
                            'lantai_licin'         => 'Lantai Licin',
                            'tangga_penyangga'     => 'Tangga / Penyangga Tidak Kokoh / Patah',
                            'bising'               => 'Bising',
                            'menghasilkan_debu'    => 'Menghasilkan Debu',
                            'bahaya_angin'         => 'Bahaya Angin',
                            'keracunan_gas'        => 'Keracunan Gas',
                            'peledakan'            => 'Peledakan',
                            'bahaya_aliran_listrik'=> 'Bahaya Alat / Aliran Listrik',
                            'bahaya_getaran'       => 'Bahaya Getaran',
                            'bahaya_zat_kimia'     => 'Bahaya Zat Kimia',
                            'terpotong_tertusuk'   => 'Terpotong / Tertusuk',
                            'terperosok'           => 'Terperosok',
                            'tertimpa_tertimpa'    => 'Tertimbun / Tertimpa',
                            'mata_terkena'         => 'Mata Kemasukan Benda',
                            'tertabrak'            => 'Tertabrak / Tabrakan',
                            'limbah_b3'            => 'Limbah B3',
                            'bahaya_radiasi'       => 'Bahaya Radiasi',
                        ];
                        $selectedBahaya = old('bahaya_pekerjaan', $permit->bahaya_pekerjaan ?? []);
                    @endphp
                    @foreach($bahaya as $key => $label)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="bahaya_pekerjaan[]" value="{{ $key }}"
                                {{ in_array($key, $selectedBahaya) ? 'checked' : '' }}
                                class="w-4 h-4 rounded text-inka-navy border-gray-300 focus:ring-inka-navy">
                            <span class="text-base text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                    <div class="col-span-full flex items-center gap-2 mt-1">
                        <span class="text-base text-gray-500 shrink-0">Lainnya:</span>
                        <input type="text" name="bahaya_lainnya" class="form-input text-base" placeholder="Sebutkan..."
                            value="{{ old('bahaya_lainnya', $permit->bahaya_lainnya) }}">
                    </div>
                </div>
            </div>

            {{-- D. Tindakan Pencegahan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">D</span>
                    <h3 class="text-base font-semibold text-gray-800 mt-0.5">Tindakan Pencegahan Bahaya</h3>
                </div>
                <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @php
                        $pencegahan = [
                            'proteksi_dari_jatuh'   => 'Proteksi Dari Jatuh',
                            'media_penghalang_api'  => 'Media Penghambat Api / Percikan',
                            'pintu_masuk_keluar'    => 'Pintu Masuk / Keluar',
                            'safety_briefing'       => 'Safety Brifing',
                            'tangga_penyangga_kuat' => 'Tangga / Penyangga Yang Kokoh',
                            'rambu_rambu'           => 'Rambu-Rambu',
                            'jalur_evakuasi'        => 'Jalur Evakuasi',
                            'penyediaan_pemadam'    => 'Penyediaan Pemadam Api',
                            'barikade_polisi'       => 'Barikade / Pagar / Police Line',
                            'sertifikat_kompetensi' => 'Sertifikat Kompetensi',
                        ];
                        $selectedPencegahan = old('tindakan_pencegahan', $permit->tindakan_pencegahan ?? []);
                    @endphp
                    @foreach($pencegahan as $key => $label)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="tindakan_pencegahan[]" value="{{ $key }}"
                                {{ in_array($key, $selectedPencegahan) ? 'checked' : '' }}
                                class="w-4 h-4 rounded text-inka-navy border-gray-300 focus:ring-inka-navy">
                            <span class="text-base text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                    <div class="col-span-full flex items-center gap-2 mt-1">
                        <span class="text-base text-gray-500 shrink-0">Lainnya:</span>
                        <input type="text" name="pencegahan_lainnya" class="form-input text-base" placeholder="Sebutkan..."
                            value="{{ old('pencegahan_lainnya', $permit->pencegahan_lainnya) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================
             STEP 3 — E. ALAT PELINDUNG DIRI (APD)
             ======================================================== --}}
        <div id="step-3" class="hidden">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">E</span>
                    <h3 class="text-base font-semibold text-gray-800 mt-0.5">Alat Pelindung Diri (APD)</h3>
                    <p class="text-sm text-gray-400 mt-0.5">Pilih APD yang akan digunakan selama pekerjaan berlangsung</p>
                </div>
                <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @php
                        $apd = [
                            'helm'               => 'Helm Keselamatan',
                            'kaca_mata'          => 'Kaca Mata Keselamatan',
                            'sarung_tangan'      => 'Sarung Tangan Kulit/Kaos/Karet',
                            'baju_pelindung'     => 'Baju Pelindung',
                            'sepatu'             => 'Sepatu Keselamatan',
                            'kaca_mata_debu'     => 'Kaca Mata Debu',
                            'rompi'              => 'Rompi Keselamatan',
                            'ear_plug'           => 'Ear Plug / Ear Muff',
                            'tali_sabuk'         => 'Tali / Sabuk Keselamatan',
                            'pelindung_muka'     => 'Pelindung Muka',
                            'masker'             => 'Masker / Respirator',
                        ];
                        $selectedApd = old('apd', $permit->apd ?? []);
                    @endphp
                    @foreach($apd as $key => $label)
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-inka-navy/30 hover:bg-blue-50/30 cursor-pointer transition-colors">
                            <input type="checkbox" name="apd[]" value="{{ $key }}"
                                {{ in_array($key, $selectedApd) ? 'checked' : '' }}
                                class="w-4 h-4 rounded text-inka-navy border-gray-300 focus:ring-inka-navy">
                            <span class="text-base text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                    <div class="col-span-full flex items-center gap-2 mt-1">
                        <span class="text-base text-gray-500 shrink-0">Lainnya:</span>
                        <input type="text" name="apd_lainnya" class="form-input text-base" placeholder="Sebutkan APD tambahan..."
                            value="{{ old('apd_lainnya', $permit->apd_lainnya) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================
             STEP 5 — F. VALIDASI KERJA (TANDA TANGAN)
             ======================================================== --}}
        <div id="step-5" class="hidden">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">F</span>
                    <h3 class="text-base font-semibold text-gray-800 mt-0.5">Validasi Kerja</h3>
                    <p class="text-sm text-gray-400 mt-0.5">Izin diberikan sesuai pengajuan dan kondisi di atas</p>
                </div>
                <div class="px-4 md:px-6 py-5 space-y-4">
                    <div class="pt-2">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="text-base font-semibold text-gray-700">Tanda Tangan Pemohon <span class="text-red-500">*</span></p>
                                <p class="text-sm text-gray-400">Tanda tangan pihak kontraktor. Wajib diisi.</p>
                            </div>
                            <button type="button" onclick="clearSignature()"
                                class="text-sm font-semibold text-gray-400 hover:text-red-500 border border-gray-200 hover:border-red-300 px-3 py-1.5 rounded-lg transition-colors">
                                ✕ Hapus
                            </button>
                        </div>

                        <div id="signature-container"
                            class="relative border-2 border-dashed border-gray-300 rounded-xl overflow-hidden bg-white hover:border-inka-navy/40 transition-colors cursor-crosshair w-full max-w-[300px] aspect-square mx-auto">
                            <canvas id="signature-canvas"
                                class="block w-full h-full"
                                style="touch-action: none;"
                            ></canvas>
                            <span id="signature-placeholder"
                                class="absolute inset-0 flex items-center justify-center text-sm text-gray-300 pointer-events-none select-none">
                                Tanda tangan di sini dengan mouse atau jari
                            </span>
                        </div>

                        <input type="hidden" name="tanda_tangan" id="tanda-tangan-input"
                            value="{{ old('tanda_tangan', '') }}">
                        <p id="signature-error" class="text-sm text-red-500 mt-1.5 hidden">Tanda tangan wajib diisi sebelum melanjutkan.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================
             STEP 6 — REVIEW & SUBMIT
             ======================================================== --}}
        <div id="step-6" class="hidden">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-800">Review Pengajuan</h3>
                    <p class="text-sm text-gray-400 mt-0.5">Periksa kembali seluruh data sebelum menyimpan atau mengajukan permit</p>
                </div>
                <div class="px-4 md:px-6 py-5">
                    <div id="review-content" class="space-y-4 text-base text-gray-600">
                        <p class="text-sm text-gray-400 italic">Data review akan ditampilkan di sini setelah Anda mengisi form sebelumnya.</p>
                    </div>
                </div>
            </div>

            {{-- Tombol aksi utama --}}
            <div class="mt-5 flex flex-col sm:flex-row gap-3 justify-end">
                <button type="submit" name="action" value="draft" id="btn-draft"
                    class="px-6 py-3 rounded-xl border border-inka-navy text-inka-navy text-base font-semibold hover:bg-inka-navy/5 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="btn-text">Simpan sebagai Draft</span>
                    <span class="btn-loading hidden flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        Memproses...
                    </span>
                </button>
                <button type="submit" name="action" value="submit" id="btn-submit"
                    class="px-6 py-3 rounded-xl bg-inka-navy text-white text-base font-semibold hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="btn-text">Submit Permit</span>
                    <span class="btn-loading hidden flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        Mengirim...
                    </span>
                </button>
            </div>
        </div>

        {{-- Navigasi Step --}}
        <div class="mt-6 flex items-center justify-between" id="step-nav">
            <button type="button" id="btn-prev" onclick="changeStep(-1)"
                class="hidden px-5 py-2.5 rounded-xl border border-gray-200 text-base font-semibold text-gray-600 hover:border-gray-300 transition-colors">
                Kembali
            </button>
            <div></div>
            <button type="button" id="btn-next" onclick="changeStep(1)"
                class="px-5 py-2.5 rounded-xl bg-inka-navy text-white text-base font-semibold hover:opacity-90 transition-opacity">
                Selanjutnya
            </button>
        </div>

    </form>

    <style>
        .form-label { display: block; font-size: 0.875rem; font-weight: 600; color: #4b5563; margin-bottom: 6px; }
        .form-input {
            width: 100%; padding: 9px 12px; border: 1px solid #e5e7eb; border-radius: 10px;
            font-size: 1rem; color: #1f2937; background: white;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-input:focus { outline: none; border-color: #111d33; box-shadow: 0 0 0 3px rgba(17,29,51,0.08); }
        .form-input.error { border-color: #fca5a5; }
        .form-input.error:focus { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.15); }
    </style>

    <script>
        let currentStep = 0;
        const totalSteps = 7;
        let selectedTipe = '{{ $permit->tipe }}';

        function selectTipe(tipe) {
            selectedTipe = tipe;
            document.getElementById('tipe-input').value = tipe;

            document.querySelectorAll('.tipe-btn').forEach(btn => {
                btn.classList.remove('border-inka-navy', 'bg-inka-navy/5');
                btn.classList.add('border-gray-200');
            });
            const activeBtn = document.getElementById('btn-' + tipe.toLowerCase());
            activeBtn.classList.add('border-inka-navy', 'bg-inka-navy/5');
            activeBtn.classList.remove('border-gray-200');
        }

        let selectedSite = document.getElementById('site-input').value || 'Madiun';

        function selectSite(site) {
            selectedSite = site;
            document.getElementById('site-input').value = site;

            document.querySelectorAll('.site-btn').forEach(btn => {
                btn.classList.remove('border-inka-navy', 'bg-inka-navy/5');
                btn.classList.add('border-gray-200');
            });
            const activeSiteBtn = document.getElementById('btn-site-' + site.toLowerCase());
            activeSiteBtn.classList.add('border-inka-navy', 'bg-inka-navy/5');
            activeSiteBtn.classList.remove('border-gray-200');
        }
        selectSite(selectedSite);

        function changeStep(direction) {
            let nextStep = currentStep + direction;
            if (nextStep < 0 || nextStep >= totalSteps) return;

            // Validation before moving forward
            if (direction > 0) {
                const stepEl = document.getElementById('step-' + currentStep);
                const inputs = stepEl.querySelectorAll('input[required], select[required], textarea[required]');
                let valid = true;

                inputs.forEach(i => i.setCustomValidity(''));

                for (const input of inputs) {
                    if (!input.checkValidity()) {
                        let labelName = 'bidang ini';
                        const parent = input.closest('div');
                        if (parent) {
                            const labelEl = parent.querySelector('.form-label');
                            if (labelEl) {
                                labelName = labelEl.innerText.replace('*', '').trim();
                            }
                        }
                        input.setCustomValidity('Harap isi ' + labelName);
                        input.reportValidity();
                        valid = false;
                        break;
                    }
                }
                if (!valid) {
                    const errorAlert = document.getElementById('form-error-alert');
                    errorAlert.classList.remove('hidden');
                    errorAlert.style.opacity = '1';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }

                // Validate document count for Eksternal when leaving step 4
                if (currentStep === 4 && selectedTipe === 'Eksternal') {
                    const existingDocs = document.querySelectorAll('.existing-doc').length;
                    const newDocs = document.querySelectorAll('.doc-entry').length;
                    if (existingDocs === 0 && newDocs === 0) {
                        document.getElementById('doc-required-warning').classList.remove('hidden');
                        return;
                    }
                }

                // Validate signature when leaving step 5
                if (currentStep === 5) {
                    if (!window._signatureHasDrawn || !window._signatureHasDrawn()) {
                        document.getElementById('signature-error').classList.remove('hidden');
                        document.getElementById('signature-canvas').scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }
                }
            }

            // Hide current
            document.getElementById('step-' + currentStep).classList.add('hidden');

            // Update step indicator
            updateStepIndicator(currentStep, nextStep);

            currentStep = nextStep;

            // Show next
            document.getElementById('step-' + currentStep).classList.remove('hidden');

            // Resize canvas if we just entered step 5
            if (currentStep === 5 && typeof window._resizeSignature === 'function') {
                window._resizeSignature();
            }

            // Update buttons
            document.getElementById('btn-prev').classList.toggle('hidden', currentStep === 0);
            document.getElementById('btn-next').classList.toggle('hidden', currentStep === totalSteps - 1);

            // Build review on step 6
            if (currentStep === totalSteps - 1) buildReview();

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function updateStepIndicator(from, to) {
            document.getElementById('step-circle-' + from).className =
                'step-circle w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 bg-green-500 text-white border-green-500';
            document.getElementById('step-label-' + from).className =
                'text-xs mt-1 font-medium text-center leading-tight text-green-600';

            document.getElementById('step-circle-' + to).className =
                'step-circle w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 bg-inka-navy text-white border-inka-navy';
            document.getElementById('step-label-' + to).className =
                'text-xs mt-1 font-medium text-center leading-tight text-inka-navy';

            if (from < to && document.getElementById('step-line-' + from)) {
                document.getElementById('step-line-' + from).classList.add('bg-green-400');
                document.getElementById('step-line-' + from).classList.remove('bg-gray-200');
            }
        }

        function buildReview() {
            const form = document.getElementById('permit-form');
            const fd = new FormData(form);
            let html = '';

            html += `<div class="p-3 bg-gray-50 rounded-xl mb-4">
                <p class="text-sm text-gray-400">Tipe Permit</p>
                <p class="font-semibold text-gray-800">${selectedTipe}</p>
            </div>`;

            html += `<div class="p-3 bg-gray-50 rounded-xl mb-4">
                <p class="text-sm text-gray-400">Site</p>
                <p class="font-semibold text-gray-800">${selectedSite}</p>
            </div>`;

            const namaP = fd.get('nama_pekerjaan') || '—';
            const lokasi = fd.get('lokasi') || '—';
            const kontra = fd.get('kontraktor') || '—';
            const pic    = fd.get('penanggung_jawab') || '—';
            const telp   = fd.get('telepon') || '—';
            const tgl1   = fd.get('tanggal_mulai') || '—';
            const tgl2   = fd.get('tanggal_selesai') || '—';

            html += `<div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-base">
                <div><p class="text-sm text-gray-400">Nama Pekerjaan</p><p class="font-medium text-gray-800">${namaP}</p></div>
                <div><p class="text-sm text-gray-400">Lokasi</p><p class="font-medium text-gray-800">${lokasi}</p></div>
                <div><p class="text-sm text-gray-400">Kontraktor</p><p class="font-medium text-gray-800">${kontra}</p></div>
                <div><p class="text-sm text-gray-400">Penanggung Jawab</p><p class="font-medium text-gray-800">${pic}</p></div>
                <div><p class="text-sm text-gray-400">Telepon</p><p class="font-medium text-gray-800">${telp}</p></div>
                <div><p class="text-sm text-gray-400">Tanggal</p><p class="font-medium text-gray-800">${tgl1} s/d ${tgl2}</p></div>
            </div>`;

            const bahaya = fd.getAll('bahaya_pekerjaan[]');
            if (bahaya.length) {
                html += `<div class="border-t border-gray-100 pt-4"><p class="text-sm text-gray-400 mb-1">Bahaya Pekerjaan (${bahaya.length} dipilih)</p>
                    <p class="font-medium text-gray-800">${bahaya.join(', ')}</p></div>`;
            }

            const apd = fd.getAll('apd[]');
            if (apd.length) {
                html += `<div class="border-t border-gray-100 pt-4"><p class="text-sm text-gray-400 mb-1">APD (${apd.length} dipilih)</p>
                    <p class="font-medium text-gray-800">${apd.join(', ')}</p></div>`;
            }

            document.getElementById('review-content').innerHTML = html;
        }

        // ===== DOKUMEN PENDUKUNG =====
        let docCount = 0;
        function addDocument() {
            const list = document.getElementById('documents-list');
            const idx = docCount;
            const html = `
                <div class="doc-entry p-4 border border-gray-100 rounded-xl space-y-3" id="doc-${idx}">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-600">Dokumen Baru ${idx + 1}</p>
                        <button type="button" onclick="removeDocument(${idx})" class="text-sm text-red-500 hover:underline">Hapus</button>
                    </div>
                    <div>
                        <label class="form-label">Nama Dokumen <span class="text-red-500">*</span></label>
                        <input type="text" name="dokumen[${idx}][nama]" class="form-input" placeholder="Contoh: HIRADC" required>
                    </div>
                    <div>
                        <label class="form-label">Deskripsi</label>
                        <textarea name="dokumen[${idx}][deskripsi]" class="form-input" rows="2" placeholder="Deskripsi singkat dokumen..."></textarea>
                    </div>
                    <div>
                        <label class="form-label">File <span class="text-red-500">*</span></label>
                        <input type="file" name="dokumen[${idx}][file]" class="form-input" required>
                        <p class="text-xs text-gray-400 mt-1">Maks 10MB. Format: PDF, gambar, Office, dll.</p>
                    </div>
                </div>
            `;
            list.insertAdjacentHTML('beforeend', html);
            docCount++;
            document.getElementById('doc-required-warning').classList.add('hidden');
        }

        function removeDocument(idx) {
            const el = document.getElementById('doc-' + idx);
            if (el) el.remove();
            const existingDocs = document.querySelectorAll('.existing-doc').length;
            const newDocs = document.querySelectorAll('.doc-entry').length;
            if (existingDocs === 0 && newDocs === 0) {
                document.getElementById('doc-required-warning').classList.remove('hidden');
            }
        }

        // ===== TANDA TANGAN DIGITAL (CANVAS) =====
        (function () {
            const canvas = document.getElementById('signature-canvas');
            const placeholder = document.getElementById('signature-placeholder');
            const hiddenInput = document.getElementById('tanda-tangan-input');
            const container = document.getElementById('signature-container');

            const ctx = canvas.getContext('2d');

            function resizeCanvas() {
                const rect = container.getBoundingClientRect();
                if (rect.width === 0) return;

                const rawDpr = window.devicePixelRatio || 1;
                const dpr = window.matchMedia('(max-width: 767px)').matches ? Math.min(rawDpr, 1.5) : rawDpr;
                canvas.width  = rect.width  * dpr;
                canvas.height = rect.height * dpr;
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

                // Set white background
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, rect.width, rect.height);

                ctx.strokeStyle = '#111d33';
                ctx.lineWidth   = 2;
                ctx.lineCap     = 'round';
                ctx.lineJoin    = 'round';
            }
            window.addEventListener('resize', resizeCanvas);
            window._resizeSignature = resizeCanvas;

            let drawing = false;
            let hasDrawn = false;

            function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                if (e.touches) {
                    return {
                        x: e.touches[0].clientX - rect.left,
                        y: e.touches[0].clientY - rect.top,
                    };
                }
                return { x: e.clientX - rect.left, y: e.clientY - rect.top };
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
                    container.classList.remove('border-dashed', 'border-gray-300');
                    container.classList.add('border-solid', 'border-inka-navy/30');
                    document.getElementById('signature-error').classList.add('hidden');
                }
            }

            function stopDraw() {
                if (!drawing) return;
                drawing = false;
                ctx.beginPath();
                hiddenInput.value = window.matchMedia('(max-width: 767px)').matches ? canvas.toDataURL('image/jpeg', 0.7) : canvas.toDataURL('image/png');
            }

            canvas.addEventListener('mousedown',  startDraw);
            canvas.addEventListener('mousemove',  draw);
            canvas.addEventListener('mouseup',    stopDraw);
            canvas.addEventListener('mouseleave', stopDraw);
            canvas.addEventListener('touchstart', startDraw, { passive: false });
            canvas.addEventListener('touchmove',  draw,      { passive: false });
            canvas.addEventListener('touchend',   stopDraw);

            window.clearSignature = function () {
                const dpr = window.devicePixelRatio || 1;
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width / dpr, canvas.height / dpr);
                hasDrawn = false;
                hiddenInput.value = '';
                placeholder.classList.remove('hidden');
                container.classList.add('border-dashed', 'border-gray-300');
                container.classList.remove('border-solid', 'border-inka-navy/30');
            };

            window._signatureHasDrawn = function () { return hasDrawn; };
        })();

        let peralatanCount = {{ count(old('peralatan_kerja', $permit->peralatan_kerja ?? [])) ?: 1 }};
        function addPeralatan() {
            const list = document.getElementById('peralatan-list');
            const row = document.createElement('div');
            row.className = 'peralatan-row grid grid-cols-2 lg:grid-cols-4 gap-2 items-center';
            row.innerHTML = `
                <input type="text" name="peralatan_kerja[${peralatanCount}][alat]" placeholder="mis. Gerinda" class="form-input text-base">
                <input type="text" name="peralatan_kerja[${peralatanCount}][jumlah_alat]" placeholder="1" class="form-input text-base">
                <input type="text" name="peralatan_kerja[${peralatanCount}][material]" placeholder="mis. Baja" class="form-input text-base">
                <input type="text" name="peralatan_kerja[${peralatanCount}][jumlah_material]" placeholder="5 kg" class="form-input text-base">
            `;
            list.appendChild(row);
            peralatanCount++;
        }
    </script>

    <script>
        // Track clicked action and prevent double-submit
        (function () {
            let activeAction = 'draft';

            const draftBtn = document.getElementById('btn-draft');
            const submitBtn = document.getElementById('btn-submit');

            if (draftBtn) {
                draftBtn.addEventListener('click', function () { activeAction = 'draft'; });
            }
            if (submitBtn) {
                submitBtn.addEventListener('click', function () { activeAction = 'submit'; });
            }

            const form = document.getElementById('permit-form');
            if (form) {
                form.addEventListener('submit', function (e) {
                    if (form.dataset.submitting === 'true') {
                        e.preventDefault();
                        return false;
                    }
                    form.dataset.submitting = 'true';

                    // Ensure action hidden input exists and receives the intended action value
                    let actionInput = form.querySelector('input[name="action"]');
                    if (!actionInput) {
                        actionInput = document.createElement('input');
                        actionInput.type = 'hidden';
                        actionInput.name = 'action';
                        form.appendChild(actionInput);
                    }

                    if (e.submitter && e.submitter.name === 'action' && e.submitter.value) {
                        actionInput.value = e.submitter.value;
                    } else {
                        actionInput.value = activeAction;
                    }

                    if (draftBtn) {
                        draftBtn.disabled = true;
                        draftBtn.querySelector('.btn-text')?.classList.add('hidden');
                        draftBtn.querySelector('.btn-loading')?.classList.remove('hidden');
                    }
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.querySelector('.btn-text')?.classList.add('hidden');
                        submitBtn.querySelector('.btn-loading')?.classList.remove('hidden');
                    }
                });
            }
        })();
    </script>
</x-app-layout>
