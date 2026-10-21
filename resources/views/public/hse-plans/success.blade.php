<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HSE Plan Terkirim — INKA Madiun</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logok3.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center px-4 py-8">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm max-w-xl w-full p-6 sm:p-8 text-center">
        <div class="w-14 h-14 rounded-2xl bg-green-50 border border-green-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-gray-800">HSE Plan Berhasil Dikirim</h1>
        <p class="text-sm text-gray-500 mt-1">Pengajuan Anda sudah masuk ke antrean persetujuan Manager SHE.</p>

        <div class="mt-6 rounded-xl border border-gray-100 bg-gray-50/60 px-4 py-4 text-left">
            <p class="text-xs text-gray-400">Nomor HSE Plan</p>
            <p class="text-lg font-bold text-inka-navy font-mono">{{ $hsePlan->no_hse_plan }}</p>
            <div class="grid sm:grid-cols-2 gap-2 mt-3 text-sm">
                <div><span class="text-gray-400">Kontraktor:</span> <span class="font-semibold text-gray-700">{{ $hsePlan->nama_kontraktor }}</span></div>
                <div><span class="text-gray-400">Site:</span> <span class="font-semibold text-gray-700">{{ $hsePlan->site }}</span></div>
                <div class="sm:col-span-2"><span class="text-gray-400">Pekerjaan:</span> <span class="font-semibold text-gray-700">{{ $hsePlan->deskripsi_pekerjaan }}</span></div>
            </div>
        </div>

        <p class="text-sm text-gray-500 mt-4">Simpan nomor HSE Plan ini. Status dapat dicek kapan saja menggunakan nomor HSE Plan dan nomor telepon yang Anda isi.</p>

        <div class="flex flex-col sm:flex-row gap-2.5 mt-6">
            <a href="{{ $statusUrl }}" class="flex-1 px-5 py-2.5 rounded-xl bg-inka-navy text-white text-sm font-semibold hover:opacity-90 transition-opacity">Cek Status HSE Plan</a>
            <a href="/" class="flex-1 px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
