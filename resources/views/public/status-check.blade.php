<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Pengajuan — PT INKA (Persero)</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logok3.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col items-center px-4 py-8">
    <div class="w-full max-w-2xl mb-3">
        <a href="/" class="text-sm text-gray-500 hover:text-inka-navy">&larr; Kembali ke Beranda</a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm max-w-2xl w-full p-6 sm:p-8" x-data="{ tab: '{{ $jenis }}' }">
        <div class="mb-5">
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-800">Cek Status Pengajuan</h1>
            <p class="text-sm text-gray-500 mt-1">Pilih jenis pengajuan, lalu masukkan nomor dan nomor telepon yang Anda isi saat mengajukan.</p>
        </div>

        <div class="grid grid-cols-2 gap-1 rounded-xl bg-slate-100 p-1 mb-5" role="tablist">
            <button type="button" role="tab" @click="tab = 'permit'"
                :class="tab === 'permit' ? 'bg-white text-inka-navy shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="px-2 py-2 rounded-lg text-xs sm:text-sm font-semibold transition-colors">Permit</button>
            <button type="button" role="tab" @click="tab = 'hse-plan'"
                :class="tab === 'hse-plan' ? 'bg-white text-inka-navy shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="px-2 py-2 rounded-lg text-xs sm:text-sm font-semibold transition-colors">HSE Plan</button>
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

        {{-- Permit --}}
        <div x-show="tab === 'permit'" x-cloak>
            <form method="POST" action="/cek-status" class="space-y-4">
                @csrf
                <input type="hidden" name="jenis" value="permit">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Permit <span class="text-red-500">*</span></label>
                    <input type="text" name="no_permit" required maxlength="100"
                        value="{{ old('no_permit', $jenis === 'permit' && isset($permit) ? $permit->no_permit : '') }}"
                        placeholder="Contoh: WP-2026-1A2B3C4D"
                        class="w-full border-gray-300 rounded-xl focus:border-inka-navy focus:ring-inka-navy text-sm font-mono uppercase">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon <span class="text-red-500">*</span></label>
                    <input type="text" name="telepon" required maxlength="50"
                        value="{{ old('telepon', $jenis === 'permit' && isset($permit) ? $permit->telepon : '') }}"
                        placeholder="Nomor yang dipakai saat mengajukan"
                        class="w-full border-gray-300 rounded-xl focus:border-inka-navy focus:ring-inka-navy text-sm">
                </div>
                <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-inka-navy text-white text-sm font-semibold hover:opacity-90 transition-opacity">
                    Cek Status
                </button>
            </form>

            @if($jenis === 'permit' && isset($permit))
                @include('public.partials.status-permit-result', [
                    'permit' => $permit,
                    'successUrl' => $successUrl,
                    'cancelUrl' => $cancelUrl,
                ])
            @endif
        </div>

        {{-- HSE Plan --}}
        <div x-show="tab === 'hse-plan'" x-cloak>
            <form method="POST" action="/cek-status" class="space-y-4">
                @csrf
                <input type="hidden" name="jenis" value="hse-plan">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor HSE Plan <span class="text-red-500">*</span></label>
                    <input type="text" name="no_hse_plan" required maxlength="100"
                        value="{{ old('no_hse_plan', $jenis === 'hse-plan' && isset($hsePlan) ? $hsePlan->no_hse_plan : '') }}"
                        placeholder="Contoh: 001/HSEPLAN/HSE/2026"
                        class="w-full border-gray-300 rounded-xl focus:border-inka-navy focus:ring-inka-navy text-sm font-mono uppercase">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon <span class="text-red-500">*</span></label>
                    <input type="text" name="telepon" required maxlength="50"
                        value="{{ old('telepon', $jenis === 'hse-plan' && isset($hsePlan) ? $hsePlan->telepon : '') }}"
                        placeholder="Nomor yang dipakai saat mengajukan"
                        class="w-full border-gray-300 rounded-xl focus:border-inka-navy focus:ring-inka-navy text-sm">
                </div>
                <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-inka-navy text-white text-sm font-semibold hover:opacity-90 transition-opacity">
                    Cek Status
                </button>
            </form>

            @if($jenis === 'hse-plan' && isset($hsePlan))
                @include('public.partials.status-hse-plan-result', ['hsePlan' => $hsePlan])
            @endif
        </div>

    </div>
</body>
</html>
