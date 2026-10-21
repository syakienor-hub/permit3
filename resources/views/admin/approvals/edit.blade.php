<x-app-layout>
    <x-slot name="title">Koreksi Detail Permit — {{ $permit->no_permit }}</x-slot>

    @php
        $selectedKlasifikasi = old('klasifikasi_pekerjaan', $permit->klasifikasi_pekerjaan ?? []);
        $selectedBahaya = old('bahaya_pekerjaan', $permit->bahaya_pekerjaan ?? []);
        $selectedPencegahan = old('tindakan_pencegahan', $permit->tindakan_pencegahan ?? []);
        $selectedApd = old('apd', $permit->apd ?? []);
        $existingPeralatan = old('peralatan_kerja', $permit->peralatan_kerja ?? []);

        $dbKlasifikasi = \App\Models\Classification::all();
        $klasifikasi = $dbKlasifikasi->isNotEmpty()
            ? $dbKlasifikasi->pluck('name', 'code')->toArray()
            : [
                'panas'           => 'Pekerjaan Panas',
                'ketinggian'      => 'Pekerjaan Ketinggian',
                'ruang_terbatas'  => 'Ruang Terbatas',
                'galian'          => 'Pekerjaan Galian',
                'tegangan_tinggi' => 'Pekerjaan Tegangan Tinggi',
                'radiasi'         => 'Radiasi',
            ];

        $bahaya = [
            'percikan_panas'        => 'Percikan Panas',
            'bahaya_kebakaran'      => 'Bahaya Kebakaran',
            'cidera_tulang'         => 'Cidera Tulang Belakang',
            'pencemaran_lingk'      => 'Pencemaran Lingk.',
            'terpukul_torbentur'    => 'Terpukul / Torbentur',
            'penerangan_kurang'     => 'Penerangan Kurang',
            'bahaya_makhluk_hidup'  => 'Bahaya Makhluk Hidup',
            'jatuh_ketinggian'      => 'Jatuh Dari Ketinggian',
            'lantai_licin'          => 'Lantai Licin',
            'tangga_penyangga'      => 'Tangga / Penyangga Tidak Kokoh / Patah',
            'bising'                => 'Bising',
            'menghasilkan_debu'     => 'Menghasilkan Debu',
            'bahaya_angin'          => 'Bahaya Angin',
            'keracunan_gas'         => 'Keracunan Gas',
            'peledakan'             => 'Peledakan',
            'bahaya_aliran_listrik' => 'Bahaya Alat / Aliran Listrik',
            'bahaya_getaran'        => 'Bahaya Getaran',
            'bahaya_zat_kimia'      => 'Bahaya Zat Kimia',
            'terpotong_tertusuk'    => 'Terpotong / Tertusuk',
            'terperosok'            => 'Terperosok',
            'tertimpa_tertimpa'     => 'Tertimbun / Tertimpa',
            'mata_terkena'          => 'Mata Kemasukan Benda',
            'tertabrak'             => 'Tertabrak / Tabrakan',
            'limbah_b3'             => 'Limbah B3',
            'bahaya_radiasi'        => 'Bahaya Radiasi',
        ];

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

        $apd = [
            'helm'           => 'Helm Keselamatan',
            'kaca_mata'      => 'Kaca Mata Keselamatan',
            'sarung_tangan'  => 'Sarung Tangan Kulit/Kaos/Karet',
            'baju_pelindung' => 'Baju Pelindung',
            'sepatu'         => 'Sepatu Keselamatan',
            'kaca_mata_debu' => 'Kaca Mata Debu',
            'rompi'          => 'Rompi Keselamatan',
            'ear_plug'       => 'Ear Plug / Ear Muff',
            'tali_sabuk'     => 'Tali / Sabuk Keselamatan',
            'pelindung_muka' => 'Pelindung Muka',
            'masker'         => 'Masker / Respirator',
        ];
    @endphp

    {{-- Breadcrumb --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <div class="flex items-center gap-2 text-sm text-gray-400 min-w-0">
            <a href="/admin/dashboard" class="hover:text-gray-600 transition-colors shrink-0">Dashboard</a>
            <span>/</span>
            <a href="/admin/approvals" class="hover:text-gray-600 transition-colors shrink-0">Review Permit</a>
            <span>/</span>
            <a href="/admin/approvals/{{ $permit->id }}" class="hover:text-gray-600 transition-colors shrink-0">{{ $permit->no_permit }}</a>
            <span>/</span>
            <span class="font-semibold text-gray-700 truncate">Koreksi Detail</span>
        </div>
    </div>

    {{-- Header kartu --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-4 md:px-6 py-5 mb-5">
        <p class="text-xs text-gray-400 mb-1">Nomor Permit</p>
        <div class="flex flex-wrap items-center gap-2">
            <p class="text-xl font-bold text-gray-800">{{ $permit->no_permit }}</p>
            <x-permit-tipe-badge :tipe="$permit->tipe" />
            <x-permit-site-badge :site="$permit->site" />
        </div>
        <p class="text-xs text-gray-500 mt-1">
            Divisi: <span class="font-semibold text-gray-700">{{ optional($permit->user)->name ?? $permit->divisi_pengaju ?? '—' }}</span>
        </p>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-2xl px-4 md:px-6 py-4 mb-5 flex gap-3">
        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-amber-700">Koreksi detail sebelum diteruskan ke Manager</p>
            <p class="text-sm text-amber-600 mt-1">Setiap perubahan yang Anda simpan akan tercatat di riwayat perubahan permit ini. Site dan tipe permit tidak dapat diubah di sini.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 p-4 rounded-xl">
        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div class="text-sm font-medium text-red-800">
            <p>Mohon periksa kembali isian yang ditandai:</p>
            <ul class="list-disc list-inside mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form action="/admin/approvals/{{ $permit->id }}/edit" method="POST" id="detail-form">
        @csrf
        @method('PUT')

        <div class="space-y-5">
            {{-- A. Klasifikasi Pekerjaan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">A</span>
                    <h3 class="text-base font-semibold text-gray-800 mt-0.5">Klasifikasi Pekerjaan</h3>
                </div>
                <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
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
                        <input type="text" name="nama_pekerjaan" class="form-input @error('nama_pekerjaan') error @enderror"
                            value="{{ old('nama_pekerjaan', $permit->nama_pekerjaan) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" name="lokasi" class="form-input @error('lokasi') error @enderror"
                            value="{{ old('lokasi', $permit->lokasi) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Perusahaan <span class="text-red-500">*</span></label>
                        <input type="text" name="kontraktor" class="form-input @error('kontraktor') error @enderror"
                            value="{{ old('kontraktor', $permit->kontraktor) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Manager / Penanggung Jawab</label>
                        <input type="text" name="penanggung_jawab" class="form-input"
                            value="{{ old('penanggung_jawab', $permit->penanggung_jawab) }}">
                    </div>
                    <div>
                        <label class="form-label">No. Telpon</label>
                        <input type="text" name="telepon" class="form-input"
                            value="{{ old('telepon', $permit->telepon) }}">
                    </div>
                    <div>
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-input @error('tanggal_mulai') error @enderror"
                            value="{{ old('tanggal_mulai', $permit->tanggal_mulai?->format('Y-m-d')) }}">
                    </div>
                    <div>
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-input @error('tanggal_selesai') error @enderror"
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
                        @forelse($existingPeralatan as $idx => $item)
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 items-center">
                                <input type="text" name="peralatan_kerja[{{ $idx }}][alat]" placeholder="e.g. Gerinda" class="form-input text-base" value="{{ is_array($item) ? ($item['alat'] ?? '') : '' }}">
                                <input type="text" name="peralatan_kerja[{{ $idx }}][jumlah_alat]" placeholder="1" class="form-input text-base" value="{{ is_array($item) ? ($item['jumlah_alat'] ?? '') : '' }}">
                                <input type="text" name="peralatan_kerja[{{ $idx }}][material]" placeholder="e.g. Baja" class="form-input text-base" value="{{ is_array($item) ? ($item['material'] ?? '') : '' }}">
                                <input type="text" name="peralatan_kerja[{{ $idx }}][jumlah_material]" placeholder="5 kg" class="form-input text-base" value="{{ is_array($item) ? ($item['jumlah_material'] ?? '') : '' }}">
                            </div>
                        @empty
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 items-center">
                                <input type="text" name="peralatan_kerja[0][alat]" placeholder="e.g. Gerinda" class="form-input text-base">
                                <input type="text" name="peralatan_kerja[0][jumlah_alat]" placeholder="1" class="form-input text-base">
                                <input type="text" name="peralatan_kerja[0][material]" placeholder="e.g. Baja" class="form-input text-base">
                                <input type="text" name="peralatan_kerja[0][jumlah_material]" placeholder="5 kg" class="form-input text-base">
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- C. Bahaya Pekerjaan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">C</span>
                    <h3 class="text-base font-semibold text-gray-800 mt-0.5">Bahaya Pekerjaan</h3>
                </div>
                <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
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

            {{-- E. APD --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">E</span>
                    <h3 class="text-base font-semibold text-gray-800 mt-0.5">Alat Pelindung Diri (APD)</h3>
                </div>
                <div class="px-4 md:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
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

        <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-end">
            <a href="/admin/approvals/{{ $permit->id }}"
               class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 text-base font-semibold text-center hover:border-gray-300 transition-colors">
                Batal
            </a>
            <button type="submit" id="btn-save-detail"
                class="px-6 py-3 rounded-xl bg-inka-navy text-white text-base font-semibold hover:opacity-90 transition-opacity">
                Simpan Perubahan
            </button>
        </div>
    </form>

    @if($permit->edit_history && count($permit->edit_history) > 0)
    <div class="mt-5 bg-white rounded-2xl border border-gray-100 shadow-sm px-4 md:px-6 py-5">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Riwayat Koreksi Detail</h3>
        <div class="space-y-4">
            @foreach(array_reverse($permit->edit_history) as $entry)
            <div class="border border-gray-100 rounded-xl p-3 bg-gray-50">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="text-xs font-bold text-gray-700">{{ $entry['by'] ?? '—' }}</span>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">{{ $entry['role'] ?? 'Staff HSE' }}</span>
                    <span class="text-[10px] text-gray-400">{{ isset($entry['at']) ? \Illuminate\Support\Carbon::parse($entry['at'])->format('d/m/Y H:i') : '—' }}</span>
                </div>
                <ul class="space-y-1">
                    @foreach($entry['changes'] ?? [] as $change)
                    <li class="text-xs text-gray-600">
                        <span class="font-semibold text-gray-700">{{ $change['label'] ?? '' }}:</span>
                        <span class="text-red-500 line-through">{{ ($change['from'] ?? '') !== '' ? $change['from'] : '—' }}</span>
                        <span class="text-gray-400">→</span>
                        <span class="text-green-600 font-medium">{{ ($change['to'] ?? '') !== '' ? $change['to'] : '—' }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>
    </div>
    @endif

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
        let peralatanCount = {{ max(count($existingPeralatan), 1) }};

        function addPeralatan() {
            const idx = peralatanCount++;
            const row = document.createElement('div');
            row.className = 'grid grid-cols-2 lg:grid-cols-4 gap-2 items-center';
            row.innerHTML =
                '<input type="text" name="peralatan_kerja[' + idx + '][alat]" placeholder="e.g. Gerinda" class="form-input text-base">' +
                '<input type="text" name="peralatan_kerja[' + idx + '][jumlah_alat]" placeholder="1" class="form-input text-base">' +
                '<input type="text" name="peralatan_kerja[' + idx + '][material]" placeholder="e.g. Baja" class="form-input text-base">' +
                '<input type="text" name="peralatan_kerja[' + idx + '][jumlah_material]" placeholder="5 kg" class="form-input text-base">';
            document.getElementById('peralatan-list').appendChild(row);
        }

        document.getElementById('detail-form').addEventListener('submit', function () {
            const btn = document.getElementById('btn-save-detail');
            btn.disabled = true;
            btn.textContent = 'Menyimpan...';
        });
    </script>

</x-app-layout>
