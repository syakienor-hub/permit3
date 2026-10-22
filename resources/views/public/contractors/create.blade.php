<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kontraktor — PT INKA (Persero)</title>
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
        .form-input:focus { outline: none; border-color: #111d33; box-shadow: 0 0 0 3px rgba(17,29,51,0.08); }
        .form-input:disabled { background: #f9fafb; color: #9ca3af; }
    </style>
</head>
<body class="bg-slate-50 text-gray-900 min-h-screen">
    <header class="bg-white border-b border-gray-100 sticky top-0 z-30">
        <div class="max-w-5xl mx-auto px-4 md:px-6 h-16 flex items-center gap-2.5 sm:gap-3">
            <a href="/" class="flex items-center gap-2.5 mr-auto min-w-0">
                <img src="{{ asset('assets/images/logoinka.svg') }}" alt="INKA" class="h-6 sm:h-7 w-auto shrink-0">
                <div class="border-l border-gray-200 pl-2.5 sm:pl-3 whitespace-nowrap">
                    <span class="text-[13px] sm:text-sm font-bold">Data Kontraktor</span>
                    <span class="block text-[9px] sm:text-[10px] font-semibold text-gray-500 tracking-wide">PT INKA (Persero)</span>
                </div>
            </a>
            <a href="/" title="Kembali ke Beranda"
                class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 px-3 py-1.5 text-sm font-semibold text-gray-500 hover:border-inka-navy/30 hover:bg-gray-50 hover:text-inka-navy transition-colors whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10"/>
                </svg>
                Beranda
            </a>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 md:px-6 py-6 md:py-8">
        <div class="mb-6">
            <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">Tambah Data Kontraktor</h1>
            <p class="text-sm text-gray-500 mt-1">Isi data kontraktor dan lampirkan dokumen. Jika belum memiliki HSE Plan, Anda dapat langsung mengisi form HSE Plan di bagian bawah.</p>
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

        <form action="/data-kontraktor" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">Data Kontraktor</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Data vendor, PIC, dan informasi pekerjaan</p>
                </div>
                <div class="px-4 md:px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="form-label">Vendor / Perusahaan <span class="text-red-500">*</span></label>
                        <input type="text" name="vendor" class="form-input" value="{{ old('vendor') }}" placeholder="Nama PT Vendor" required>
                    </div>
                    <div>
                        <label class="form-label">PIC <span class="text-red-500">*</span></label>
                        <input type="text" name="pic" class="form-input" value="{{ old('pic') }}" placeholder="Nama Penanggungjawab" required>
                    </div>
                    <div>
                        <label class="form-label">Jenis Pekerjaan <span class="text-red-500">*</span></label>
                        <select name="jenis_pekerjaan" class="form-input" required>
                            <option value="">Pilih Jenis Pekerjaan</option>
                            @foreach($classifications as $classification)
                                <option value="{{ $classification->code }}" @selected(old('jenis_pekerjaan') === $classification->code)>{{ $classification->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">No. Telepon <span class="text-red-500">*</span></label>
                        <input type="text" name="telepon" class="form-input" value="{{ old('telepon') }}" placeholder="08xx..." required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label">Nama Pekerjaan <span class="text-red-500">*</span></label>
                        <textarea name="nama_pekerjaan" rows="3" class="form-input" placeholder="Uraikan nama dan lingkup pekerjaan" required>{{ old('nama_pekerjaan') }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" class="form-input" value="{{ old('tanggal_mulai') }}" required>
                    </div>
                    <div>
                        <label class="form-label">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_selesai" class="form-input" value="{{ old('tanggal_selesai') }}" required>
                    </div>
                    <div>
                        <label class="form-label">Jumlah Pekerja <span class="text-red-500">*</span></label>
                        <input type="number" name="jumlah_pekerja" class="form-input" value="{{ old('jumlah_pekerja') }}" placeholder="Jumlah pekerja" min="1" required>
                    </div>
                    <div>
                        <label class="form-label">Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" name="lokasi" class="form-input" value="{{ old('lokasi') }}" placeholder="Ketik lokasi pekerjaan" required>
                    </div>
                    <div>
                        <label class="form-label">Site <span class="text-red-500">*</span></label>
                        <select name="site" class="form-input" required>
                            <option value="Madiun" @selected(old('site', 'Madiun') === 'Madiun')>Madiun</option>
                            <option value="Banyuwangi" @selected(old('site') === 'Banyuwangi')>Banyuwangi</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 md:px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-800">Dokumen</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Opsional. Format PDF, DOC, XLS, atau gambar. Maksimal 10MB per file.</p>
                </div>
                <div class="px-4 md:px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="form-label">Dokumen HSE Plan (jika sudah ada)</label>
                        <input type="file" name="hse_plan_file" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        <p class="text-xs text-gray-400 mt-1.5">Upload jika perusahaan Anda sudah memiliki dokumen HSE Plan. Dokumen ini akan langsung masuk antrean review Manager HSE.</p>
                    </div>
                    <div>
                        <label class="form-label">Dokumen Lainnya</label>
                        <input type="file" name="dokumen_lainnya[]" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" multiple>
                        <p class="text-xs text-gray-400 mt-1.5">Misalnya NPWP, izin usaha, atau dokumen pendukung lainnya.</p>
                    </div>
                </div>
            </section>

            <section class="bg-blue-50 border border-blue-100 rounded-2xl px-4 md:px-6 py-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <p class="text-base font-semibold text-gray-800">Belum memiliki dokumen HSE Plan?</p>
                        <p class="text-sm text-gray-500 mt-1">Klik tombol di samping untuk mengisi form HSE Plan. Data kontraktor yang sudah Anda isi akan otomatis terisi di form tersebut.</p>
                    </div>
                    <button type="submit" name="action" value="isi_form"
                        class="shrink-0 inline-flex items-center justify-center gap-2 rounded-xl border border-inka-navy bg-white px-4 py-2.5 text-sm font-semibold text-inka-navy hover:bg-inka-navy hover:text-white transition-colors whitespace-nowrap">
                        Isi Form HSE Plan
                    </button>
                </div>
            </section>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pb-8">
                <a href="/" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 text-center hover:bg-gray-50 transition-colors">Batal</a>
                <button type="submit" name="action" value="simpan"
                    class="px-5 py-2.5 rounded-xl bg-inka-navy text-white text-sm font-semibold hover:opacity-90 transition-opacity">Simpan Data</button>
            </div>
        </form>
    </main>
</body>
</html>
