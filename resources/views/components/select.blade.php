@props(['name', 'label' => null, 'options' => [], 'selected' => '', 'placeholder' => 'Pilih...', 'required' => false, 'id' => null, 'icon' => null])

@php
    $id = $id ?? $name;
@endphp

<div class="flex flex-col gap-2">
    @if($label)
        <label for="{{ $id }}" class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">
            {{ $label }}
        </label>
    @endif
    <div class="relative">
        @if($icon)
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary/60 dark:text-white/40 text-[20px] pointer-events-none">{{ $icon }}</span>
        @endif
        
        <select name="{{ $name }}" id="{{ $id }}"
            class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg py-2.5 pr-10 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none appearance-none w-full cursor-pointer {{ $icon ? 'pl-10' : 'px-4' }} {{ $attributes->get('class') }}"
            {{ $required ? 'required' : '' }} {{ $attributes->except('class') }}>
            @if($placeholder)
                <option value="" {{ $required ? 'disabled' : '' }} {{ $selected == '' ? 'selected' : '' }}>{{ $placeholder }}</option>
            @endif

            {{ $slot }}

            @foreach($options as $key => $value)
                @php
                    $isSelected = (string) $key === (string) $selected;
                @endphp
                <option value="{{ $key }}" {{ $isSelected ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </select>
        <div
            class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500 dark:text-white/60">
            <span class="material-symbols-outlined text-[20px]">expand_more</span>
        </div>
    </div>
    @error($name)
        <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
    @enderror
</div>