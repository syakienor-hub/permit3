@props(['tipe' => 'Internal'])

@if($tipe === 'Eksternal')
    <span class="badge bg-blue-50 text-blue-700 border-blue-200/70">
        Eksternal
    </span>
@else
    <span class="badge bg-gray-100 text-gray-600 border-gray-200">
        Internal
    </span>
@endif
