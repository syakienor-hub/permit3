<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status HSE Plan — INKA Madiun</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logok3.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center px-4 py-8">
    @php
        $statusMap = [
            'Review Manager' => ['label' => 'Menunggu Persetujuan Manager HSE', 'class' => 'bg-orange-100 text-orange-700'],
            'Approved' => ['label' => 'Disetujui', 'class' => 'bg-green-100 text-green-700'],
            'Rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-600 text-white'],
        ];
        $status = isset($hsePlan) ? ($statusMap[$hsePlan->status] ?? ['label' => $hsePlan->status, 'class' => 'bg-gray-100 text-gray-600']) : null;
    @endphp

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm max-w-2xl w-full p-6 sm:p-8">
        <div class="mb-5">
            <a href="/" class="text-sm text-gray-500 hover:text-inka-navy">&larr; Kembali ke Beranda</a>
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-800 mt-2">Cek Status HSE Plan</h1>
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
            <div class="mt-6 pt-6 border-t border-gray-100">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div>
                        <p class="text-xs text-gray-400">Nomor HSE Plan</p>
                        <p class="text-lg font-bold text-inka-navy font-mono">{{ $hsePlan->no_hse_plan }}</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold {{ $status['class'] }}">{{ $status['label'] }}</span>
                </div>

                @if($hsePlan->status === 'Rejected')
                    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-4 mb-4">
                        <h2 class="font-bold text-red-800">HSE Plan Ditolak</h2>
                        @if($hsePlan->rejection_reason)
                            <p class="text-sm text-red-700 mt-1"><strong>Alasan:</strong> {{ $hsePlan->rejection_reason }}</p>
                        @endif
                        @if($hsePlan->reviewed_at)
                            <p class="text-xs text-red-600 mt-2">Diproses pada {{ $hsePlan->reviewed_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                @elseif($hsePlan->status === 'Approved')
                    <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-4 mb-4">
                        <h2 class="font-bold text-green-800">HSE Plan Disetujui</h2>
                        @if($hsePlan->reviewed_at)
                            <p class="text-xs text-green-700 mt-2">Disetujui pada {{ $hsePlan->reviewed_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                @endif

                <div class="rounded-xl border border-gray-100 bg-gray-50/60 px-4 py-3 mb-4 text-sm">
                    <div class="grid sm:grid-cols-2 gap-2">
                        <div><span class="text-gray-400">Pekerjaan:</span> <span class="font-semibold text-gray-700">{{ $hsePlan->deskripsi_pekerjaan }}</span></div>
                        <div><span class="text-gray-400">Site:</span> <span class="font-semibold text-gray-700">{{ $hsePlan->site }}</span></div>
                        <div><span class="text-gray-400">Kontraktor:</span> <span class="font-semibold text-gray-700">{{ $hsePlan->nama_kontraktor }}</span></div>
                        <div><span class="text-gray-400">Divisi:</span> <span class="font-semibold text-gray-700">{{ $hsePlan->divisi_pengaju }}</span></div>
                        <div><span class="text-gray-400">Mulai:</span> <span class="font-semibold text-gray-700">{{ $hsePlan->tanggal_mulai?->format('d/m/Y') ?? '-' }} {{ $hsePlan->jam_mulai ?: '' }}</span></div>
                        <div><span class="text-gray-400">Selesai:</span> <span class="font-semibold text-gray-700">{{ $hsePlan->tanggal_selesai?->format('d/m/Y') ?? '-' }} {{ $hsePlan->jam_selesai ?: '' }}</span></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
