@props(['status', 'label' => null, 'badgeClass' => null])

@php
    $toneMap = [
        'Draft'          => 'bg-gray-100 text-gray-600 border-gray-200',
        'Menunggu Daftar Periksa' => 'bg-amber-50 text-amber-700 border-amber-200/70',
        'Submitted'      => 'bg-blue-50 text-blue-700 border-blue-200/70',
        'Review Staff'   => 'bg-orange-50 text-orange-700 border-orange-200/70',
        'Review Manager' => 'bg-orange-50 text-orange-700 border-orange-200/70',
        'Revision'       => 'bg-red-50 text-red-700 border-red-200/70',
        'Active'         => 'bg-green-50 text-green-700 border-green-200/70',
        'Closed'         => 'bg-slate-50 text-slate-700 border-slate-200',
        'Cancelled'      => 'bg-red-600 text-white border-red-600',
    ];

    $tone = $badgeClass ?? ($toneMap[$status] ?? 'bg-gray-100 text-gray-600 border-gray-200');
    $text = $label ?? (str_starts_with((string) $status, 'Review') ? 'Menunggu ' . $status : $status);
@endphp

<span {{ $attributes->merge(['class' => 'badge ' . $tone]) }}>{{ $text }}</span>
