@props(['field', 'label', 'hint' => null])

@php
    $name = 'answers[' . $field . ']';
    $oldValue = old('answers.' . $field);
@endphp

<div class="py-4 border-b border-gray-100 last:border-0">
    <p class="text-sm font-semibold text-gray-700 leading-relaxed">
        {{ $label }} <span class="text-red-500">*</span>
    </p>
    @if($hint)
        <p class="text-xs text-gray-400 mt-0.5">{{ $hint }}</p>
    @endif
    <div class="flex items-center gap-3 mt-3">
        <label class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 cursor-pointer hover:border-inka-navy/40">
            <input type="radio" name="{{ $name }}" value="1" class="accent-[#111d33]" @checked((string) $oldValue === '1') required>
            YA
        </label>
        <label class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 cursor-pointer hover:border-inka-navy/40">
            <input type="radio" name="{{ $name }}" value="0" class="accent-[#111d33]" @checked((string) $oldValue === '0')>
            TIDAK
        </label>
    </div>
</div>
