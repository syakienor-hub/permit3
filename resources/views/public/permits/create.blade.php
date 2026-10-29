<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Safety Permit — PT INKA (Persero)</title>
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
            font-size: 1rem; color: #1f2937; background: white;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-input:focus { outline: none; border-color: #111d33; box-shadow: 0 0 0 3px rgba(17,29,51,0.08); }
        /* HP saja: step tersembunyi tidak di-render (hemat memori/CPU). Laptop tidak berubah. */
        @media (max-width: 767px) {
            #step-0.hidden, #step-1.hidden, #step-2.hidden, #step-3.hidden,
            #step-4.hidden, #step-5.hidden, #step-6.hidden {
                content-visibility: hidden;
                contain-intrinsic-size: 0 500px;
            }
            .form-input { transition: none; }
            .form-input:focus { box-shadow: none; }
            header { backdrop-filter: none; -webkit-backdrop-filter: none; }
        }
    </style>
</head>
<body class="bg-slate-50 text-gray-900 min-h-screen">

    {{-- Header publik --}}
    <header class="bg-white border-b border-gray-100 sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 md:px-6 h-16 flex items-center gap-2.5 sm:gap-3">
            <a href="/" class="flex items-center gap-2.5 mr-auto min-w-0">
                <img src="{{ asset('assets/images/logoinka.svg') }}" alt="INKA" class="h-6 sm:h-7 w-auto shrink-0">
                <div class="border-l border-gray-200 pl-2.5 sm:pl-3 whitespace-nowrap">
                    <span class="text-[13px] sm:text-sm font-bold">Safety Permit</span>
                    <span class="block text-[9px] sm:text-[10px] font-semibold text-gray-500 tracking-wide">PT INKA (Persero)</span>
                </div>
            </a>
            <a href="/" class="text-sm font-semibold text-gray-500 hover:text-gray-800 transition-colors whitespace-nowrap">← Beranda</a>
            <a href="{{ route('login') }}" class="text-sm font-semibold text-white bg-inka-navy px-3 sm:px-4 py-2 rounded-xl hover:opacity-90 transition-opacity whitespace-nowrap">Login Petugas</a>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 md:px-6 py-6 md:py-8">
        <div class="mb-6">
            <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">Ajukan Safety Permit</h1>
            <p class="text-sm text-gray-500 mt-1">Tanpa login. Setelah dikirim, permit langsung masuk verifikasi <span class="font-semibold text-gray-700">Staff HSE</span>. Daftar Periksa Izin Pekerjaan Berisiko Tinggi dikelola terpisah oleh Staff HSE melalui dashboard.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl px-5 py-4">
                <p class="text-sm font-semibold text-red-700 mb-1">Ada yang perlu diperbaiki:</p>
                <ul class="text-sm text-red-600 list-disc list-inside">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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
                        6 => 'Review & Kirim',
                    ];
                @endphp
                @foreach($steps as $num => $label)
                    <div class="flex items-center {{ $num < 6 ? 'flex-1' : '' }}">
                        <div class="w-24 flex flex-col items-center">
                            <div class="step-circle w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold border-2 transition-all duration-200
                                {{ $num === 0 ? 'bg-inka-navy text-white border-inka-navy' : 'bg-white text-gray-400 border-gray-200' }}"
                                id="step-circle-{{ $num }}">
                                {{ $num }}
                            </div>
                            <span class="text-xs mt-1 font-medium text-center leading-tight whitespace-nowrap
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

        <form action="/ajukan-permit" method="POST" id="permit-form" enctype="multipart/form-data">
            @csrf

            <div id="form-error-alert" class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 p-4 rounded-xl transition-opacity duration-300 hidden">
                <div class="text-sm font-medium text-red-800">Mohon lengkapi semua form yang wajib diisi (bertanda *) pada langkah ini.</div>
            </div>

            {{-- STEP 0 — SITE & TIPE --}}
            <div id="step-0" class="space-y-5">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-800">Pilih Site <span class="text-red-500">*</span></h3>
                        <p class="text-sm text-gray-400 mt-0.5">Permit akan direview oleh Staff HSE site yang dipilih</p>
                    </div>
                    <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <button type="button" onclick="selectSite('Madiun')" id="btn-site-madiun"
                            class="site-btn p-6 rounded-2xl border-2 border-inka-navy bg-inka-navy/5 text-left transition-all">
                            <p class="text-lg font-bold text-gray-800">Madiun</p>
                            <p class="text-sm text-gray-400 mt-1">Plant Madiun — direview Staff HSE Madiun</p>
                        </button>
                        <button type="button" onclick="selectSite('Banyuwangi')" id="btn-site-banyuwangi"
                            class="site-btn p-6 rounded-2xl border-2 border-gray-200 hover:border-inka-navy text-left transition-all">
                            <p class="text-lg font-bold text-gray-800">Banyuwangi</p>
                            <p class="text-sm text-gray-400 mt-1">Plant Banyuwangi — direview Staff HSE Banyuwangi</p>
                        </button>
                    </div>
                    <input type="hidden" name="site" id="site-input" value="{{ old('site', 'Madiun') }}">
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-800">Pilih Tipe Permit</h3>
                        <p class="text-sm text-gray-400 mt-0.5">Internal untuk pekerjaan internal, Eksternal untuk pekerjaan oleh kontraktor</p>
                    </div>
                    <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <button type="button" onclick="selectTipe('Internal')" id="btn-internal"
                            class="tipe-btn p-6 rounded-2xl border-2 border-inka-navy bg-inka-navy/5 text-left transition-all">
                            <p class="text-lg font-bold text-gray-800">Internal</p>
                            <p class="text-sm text-gray-400 mt-1">Pekerjaan internal perusahaan (dokumen pendukung opsional)</p>
                        </button>
                        <button type="button" onclick="selectTipe('Eksternal')" id="btn-eksternal"
                            class="tipe-btn p-6 rounded-2xl border-2 border-gray-200 hover:border-inka-navy text-left transition-all">
                            <p class="text-lg font-bold text-gray-800">Eksternal</p>
                            <p class="text-sm text-gray-400 mt-1">Pekerjaan oleh kontraktor eksternal (wajib upload dokumen)</p>
                        </button>
                    </div>
                    <input type="hidden" name="tipe" id="tipe-input" value="Internal">
                </div>

            </div>

            {{-- STEP 4 — DOKUMEN --}}
            <div id="step-4" class="hidden space-y-5">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-800 mt-0.5">Dokumen Pendukung</h3>
                        <p class="text-sm text-gray-400 mt-0.5">Opsional untuk permit Internal, wajib untuk Eksternal (max 10MB per file)</p>
                    </div>
                    <div class="px-4 md:px-6 py-5">
                        <div id="documents-list" class="space-y-4"></div>
                        <button type="button" onclick="addDocument()"
                            class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-inka-navy border border-inka-navy rounded-xl hover:bg-inka-navy/5 transition-colors">
                            + Tambah Dokumen
                        </button>
                        <p id="doc-required-warning" class="text-sm text-red-500 mt-3 hidden">Minimal upload 1 dokumen pendukung.</p>
                    </div>
                </div>
            </div>

            {{-- STEP 1 — KLASIFIKASI + INFO --}}
            <div id="step-1" class="space-y-5 hidden">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-100">
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
                                    'panas' => 'Pekerjaan Panas',
                                    'ketinggian' => 'Pekerjaan Ketinggian',
                                    'ruang_terbatas' => 'Ruang Terbatas',
                                    'galian' => 'Pekerjaan Galian',
                                    'tegangan_tinggi' => 'Pekerjaan Tegangan Tinggi',
                                    'radiasi' => 'Radiasi',
                                ];
                            }
                        @endphp
                        @foreach($klasifikasi as $key => $label)
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-inka-navy/30 hover:bg-blue-50/30 cursor-pointer transition-colors">
                                <input type="checkbox" name="klasifikasi_pekerjaan[]" value="{{ $key }}"
                                    class="w-4 h-4 rounded text-inka-navy border-gray-300 focus:ring-inka-navy">
                                <span class="text-base text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-800 mt-0.5">Informasi Pekerjaan</h3>
                    </div>
                    <div class="px-4 md:px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="form-label">Divisi / Unit Pengaju <span class="text-red-500">*</span></label>
                            <input type="text" name="divisi_pengaju" class="form-input" value="{{ old('divisi_pengaju') }}" placeholder="Contoh: Divisi Teknik, Divisi Produksi, HSE" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="form-label">Pekerjaan <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_pekerjaan" class="form-input" value="{{ old('nama_pekerjaan') }}" placeholder="Contoh: Perbaikan Atap Gudang B" required>
                        </div>
                        <div>
                            <label class="form-label">Lokasi <span class="text-red-500">*</span></label>
                            <input type="text" name="lokasi" class="form-input" value="{{ old('lokasi') }}" placeholder="Contoh: Gudang B, Area Produksi" required>
                        </div>
                        <div>
                            <label class="form-label">Perusahaan / Kontraktor <span class="text-red-500">*</span></label>
                            <input type="text" name="kontraktor" class="form-input" value="{{ old('kontraktor') }}" placeholder="Nama perusahaan kontraktor" required>
                        </div>
                        <div>
                            <label class="form-label">Penanggung Jawab Lapangan</label>
                            <input type="text" name="penanggung_jawab" class="form-input" value="{{ old('penanggung_jawab') }}" placeholder="Nama PIC">
                        </div>
                        <div>
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="telepon" class="form-input" value="{{ old('telepon') }}" placeholder="08xxxxxxxxxx">
                        </div>
                        <div>
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-input" value="{{ old('tanggal_mulai') }}">
                        </div>
                        <div>
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-input" value="{{ old('tanggal_selesai') }}">
                        </div>
                    </div>

                    <div class="px-4 md:px-6 pb-5 border-t border-gray-100 pt-4">
                        <p class="text-sm font-semibold text-gray-600 mb-3">Daftar Pekerja</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                            @php
                                $pekerja = ['Engineer','Operator Alat Berat','Teknisi Listrik','Mekanik','Welder','Operator','Tukang Bangunan','Tukang Kayu','Helper'];
                            @endphp
                            @foreach($pekerja as $p)
                                @php $key = strtolower(str_replace(' ', '_', $p)); @endphp
                                <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2 border border-gray-100">
                                    <span class="text-sm text-gray-600 flex-1">{{ $p }}</span>
                                    <input type="number" name="daftar_pekerja[{{ $key }}]" min="0" value="0"
                                        class="w-16 text-center text-base font-semibold border border-gray-200 rounded-lg py-0.5 px-1 focus:ring-1 focus:ring-inka-navy focus:border-inka-navy">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="px-4 md:px-6 pb-5 border-t border-gray-100 pt-4">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold text-gray-600">Peralatan Kerja & Material</p>
                            <button type="button" onclick="addPeralatan()" class="text-sm font-semibold text-inka-navy hover:underline">+ Tambah Baris</button>
                        </div>
                        <div id="peralatan-list" class="space-y-2">
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                                <input type="text" name="peralatan_kerja[0][alat]" placeholder="Peralatan, mis. Gerinda" class="form-input text-base">
                                <input type="text" name="peralatan_kerja[0][jumlah_alat]" placeholder="Jumlah, mis. 1" class="form-input text-base">
                                <input type="text" name="peralatan_kerja[0][material]" placeholder="Material, mis. Baja" class="form-input text-base">
                                <input type="text" name="peralatan_kerja[0][jumlah_material]" placeholder="Jumlah, mis. 5 kg" class="form-input text-base">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 2 — BAHAYA + PENCEGAHAN --}}
            <div id="step-2" class="space-y-5 hidden">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-800 mt-0.5">Bahaya Pekerjaan</h3>
                    </div>
                    <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @php
                            $bahaya = [
                                'percikan_panas' => 'Percikan Panas',
                                'bahaya_kebakaran' => 'Bahaya Kebakaran',
                                'cidera_tulang' => 'Cidera Tulang Belakang',
                                'pencemaran_lingk' => 'Pencemaran Lingk.',
                                'terpukul_torbentur' => 'Terpukul / Torbentur',
                                'penerangan_kurang' => 'Penerangan Kurang',
                                'bahaya_makhluk_hidup' => 'Bahaya Makhluk Hidup',
                                'jatuh_ketinggian' => 'Jatuh Dari Ketinggian',
                                'lantai_licin' => 'Lantai Licin',
                                'tangga_penyangga' => 'Tangga / Penyangga Tidak Kokoh',
                                'bising' => 'Bising',
                                'menghasilkan_debu' => 'Menghasilkan Debu',
                                'bahaya_angin' => 'Bahaya Angin',
                                'keracunan_gas' => 'Keracunan Gas',
                                'peledakan' => 'Peledakan',
                                'bahaya_aliran_listrik' => 'Bahaya Alat / Aliran Listrik',
                                'bahaya_getaran' => 'Bahaya Getaran',
                                'bahaya_zat_kimia' => 'Bahaya Zat Kimia',
                                'terpotong_tertusuk' => 'Terpotong / Tertusuk',
                                'terperosok' => 'Terperosok',
                                'tertimpa_tertimpa' => 'Tertimbun / Tertimpa',
                                'mata_terkena' => 'Mata Kemasukan Benda',
                                'tertabrak' => 'Tertabrak / Tabrakan',
                                'limbah_b3' => 'Limbah B3',
                                'bahaya_radiasi' => 'Bahaya Radiasi',
                            ];
                        @endphp
                        @foreach($bahaya as $key => $label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="bahaya_pekerjaan[]" value="{{ $key }}"
                                    class="w-4 h-4 rounded text-inka-navy border-gray-300 focus:ring-inka-navy">
                                <span class="text-base text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                        <div class="col-span-full flex items-center gap-2 mt-1">
                            <span class="text-base text-gray-500 shrink-0">Lainnya:</span>
                            <input type="text" name="bahaya_lainnya" class="form-input text-base" placeholder="Sebutkan...">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-800 mt-0.5">Tindakan Pencegahan Bahaya</h3>
                    </div>
                    <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @php
                            $pencegahan = [
                                'proteksi_dari_jatuh' => 'Proteksi Dari Jatuh',
                                'media_penghalang_api' => 'Media Penghambat Api / Percikan',
                                'pintu_masuk_keluar' => 'Pintu Masuk / Keluar',
                                'safety_briefing' => 'Safety Briefing',
                                'tangga_penyangga_kuat' => 'Tangga / Penyangga Yang Kokoh',
                                'rambu_rambu' => 'Rambu-Rambu',
                                'jalur_evakuasi' => 'Jalur Evakuasi',
                                'penyediaan_pemadam' => 'Penyediaan Pemadam Api',
                                'barikade_polisi' => 'Barikade / Pagar / Police Line',
                                'sertifikat_kompetensi' => 'Sertifikat Kompetensi',
                            ];
                        @endphp
                        @foreach($pencegahan as $key => $label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="tindakan_pencegahan[]" value="{{ $key }}"
                                    class="w-4 h-4 rounded text-inka-navy border-gray-300 focus:ring-inka-navy">
                                <span class="text-base text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                        <div class="col-span-full flex items-center gap-2 mt-1">
                            <span class="text-base text-gray-500 shrink-0">Lainnya:</span>
                            <input type="text" name="pencegahan_lainnya" class="form-input text-base" placeholder="Sebutkan...">
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 3 — APD --}}
            <div id="step-3" class="hidden">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-800 mt-0.5">Alat Pelindung Diri (APD)</h3>
                        <p class="text-sm text-gray-400 mt-0.5">Pilih APD yang akan digunakan selama pekerjaan berlangsung</p>
                    </div>
                    <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @php
                            $apd = [
                                'helm' => 'Helm Keselamatan',
                                'kaca_mata' => 'Kaca Mata Keselamatan',
                                'sarung_tangan' => 'Sarung Tangan Kulit/Kaos/Karet',
                                'baju_pelindung' => 'Baju Pelindung',
                                'sepatu' => 'Sepatu Keselamatan',
                                'kaca_mata_debu' => 'Kaca Mata Debu',
                                'rompi' => 'Rompi Keselamatan',
                                'ear_plug' => 'Ear Plug / Ear Muff',
                                'tali_sabuk' => 'Tali / Sabuk Keselamatan',
                                'pelindung_muka' => 'Pelindung Muka',
                                'masker' => 'Masker / Respirator',
                            ];
                        @endphp
                        @foreach($apd as $key => $label)
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-inka-navy/30 hover:bg-blue-50/30 cursor-pointer transition-colors">
                                <input type="checkbox" name="apd[]" value="{{ $key }}"
                                    class="w-4 h-4 rounded text-inka-navy border-gray-300 focus:ring-inka-navy">
                                <span class="text-base text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                        <div class="col-span-full flex items-center gap-2 mt-1">
                            <span class="text-base text-gray-500 shrink-0">Lainnya:</span>
                            <input type="text" name="apd_lainnya" class="form-input text-base" placeholder="Sebutkan APD tambahan...">
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 5 — TTD --}}
            <div id="step-5" class="hidden">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-800 mt-0.5">Validasi Kerja</h3>
                        <p class="text-sm text-gray-400 mt-0.5">Tanda tangan pemohon, wajib diisi</p>
                    </div>
                    <div class="px-4 md:px-6 py-5 space-y-4">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="text-base font-semibold text-gray-700">Tanda Tangan Pemohon <span class="text-red-500">*</span></p>
                                <p class="text-sm text-gray-400">Gambar dengan mouse atau jari.</p>
                            </div>
                            <button type="button" onclick="clearSignature()"
                                class="text-sm font-semibold text-gray-400 hover:text-red-500 border border-gray-200 hover:border-red-300 px-3 py-1.5 rounded-lg transition-colors">
                                ✕ Hapus
                            </button>
                        </div>
                        <div id="signature-container"
                            class="relative border-2 border-dashed border-gray-300 rounded-xl overflow-hidden bg-white hover:border-inka-navy/40 transition-colors cursor-crosshair w-full max-w-[300px] aspect-square mx-auto">
                            <canvas id="signature-canvas" class="block w-full h-full" style="touch-action: none;"></canvas>
                            <span id="signature-placeholder"
                                class="absolute inset-0 flex items-center justify-center text-sm text-gray-300 pointer-events-none select-none">
                                Tanda tangan di sini
                            </span>
                        </div>
                        <input type="hidden" name="tanda_tangan" id="tanda-tangan-input">
                        <p id="signature-error" class="text-sm text-red-500 mt-1.5 hidden">Tanda tangan wajib diisi sebelum melanjutkan.</p>
                    </div>
                </div>
            </div>

            {{-- STEP 6 — REVIEW --}}
            <div id="step-6" class="hidden">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-800">Review Pengajuan</h3>
                        <p class="text-sm text-gray-400 mt-0.5">Periksa kembali sebelum mengirim ke Staff HSE</p>
                    </div>
                    <div class="px-4 md:px-6 py-5">
                        <div id="review-content" class="space-y-4 text-base text-gray-600">
                            <p class="text-sm text-gray-400 italic">Data review akan tampil setelah form sebelumnya diisi.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <button type="submit" id="btn-submit"
                        class="w-full sm:w-auto justify-center px-6 py-3 rounded-xl bg-inka-navy text-white text-base font-semibold hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="btn-text">Kirim ke Staff HSE</span>
                        <span class="btn-loading hidden">Mengirim...</span>
                    </button>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between" id="step-nav">
                <button type="button" id="btn-prev" onclick="changeStep(-1)"
                    class="hidden px-5 py-2.5 rounded-xl border border-gray-200 bg-white text-base font-semibold text-gray-600 hover:border-gray-300 transition-colors">
                    Kembali
                </button>
                <div></div>
                <button type="button" id="btn-next" onclick="changeStep(1)"
                    class="px-5 py-2.5 rounded-xl bg-inka-navy text-white text-base font-semibold hover:opacity-90 transition-opacity">
                    Selanjutnya
                </button>
            </div>
        </form>

        <p class="text-center text-xs text-gray-400 mt-8">Dengan mengirim, Anda menyatakan data di atas benar dan pekerjaan siap diverifikasi HSE.</p>
    </main>

    <script>
        let currentStep = 0;
        const totalSteps = 7;
        let selectedTipe = 'Internal';

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
            if (direction > 0) {
                const stepEl = document.getElementById('step-' + currentStep);
                const inputs = stepEl.querySelectorAll('input[required], select[required], textarea[required]');
                inputs.forEach(i => i.setCustomValidity(''));
                for (const input of inputs) {
                    if (!input.checkValidity()) {
                        input.reportValidity();
                        const errorAlert = document.getElementById('form-error-alert');
                        errorAlert.classList.remove('hidden');
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        return;
                    }
                }
                if (currentStep === 4 && selectedTipe === 'Eksternal') {
                    if (document.querySelectorAll('.doc-entry').length === 0) {
                        document.getElementById('doc-required-warning').classList.remove('hidden');
                        return;
                    }
                }
                if (currentStep === 5) {
                    if (!window._signatureHasDrawn || !window._signatureHasDrawn()) {
                        document.getElementById('signature-error').classList.remove('hidden');
                        return;
                    }
                }
            }

            document.getElementById('step-' + currentStep).classList.add('hidden');
            updateStepIndicator(currentStep, nextStep);
            currentStep = nextStep;
            document.getElementById('step-' + currentStep).classList.remove('hidden');
            if (currentStep === 5 && typeof window._resizeSignature === 'function') window._resizeSignature();
            document.getElementById('btn-prev').classList.toggle('hidden', currentStep === 0);
            document.getElementById('btn-next').classList.toggle('hidden', currentStep === totalSteps - 1);
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
            let html = '<div class="p-3 bg-gray-50 rounded-xl mb-4"><p class="text-sm text-gray-400">Tipe Permit</p><p class="font-semibold text-gray-800">' + selectedTipe + '</p></div>';
            const divisi = fd.get('divisi_pengaju') || '—';
            const namaP = fd.get('nama_pekerjaan') || '—';
            const lokasi = fd.get('lokasi') || '—';
            const kontra = fd.get('kontraktor') || '—';
            const pic = fd.get('penanggung_jawab') || '—';
            const telp = fd.get('telepon') || '—';
            const tgl1 = fd.get('tanggal_mulai') || '—';
            const tgl2 = fd.get('tanggal_selesai') || '—';
            html += '<div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-base">'
                + '<div><p class="text-sm text-gray-400">Divisi Pengaju</p><p class="font-medium text-gray-800">' + divisi + '</p></div>'
                + '<div><p class="text-sm text-gray-400">Nama Pekerjaan</p><p class="font-medium text-gray-800">' + namaP + '</p></div>'
                + '<div><p class="text-sm text-gray-400">Lokasi</p><p class="font-medium text-gray-800">' + lokasi + '</p></div>'
                + '<div><p class="text-sm text-gray-400">Kontraktor</p><p class="font-medium text-gray-800">' + kontra + '</p></div>'
                + '<div><p class="text-sm text-gray-400">Penanggung Jawab</p><p class="font-medium text-gray-800">' + pic + '</p></div>'
                + '<div><p class="text-sm text-gray-400">Telepon</p><p class="font-medium text-gray-800">' + telp + '</p></div>'
                + '<div><p class="text-sm text-gray-400">Tanggal</p><p class="font-medium text-gray-800">' + tgl1 + ' s/d ' + tgl2 + '</p></div>'
                + '</div>';
            document.getElementById('review-content').innerHTML = html;
        }

        let docCount = 0;
        function addDocument() {
            const list = document.getElementById('documents-list');
            const idx = docCount;
            list.insertAdjacentHTML('beforeend',
                '<div class="doc-entry p-4 border border-gray-100 rounded-xl space-y-3" id="doc-' + idx + '">'
                + '<div class="flex items-center justify-between"><p class="text-sm font-semibold text-gray-600">Dokumen ' + (idx + 1) + '</p>'
                + '<button type="button" onclick="removeDocument(' + idx + ')" class="text-sm text-red-500 hover:underline">Hapus</button></div>'
                + '<div><label class="form-label">Nama Dokumen <span class="text-red-500">*</span></label>'
                + '<input type="text" name="dokumen[' + idx + '][nama]" class="form-input" placeholder="Contoh: HIRADC" required></div>'
                + '<div><label class="form-label">Deskripsi</label>'
                + '<textarea name="dokumen[' + idx + '][deskripsi]" class="form-input" rows="2" placeholder="Deskripsi singkat..."></textarea></div>'
                + '<div><label class="form-label">File <span class="text-red-500">*</span></label>'
                + '<input type="file" name="dokumen[' + idx + '][file]" class="form-input" required>'
                + '<p class="text-xs text-gray-400 mt-1">Maks 10MB. PDF, gambar, Office.</p></div></div>');
            docCount++;
            document.getElementById('doc-required-warning').classList.add('hidden');
        }
        function removeDocument(idx) {
            const el = document.getElementById('doc-' + idx);
            if (el) el.remove();
        }

        (function () {
            const canvas = document.getElementById('signature-canvas');
            const placeholder = document.getElementById('signature-placeholder');
            const hiddenInput = document.getElementById('tanda-tangan-input');
            const container = document.getElementById('signature-container');
            const ctx = canvas.getContext('2d');
            function resizeCanvas() {
                const rect = container.getBoundingClientRect();
                if (rect.width === 0) return;
                // HP saja: batasi DPR max 1.5. Laptop tetap DPR asli.
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
            window._resizeSignature = resizeCanvas;
            let drawing = false, hasDrawn = false;
            function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                if (e.touches) return { x: e.touches[0].clientX - rect.left, y: e.touches[0].clientY - rect.top };
                return { x: e.clientX - rect.left, y: e.clientY - rect.top };
            }
            function startDraw(e) { e.preventDefault(); drawing = true; const pos = getPos(e); ctx.beginPath(); ctx.moveTo(pos.x, pos.y); }
            function draw(e) {
                if (!drawing) return; e.preventDefault();
                const pos = getPos(e); ctx.lineTo(pos.x, pos.y); ctx.stroke();
                if (!hasDrawn) {
                    hasDrawn = true; placeholder.classList.add('hidden');
                    document.getElementById('signature-error').classList.add('hidden');
                }
            }
            function stopDraw() { if (!drawing) return; drawing = false; ctx.beginPath(); hiddenInput.value = window.matchMedia('(max-width: 767px)').matches ? canvas.toDataURL('image/jpeg', 0.7) : canvas.toDataURL('image/png'); }
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
                hasDrawn = false; hiddenInput.value = '';
                placeholder.classList.remove('hidden');
            };
            window._signatureHasDrawn = function () { return hasDrawn; };
        })();

        let peralatanCount = 1;
        function addPeralatan() {
            const list = document.getElementById('peralatan-list');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-2 lg:grid-cols-4 gap-2';
            row.innerHTML = '<input type="text" name="peralatan_kerja[' + peralatanCount + '][alat]" placeholder="Peralatan" class="form-input text-base">'
                + '<input type="text" name="peralatan_kerja[' + peralatanCount + '][jumlah_alat]" placeholder="Jumlah" class="form-input text-base">'
                + '<input type="text" name="peralatan_kerja[' + peralatanCount + '][material]" placeholder="Material" class="form-input text-base">'
                + '<input type="text" name="peralatan_kerja[' + peralatanCount + '][jumlah_material]" placeholder="Jumlah" class="form-input text-base">';
            list.appendChild(row);
            peralatanCount++;
        }

        (function () {
            const form = document.getElementById('permit-form');
            form.addEventListener('submit', function () {
                const btn = document.getElementById('btn-submit');
                btn.disabled = true;
                btn.querySelector('.btn-text').classList.add('hidden');
                btn.querySelector('.btn-loading').classList.remove('hidden');
            });
        })();
    </script>
</body>
</html>
