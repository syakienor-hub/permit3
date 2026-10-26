@php
    $statusMap = [
        \App\Models\WorkInspection::STATUS_PENDING => ['label' => 'Menunggu Verifikasi Staff HSE', 'class' => 'bg-orange-100 text-orange-700'],
        \App\Models\WorkInspection::STATUS_APPROVED => ['label' => 'Disetujui', 'class' => 'bg-green-100 text-green-700'],
        \App\Models\WorkInspection::STATUS_REJECTED => ['label' => 'Ditolak', 'class' => 'bg-red-600 text-white'],
    ];
    $status = $statusMap[$inspection->status] ?? ['label' => $inspection->status, 'class' => 'bg-gray-100 text-gray-600'];
@endphp

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
