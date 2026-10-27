<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Permit — PT INKA (Persero)</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logok3.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col items-center justify-center px-4 py-8">
    <div class="w-full max-w-md mb-3">
        <a href="/" class="text-sm text-gray-500 hover:text-inka-navy">&larr; Kembali ke Beranda</a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm max-w-md w-full p-6 sm:p-8 text-center">
        @if(session('success'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 text-left">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 text-left">{{ session('error') }}</div>
        @endif

        @if($permit->status === 'Cancelled')
            <div class="w-14 h-14 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
            <h1 class="text-xl font-extrabold text-red-700">Permit Dibatalkan</h1>
            <p class="text-sm text-gray-500 mt-2">Nomor permit:</p>
            <p class="text-lg font-bold text-inka-navy mt-1">{{ $permit->no_permit }}</p>
            <p class="text-sm text-gray-500 mt-3">
                Divisi <span class="font-semibold text-gray-700">{{ $permit->divisi_pengaju }}</span> —
                status <span class="font-semibold text-red-600">Cancelled</span>.
            </p>
            @if($permit->cancellation_reason)
                <p class="text-sm text-gray-600 mt-3 text-left"><strong>Alasan:</strong> {{ $permit->cancellation_reason }}</p>
            @endif
            @if($permit->cancelled_at)
                <p class="text-xs text-gray-400 mt-1 text-left">Dibatalkan pada {{ $permit->cancelled_at->format('d/m/Y H:i') }}</p>
            @endif
        @else
            <div class="w-14 h-14 rounded-full bg-green-100 text-green-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 class="text-xl font-extrabold">Permit Berhasil Diajukan</h1>
            <p class="text-sm text-gray-500 mt-2">Nomor permit Anda:</p>
            <p class="text-lg font-bold text-inka-navy mt-1">{{ $permit->no_permit }}</p>
            <p class="text-sm text-gray-500 mt-3">
                Divisi <span class="font-semibold text-gray-700">{{ $permit->divisi_pengaju }}</span> —
                status <span class="font-semibold text-orange-600">{{ $permit->status }}</span>.
            </p>

            @if($checklistUrl)
                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-left text-sm text-amber-800">
                    <p class="font-semibold">Lengkapi Daftar Periksa Izin Kerja</p>
                    <p class="mt-1">Permit ini belum masuk antrian review Staff HSE. Buat daftar periksa terlebih dahulu; setelah disetujui, permit otomatis masuk Review Staff.</p>
                </div>
            @endif

            <p class="text-xs text-gray-400 mt-3">Simpan tautan halaman ini agar Anda dapat membuka detail permit. Anda juga dapat memeriksa status kapan saja di <a href="/cek-permit" class="font-semibold text-inka-navy hover:underline">Cek Status Permit</a> menggunakan nomor permit dan nomor telepon yang Anda isi.</p>
        @endif

        <div class="mt-6 flex flex-col sm:flex-row gap-2.5 sm:gap-3 justify-center">
            @if($checklistUrl)
                <a href="{{ $checklistUrl }}" class="px-5 py-2.5 rounded-xl bg-inka-navy text-white text-sm font-semibold hover:opacity-90 transition-opacity">Buat Daftar Periksa</a>
            @endif
            @if(!in_array($permit->status, ['Closed', 'Cancelled']))
                <a href="{{ $cancelUrl }}" class="px-5 py-2.5 rounded-xl border border-red-200 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors">Batalkan Permit</a>
            @else
                <a href="/ajukan-permit" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:border-gray-300">Ajukan Lagi</a>
            @endif
        </div>
    </div>
</body>
</html>
