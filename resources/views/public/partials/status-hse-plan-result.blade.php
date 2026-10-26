@php
    $statusMap = [
        'Review Manager' => ['label' => 'Menunggu Persetujuan Manager HSE', 'class' => 'bg-orange-100 text-orange-700'],
        'Approved' => ['label' => 'Disetujui', 'class' => 'bg-green-100 text-green-700'],
        'Rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-600 text-white'],
    ];
    $status = $statusMap[$hsePlan->status] ?? ['label' => $hsePlan->status, 'class' => 'bg-gray-100 text-gray-600'];
@endphp

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
