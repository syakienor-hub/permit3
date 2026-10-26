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

    $status = $statusMap[$permit->status] ?? ['label' => $permit->status, 'class' => 'bg-gray-100 text-gray-600'];
@endphp

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
