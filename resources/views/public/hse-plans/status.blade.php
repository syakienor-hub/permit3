<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status HSE Plan — PT INKA (Persero)</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logok3.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col items-center px-4 py-8">
    <div class="w-full max-w-2xl mb-3">
        <a href="/" class="text-sm text-gray-500 hover:text-inka-navy">&larr; Kembali ke Beranda</a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm max-w-2xl w-full p-6 sm:p-8">
        <div class="mb-5">
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-800">Cek Status HSE Plan</h1>
            <p class="text-sm text-gray-500 mt-1">Masukkan nomor HSE Plan dan nomor telepon yang Anda isi saat mengajukan.</p>
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

        <form method="POST" action="/cek-hse-plan" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor HSE Plan <span class="text-red-500">*</span></label>
                <input type="text" name="no_hse_plan" required maxlength="50"
                    value="{{ old('no_hse_plan', $hsePlan->no_hse_plan ?? '') }}"
                    placeholder="Contoh: 001/HSEPLAN/HSE/2026"
                    class="w-full border-gray-300 rounded-xl focus:border-inka-navy focus:ring-inka-navy text-sm font-mono uppercase">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon <span class="text-red-500">*</span></label>
                <input type="text" name="telepon" required maxlength="50"
                    value="{{ old('telepon') }}"
                    placeholder="Nomor yang dipakai saat mengajukan"
                    class="w-full border-gray-300 rounded-xl focus:border-inka-navy focus:ring-inka-navy text-sm">
            </div>
            <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-inka-navy text-white text-sm font-semibold hover:opacity-90 transition-opacity">Cek Status</button>
        </form>

        @if(isset($hsePlan))
            @include('public.partials.status-hse-plan-result', ['hsePlan' => $hsePlan])
        @endif
    </div>
</body>
</html>
