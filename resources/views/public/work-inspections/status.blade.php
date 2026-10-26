<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Daftar Periksa — PT INKA (Persero)</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logok3.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .form-input { width: 100%; padding: 9px 12px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 0.95rem; color: #1f2937; background: #fff; }
        .form-input:focus { outline: none; border-color: #111d33; box-shadow: 0 0 0 3px rgba(17,29,51,0.08); }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col items-center px-4 py-8">
    <div class="w-full max-w-2xl mb-3">
        <a href="/" class="text-sm text-gray-500 hover:text-inka-navy">&larr; Kembali ke Beranda</a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm max-w-2xl w-full mx-auto p-6 sm:p-8">
        <div class="mb-5">
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-800">Cek Status Daftar Periksa</h1>
            <p class="text-sm text-gray-500 mt-1">Masukkan nomor daftar periksa dan nomor telepon yang Anda isi saat mengajukan.</p>
        </div>

        @if($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/cek-daftar-periksa" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">Nomor Daftar Periksa</label>
                <input type="text" name="no_periksa" value="{{ old('no_periksa', $inspection->no_periksa ?? '') }}" class="form-input" placeholder="001/DPIKBT/INKA/2026" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">No. Telepon</label>
                <input type="text" name="telepon" value="{{ old('telepon', $inspection->telepon ?? '') }}" class="form-input" placeholder="08xxxxxxxxxx" required>
            </div>
            <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-inka-navy text-white text-sm font-bold hover:opacity-90 transition-opacity">Cek Status</button>
        </form>

        @isset($inspection)
            @include('public.partials.status-work-inspection-result', ['inspection' => $inspection])
        @endisset
    </div>
</body>
</html>
