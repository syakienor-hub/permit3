<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kontraktor Tersimpan — PT INKA (Persero)</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logok3.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-gray-900 min-h-screen">
    <header class="bg-white border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-4 md:px-6 h-16 flex items-center gap-2.5">
            <a href="/" class="flex items-center gap-2.5">
                <img src="{{ asset('assets/images/logoinka.svg') }}" alt="INKA" class="h-6 sm:h-7 w-auto shrink-0">
                <div class="border-l border-gray-200 pl-2.5 sm:pl-3 whitespace-nowrap">
                    <span class="text-[13px] sm:text-sm font-bold">Data Kontraktor</span>
                    <span class="block text-[9px] sm:text-[10px] font-semibold text-gray-500 tracking-wide">PT INKA (Persero)</span>
                </div>
            </a>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 md:px-6 py-8 md:py-12">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8 text-center">
            <div class="w-16 h-16 rounded-full bg-green-50 border border-green-100 flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-800">Data Kontraktor Tersimpan</h1>
            <p class="text-sm text-gray-500 mt-2">Data kontraktor <span class="font-semibold text-gray-700">{{ $contractor->vendor }}</span> sudah kami simpan.</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mt-5">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Ringkasan Data</h2>
            </div>
            <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div><p class="text-gray-400">Vendor / Perusahaan</p><p class="font-semibold text-gray-700">{{ $contractor->vendor }}</p></div>
                <div><p class="text-gray-400">PIC</p><p class="font-semibold text-gray-700">{{ $contractor->pic }}</p></div>
                <div><p class="text-gray-400">Jenis Pekerjaan</p><p class="font-semibold text-gray-700">{{ \App\Models\Classification::where('code', $contractor->jenis_pekerjaan)->value('name') ?? $contractor->jenis_pekerjaan }}</p></div>
                <div><p class="text-gray-400">No. Telepon</p><p class="font-semibold text-gray-700">{{ $contractor->telepon }}</p></div>
                <div class="sm:col-span-2"><p class="text-gray-400">Nama Pekerjaan</p><p class="font-semibold text-gray-700 whitespace-pre-line">{{ $contractor->nama_pekerjaan }}</p></div>
                <div><p class="text-gray-400">Tanggal Mulai</p><p class="font-semibold text-gray-700">{{ $contractor->tanggal_mulai?->format('d/m/Y') }}</p></div>
                <div><p class="text-gray-400">Tanggal Selesai</p><p class="font-semibold text-gray-700">{{ $contractor->tanggal_selesai?->format('d/m/Y') }}</p></div>
                <div><p class="text-gray-400">Jumlah Pekerja</p><p class="font-semibold text-gray-700">{{ $contractor->jumlah_pekerja }}</p></div>
                <div><p class="text-gray-400">Lokasi / Site</p><p class="font-semibold text-gray-700">{{ $contractor->lokasi }} &middot; {{ $contractor->site }}</p></div>
            </div>
            @if($contractor->documents->isNotEmpty())
                <div class="px-6 pb-5">
                    <p class="text-sm text-gray-400 mb-2">Dokumen terunggah</p>
                    <ul class="space-y-1.5">
                        @foreach($contractor->documents as $document)
                            <li class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-inka-navy shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                {{ $document->nama_dokumen }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="bg-blue-50 border border-blue-100 rounded-2xl px-5 py-5 mt-5">
            <p class="text-base font-semibold text-gray-800">Langkah selanjutnya</p>
            <p class="text-sm text-gray-500 mt-1">Data kontraktor belum memiliki HSE Plan. Isi form HSE Plan agar pengajuan dapat direview oleh Manager HSE. Data yang sudah Anda isi akan otomatis terisi.</p>
            <div class="flex flex-col sm:flex-row gap-3 mt-4">
                <a href="{{ $hsePlanFormUrl }}"
                    class="inline-flex items-center justify-center rounded-xl bg-inka-navy px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90 transition-opacity">
                    Isi Form HSE Plan
                </a>
                <a href="/"
                    class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </main>
</body>
</html>
