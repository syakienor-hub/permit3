@props(['site' => null])

@if($site === 'Banyuwangi')
    <span class="badge bg-emerald-50 text-emerald-700 border-emerald-200/70">
        Banyuwangi
    </span>
@else
    <span class="badge bg-indigo-50 text-indigo-700 border-indigo-200/70">
        {{ $site ?? 'Madiun' }}
    </span>
@endif
