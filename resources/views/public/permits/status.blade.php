<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Permit — PT INKA (Persero)</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logok3.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col items-center px-4 py-8">
    @php
        $statusMap = [
            'Draft'          => ['label' => 'Draft', 'class' => 'bg-gray-100 text-gray-600'],
            'Submitted'      => ['label' => 'Menunggu Review', 'class' => 'bg-orange-100 text-orange-700'],
            'Review Staff'   => ['label' => 'Menunggu Review Staff HSE', 'class' => 'bg-orange-100 text-orange-700'],
            'Review Manager' => ['label' => 'Menunggu Review Manager HSE', 'class' => 'bg-orange-100 text-orange-700'],
            'Revision'       => ['label' => 'Perlu Revisi', 'class' => 'bg-red-100 text-red-700'],
            'Active'         => ['label' => 'Aktif', 'class' => 'bg-green-100 text-green-700'],
            'Closed'         => ['label' => 'Selesai / Ditutup', 'class' => 'bg-slate-100 text-slate-600'],
            'Cancelled'      => ['label' => 'Dibatalkan', 'class' => 'bg-red-600 text-white'],
        ];

        $status = isset($permit) ? ($statusMap[$permit->status] ?? ['label' => $permit->status, 'class' => 'bg-gray-100 text-gray-600']) : null;
    @endphp

    <div class="w-full max-w-2xl mb-3">
        <a href="/" class="text-sm text-gray-500 hover:text-inka-navy">← Kembali ke Beranda</a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm max-w-2xl w-full p-6 sm:p-8">
        <div class="mb-5">
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-800">Cek Status Permit</h1>
            <p class="text-sm text-gray-500 mt-1">Masukkan nomor permit dan nomor telepon yang Anda isi saat mengajukan.</p>
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

        <form method="POST" action="/cek-permit" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Permit <span class="text-red-500">*</span></label>
                <input type="text" name="no_permit" required maxlength="50"
                    value="{{ old('no_permit', $permit->no_permit ?? '') }}"
                    placeholder="Contoh: WP-2026-1A2B3C4D"
                    class="w-full border-gray-300 rounded-xl focus:border-inka-navy focus:ring-inka-navy text-sm font-mono uppercase">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon <span class="text-red-500">*</span></label>
                <input type="text" name="telepon" required maxlength="50"
                    value="{{ old('telepon') }}"
                    placeholder="Nomor yang dipakai saat mengajukan permit"
                    class="w-full border-gray-300 rounded-xl focus:border-inka-navy focus:ring-inka-navy text-sm">
            </div>

            <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-inka-navy text-white text-sm font-semibold hover:opacity-90 transition-opacity">
                Cek Status
            </button>
        </form>

        @if(isset($permit))
            <div class="mt-6 pt-6 border-t border-gray-100">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div>
                        <p class="text-xs text-gray-400">Nomor Permit</p>
                        <p class="text-lg font-bold text-inka-navy">{{ $permit->no_permit }}</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold {{ $status['class'] }}">
                        {{ $status['label'] }}
                    </span>
                </div>

                @if($permit->status === 'Cancelled')
                    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-4 mb-4">
                        <h2 class="font-bold text-red-800">Permit Dibatalkan</h2>
                        <p class="text-sm text-red-700 mt-1">Izin kerja ini telah dicabut dan tidak berlaku lagi.</p>
                        @if($permit->cancelled_at)
                            <p class="text-xs text-red-600 mt-2">Dibatalkan pada {{ $permit->cancelled_at->format('d/m/Y H:i') }}</p>
                        @endif
                        @if($permit->cancellation_reason)
                            <p class="text-sm text-red-700 mt-2"><strong>Alasan:</strong> {{ $permit->cancellation_reason }}</p>
                        @endif
                    </div>
                @endif

                <div class="rounded-xl border border-gray-100 bg-gray-50/60 px-4 py-3 mb-4 text-sm">
                    <div class="grid sm:grid-cols-2 gap-2">
                        <div><span class="text-gray-400">Pekerjaan:</span> <span class="font-semibold text-gray-700">{{ $permit->nama_pekerjaan }}</span></div>
                        <div><span class="text-gray-400">Site:</span> <span class="font-semibold text-gray-700">{{ $permit->site ?? '-' }}</span></div>
                        <div><span class="text-gray-400">Kontraktor:</span> <span class="font-semibold text-gray-700">{{ $permit->kontraktor }}</span></div>
                        <div><span class="text-gray-400">Divisi:</span> <span class="font-semibold text-gray-700">{{ $permit->divisi_pengaju ?? '-' }}</span></div>
                        <div><span class="text-gray-400">Mulai:</span> <span class="font-semibold text-gray-700">{{ $permit->tanggal_mulai?->format('d/m/Y') ?? '-' }}</span></div>
                        <div><span class="text-gray-400">Selesai:</span> <span class="font-semibold text-gray-700">{{ $permit->tanggal_selesai?->format('d/m/Y') ?? '-' }}</span></div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-2.5 sm:gap-3">
                    <a href="{{ $successUrl }}" class="px-5 py-2.5 rounded-xl bg-inka-navy text-white text-sm font-semibold text-center hover:opacity-90 transition-opacity">
                        Lihat Detail Permit
                    </a>
                    @if(!in_array($permit->status, ['Closed', 'Cancelled']))
                        <a href="{{ $cancelUrl }}" class="px-5 py-2.5 rounded-xl border border-red-200 text-sm font-semibold text-red-600 text-center hover:bg-red-50 transition-colors">
                            Batalkan Permit
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</body>
</html>
