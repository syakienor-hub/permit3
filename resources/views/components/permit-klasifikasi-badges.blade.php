@props(['permit' => null, 'items' => null])

@php
    $labels = [];

    if ($permit) {
        if ($permit->relationLoaded('classifications') && $permit->classifications->isNotEmpty()) {
            $labels = $permit->classifications->pluck('name', 'code')->toArray();
        } elseif (!empty($permit->klasifikasi_pekerjaan) && is_array($permit->klasifikasi_pekerjaan)) {
            $labels = $permit->klasifikasi_pekerjaan;
        }
    } elseif ($items) {
        $labels = is_array($items) ? $items : [$items];
    }

    $colorMap = [
        'panas'          => 'bg-orange-50 text-orange-700 border-orange-200/70',
        'ketinggian'     => 'bg-blue-50 text-blue-700 border-blue-200/70',
        'ruang_terbatas' => 'bg-purple-50 text-purple-700 border-purple-200/70',
        'galian'         => 'bg-amber-50 text-amber-800 border-amber-200/70',
        'tegangan_tinggi'=> 'bg-rose-50 text-rose-700 border-rose-200/70',
        'radiasi'        => 'bg-emerald-50 text-emerald-700 border-emerald-200/70',
    ];
    $nameMap = [
        'panas'          => 'Pekerjaan Panas',
        'ketinggian'     => 'Pekerjaan Ketinggian',
        'ruang_terbatas' => 'Ruang Terbatas',
        'galian'         => 'Pekerjaan Galian',
        'tegangan_tinggi'=> 'Pekerjaan Tegangan Tinggi',
        'radiasi'        => 'Radiasi',
    ];
@endphp

<div class="flex flex-wrap gap-1.5 max-w-[250px]">
    @if(empty($labels))
        <span class="text-gray-400 text-xs italic">—</span>
    @else
        @foreach($labels as $key => $val)
            @php
                $code = is_string($key) && !is_numeric($key) ? $key : (is_string($val) ? $val : '');
                $displayName = $nameMap[$code] ?? (is_string($val) && !isset($nameMap[$val]) ? $val : ($nameMap[$val] ?? ucwords(str_replace('_', ' ', $code))));
                $color = $colorMap[$code] ?? 'bg-slate-50 text-slate-700 border-slate-200';
            @endphp
            <span class="badge {{ $color }}">
                {{ $displayName }}
            </span>
        @endforeach
    @endif
</div>
