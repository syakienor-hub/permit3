
@php
    $statusMap = [
        'Review Manager' => [
            'label' => 'Menunggu Persetujuan Manager HSE',
            'class' => 'bg-orange-100 text-orange-700'
        ],
        'Approved' => [
            'label' => 'Disetujui',
            'class' => 'bg-green-100 text-green-700'
        ],
        'Rejected' => [
            'label' => 'Ditolak',
            'class' => 'bg-red-600 text-white'
        ],
        'Perlu Revisi' => [
            'label' => 'Perlu Revisi',
            'class' => 'bg-amber-100 text-amber-700'
        ],
    ];

    $status = $statusMap[$hsePlan->status]
        ?? [
            'label' => $hsePlan->status,
            'class' => 'bg-gray-100 text-gray-600'
        ];
@endphp

<div class="mt-6 pt-6 border-t border-gray-100">

    {{-- NOMOR DAN STATUS HSE PLAN --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <p class="text-xs text-gray-400">Nomor HSE Plan</p>

            <p class="text-lg font-bold text-inka-navy font-mono">
                {{ $hsePlan->no_hse_plan }}
            </p>
        </div>

        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold {{ $status['class'] }}">
            {{ $status['label'] }}
        </span>
    </div>

    {{-- STATUS DITOLAK (DATA LAMA) --}}
    @if($hsePlan->status === 'Rejected')
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-4 mb-4">
            <h2 class="font-bold text-red-800">
                HSE Plan Ditolak
            </h2>

            @if($hsePlan->rejection_reason)
                <p class="text-sm text-red-700 mt-1">
                    <strong>Alasan:</strong>
                    {{ $hsePlan->rejection_reason }}
                </p>
            @endif

            @if($hsePlan->reviewed_at)
                <p class="text-xs text-red-600 mt-2">
                    Diproses pada
                    {{ $hsePlan->reviewed_at->format('d/m/Y H:i') }}
                </p>
            @endif
        </div>

    {{-- STATUS DISETUJUI --}}
    @elseif($hsePlan->status === 'Approved')
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-4 mb-4">
            <h2 class="font-bold text-green-800">
                HSE Plan Disetujui
            </h2>

            @if($hsePlan->reviewed_at)
                <p class="text-xs text-green-700 mt-2">
                    Disetujui pada
                    {{ $hsePlan->reviewed_at->format('d/m/Y H:i') }}
                </p>
            @endif
        </div>
    @endif

    {{-- INFORMASI HSE PLAN --}}
    <div class="rounded-xl border border-gray-100 bg-gray-50/60 px-4 py-3 mb-4 text-sm">
        <div class="grid sm:grid-cols-2 gap-2">
            <div>
                <span class="text-gray-400">Pekerjaan:</span>
                <span class="font-semibold text-gray-700">
                    {{ $hsePlan->deskripsi_pekerjaan }}
                </span>
            </div>

            <div>
                <span class="text-gray-400">Site:</span>
                <span class="font-semibold text-gray-700">
                    {{ $hsePlan->site }}
                </span>
            </div>

            <div>
                <span class="text-gray-400">Kontraktor:</span>
                <span class="font-semibold text-gray-700">
                    {{ $hsePlan->nama_kontraktor }}
                </span>
            </div>

            <div>
                <span class="text-gray-400">Divisi:</span>
                <span class="font-semibold text-gray-700">
                    {{ $hsePlan->divisi_pengaju }}
                </span>
            </div>

            <div>
                <span class="text-gray-400">Mulai:</span>
                <span class="font-semibold text-gray-700">
                    {{ $hsePlan->tanggal_mulai?->format('d/m/Y') ?? '-' }}
                    {{ $hsePlan->jam_mulai ?: '' }}
                </span>
            </div>

            <div>
                <span class="text-gray-400">Selesai:</span>
                <span class="font-semibold text-gray-700">
                    {{ $hsePlan->tanggal_selesai?->format('d/m/Y') ?? '-' }}
                    {{ $hsePlan->jam_selesai ?: '' }}
                </span>
            </div>
        </div>
    </div>

    {{-- CATATAN REVISI MANAGER HSE --}}
    @if(
        $hsePlan->status === \App\Models\HsePlan::STATUS_REVISION
        && filled($hsePlan->rejection_reason)
    )
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-4 mb-4">

            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-amber-600 shrink-0"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M8 10h8m-8 4h5m-9 7 3.5-3.5H19a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 001 1.732z"
                    />
                </svg>

                <h3 class="text-sm font-semibold text-amber-900">
                    Catatan Revisi Manager HSE
                </h3>
            </div>

            <p class="text-sm text-amber-900 leading-relaxed whitespace-pre-line">{{ $hsePlan->rejection_reason }}</p>

            <p class="text-xs text-amber-700 mt-3">
                Silakan menindaklanjuti catatan revisi tersebut sebelum HSE Plan dapat disetujui.
            </p>

            @if($hsePlan->reviewed_at)
                <p class="text-xs text-amber-700 mt-2">
                    Catatan diberikan pada
                    {{ $hsePlan->reviewed_at->format('d/m/Y H:i') }}
                </p>
            @endif

        </div>
    @endif

</div>
