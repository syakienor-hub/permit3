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
    @php
        $statusMap = [
            \App\Models\WorkInspection::STATUS_PENDING => ['label' => 'Menunggu Verifikasi Staff HSE', 'class' => 'bg-orange-100 text-orange-700'],
            \App\Models\WorkInspection::STATUS_APPROVED => ['label' => 'Disetujui', 'class' => 'bg-green-100 text-green-700'],
            \App\Models\WorkInspection::STATUS_REJECTED => ['label' => 'Ditolak', 'class' => 'bg-red-600 text-white'],
        ];
        $status = isset($inspection) ? ($statusMap[$inspection->status] ?? ['label' => $inspection->status, 'class' => 'bg-gray-100 text-gray-600']) : null;
    @endphp

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
            <div class="mt-7 border-t border-gray-100 pt-6">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-mono font-bold text-inka-navy">{{ $inspection->no_periksa }}</span>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $status['class'] }}">{{ $status['label'] }}</span>
                </div>
                <div class="mt-4 grid sm:grid-cols-2 gap-3 text-sm">
                    <div><p class="text-gray-400 text-xs">Pekerjaan</p><p class="font-semibold text-gray-700">{{ $inspection->nama_pekerjaan }}</p></div>
                    <div><p class="text-gray-400 text-xs">Kontraktor</p><p class="font-semibold text-gray-700">{{ $inspection->kontraktor }}</p></div>
                    <div><p class="text-gray-400 text-xs">Site</p><p class="font-semibold text-gray-700">{{ $inspection->site }}</p></div>
                    <div><p class="text-gray-400 text-xs">Lokasi</p><p class="font-semibold text-gray-700">{{ $inspection->lokasi }}</p></div>
                    <div><p class="text-gray-400 text-xs">Mulai Tanggal</p><p class="font-semibold text-gray-700">{{ $inspection->tanggal_mulai?->format('d/m/Y') ?? '—' }}</p></div>
                    <div><p class="text-gray-400 text-xs">Diajukan</p><p class="font-semibold text-gray-700">{{ ($inspection->submitted_at ?? $inspection->created_at)?->format('d/m/Y H:i') }}</p></div>
                </div>

                @if($inspection->status === \App\Models\WorkInspection::STATUS_REJECTED && $inspection->rejection_reason)
                    <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <p class="font-semibold">Alasan ditolak:</p>
                        <p class="mt-1">{{ $inspection->rejection_reason }}</p>
                    </div>
                @endif

                @if($inspection->status === \App\Models\WorkInspection::STATUS_APPROVED)
                    <div class="mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        <p class="font-semibold">Disetujui oleh {{ $inspection->safety_officer_nama ?? 'Staff HSE' }}</p>
                        <p class="mt-1">Nomor ini sudah dapat dipakai pada form <a href="/ajukan-permit" class="font-bold underline">Safety Permit</a>.</p>
                    </div>
                @endif

                <details class="mt-5 rounded-xl border border-gray-100 bg-gray-50/60 px-4 py-3">
                    <summary class="text-sm font-semibold text-gray-700 cursor-pointer">Lihat jawaban daftar periksa</summary>
                    <div class="mt-3 space-y-2 text-sm">
                        @foreach(\App\Models\WorkInspection::ANSWER_LABELS as $key => $label)
                            <div class="flex items-start justify-between gap-3 border-b border-gray-100 pb-2 last:border-0">
                                <span class="text-gray-600">{{ $label }}</span>
                                <span class="shrink-0 font-bold {{ $inspection->answer($key) ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $inspection->answer($key) ? 'YA' : 'TIDAK' }}
                                </span>
                            </div>
                        @endforeach
                        @if(!empty($inspection->answers['izin_lain_ket']))
                            <p class="text-gray-500">Izin lain: <span class="font-semibold text-gray-700">{{ $inspection->answers['izin_lain_ket'] }}</span></p>
                        @endif
                        @if(!empty($inspection->answers['apd_khusus_ket']))
                            <p class="text-gray-500">APD khusus: <span class="font-semibold text-gray-700">{{ $inspection->answers['apd_khusus_ket'] }}</span></p>
                        @endif
                    </div>
                </details>
            </div>
        @endisset
    </div>
</body>
</html>
