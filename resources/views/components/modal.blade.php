@props(['id', 'title', 'maxWidth' => 'lg'])

@php
    $maxWidthClass = match ($maxWidth) {
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
        '5xl' => 'sm:max-w-5xl',
        '6xl' => 'sm:max-w-6xl',
        '7xl' => 'sm:max-w-7xl',
        'full' => 'sm:max-w-full',
        default => 'sm:max-w-lg',
    };
@endphp

<div id="{{ $id }}" class="fixed inset-0 z-50 transition-all duration-300 opacity-0 pointer-events-none"
    aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity duration-300"
        onclick="closeModal('{{ $id }}')"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-4">
            <div
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-surface-dark border border-primary/20 dark:border-[#36271F] text-left shadow-2xl transition-all duration-300 scale-95 sm:my-8 sm:w-full {{ $maxWidthClass }}">
                <div
                    class="px-6 py-4 border-b border-primary/20 dark:border-[#36271F] flex justify-between items-center bg-surface dark:bg-[#1A1410]">
                    <h3 class="text-lg font-bold text-primary-dark dark:text-white flex items-center gap-2">
                        {{ $title_icon ?? '' }}
                        {{ $title }}
                    </h3>
                    <button onclick="closeModal('{{ $id }}')"
                        class="cursor-pointer text-slate-500 dark:text-white/60 hover:text-slate-700 dark:hover:text-white transition-colors"><span
                            class="material-symbols-outlined">close</span></button>
                </div>

                <div class="p-6 flex flex-col gap-5">
                    {{ $slot }}
                </div>

                @if(isset($footer))
                    <div
                        class="px-6 py-4 bg-gray-50 dark:bg-white/5 border-t border-primary/20 dark:border-[#36271F] flex justify-end gap-3">
                        {{ $footer }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>