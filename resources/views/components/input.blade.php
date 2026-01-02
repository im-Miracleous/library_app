@props(['name', 'label', 'type' => 'text', 'placeholder' => '', 'required' => false, 'value' => '', 'id' => null])

@php
    $id = $id ?? $name;
@endphp

<div class="flex flex-col gap-2">
    <label for="{{ $id }}" class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">
        {{ $label }}
    </label>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}"
        class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
        placeholder="{{ $placeholder }}" value="{{ $value }}" {{ $required ? 'required' : '' }} {{ $attributes }}>
    @error($name)
        <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
    @enderror
</div>