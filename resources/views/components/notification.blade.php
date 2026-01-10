@props(['type' => 'success', 'message' => '', 'detailUrl' => null])

@php
    $id = 'notification-' . uniqid();
    $colors = [
        'success' => [
            'bg' => 'bg-white dark:bg-slate-800',
            'border' => 'border-l-4 border-emerald-500',
            'icon' => 'text-emerald-500',
            'icon_name' => 'check_circle',
            'timer' => 'bg-emerald-500',
        ],
        'error' => [
            'bg' => 'bg-white dark:bg-slate-800',
            'border' => 'border-l-4 border-red-500',
            'icon' => 'text-red-500',
            'icon_name' => 'error',
            'timer' => 'bg-red-500',
        ],
    ];
    $style = $colors[$type] ?? $colors['success'];
@endphp

<div id="{{ $id }}"
     {{ $attributes->merge(['class' => "fixed right-5 z-50 flex flex-col w-full max-w-sm overflow-hidden transition-all duration-500 ease-out transform translate-x-[150%] shadow-lg rounded-lg {$style['bg']} {$style['border']}"]) }}
     style="{{ $attributes->get('style') }}"
     role="alert">
    
    <div class="p-4 flex items-start gap-3">
        <span class="material-symbols-outlined text-2xl {{ $style['icon'] }} shrink-0">
            {{ $style['icon_name'] }}
        </span>
        <div class="flex-1 pt-0.5">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-1">
                {{ $type === 'success' ? 'Berhasil' : 'Gagal' }}
            </h3>
            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                {{ $message }}
            </p>
            @if ($detailUrl)
                <a href="{{ $detailUrl }}" class="mt-2 inline-block text-xs font-bold text-emerald-600 hover:underline">
                    Lihat Detail
                </a>
            @endif
        </div>
        <button onclick="closeNotification('{{ $id }}')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>
    </div>

    {{-- Timer Bar --}}
    <div class="w-full h-1 bg-slate-100 dark:bg-slate-700">
        <div id="{{ $id }}-timer" class="h-full {{ $style['timer'] }}" style="width: 100%"></div>
    </div>
</div>

<script>
    (function() {
        const id = "{{ $id }}";
        const card = document.getElementById(id);
        const timer = document.getElementById(id + '-timer');
        
        if (card && timer) {
            // Animasi Masuk: Slide Left
            setTimeout(() => {
                card.classList.remove('translate-x-[150%]');
                card.classList.add('translate-x-0');
            }, 100);

            // Animasi Timer
            timer.style.transition = 'width 8s linear';
            setTimeout(() => {
                timer.style.width = '0%';
            }, 200);

            // Setup Auto Close
            const autoClose = setTimeout(() => {
                closeNotification(id);
            }, 8200); 

            // Expose logic to cancel timer if needed (optional)
            card.dataset.timerId = autoClose;
        }
    })();

    // Fungsi global
    if (typeof window.closeNotification !== 'function') {
        window.closeNotification = function(id) {
            const card = document.getElementById(id);
            if (!card) return;

            // Clear timeout if exists
            if (card.dataset.timerId) {
                clearTimeout(parseInt(card.dataset.timerId));
            }
            
            // Animasi Keluar: Slide Right
            card.classList.remove('translate-x-0');
            card.classList.add('translate-x-[150%]');

            // Hapus dari DOM
            setTimeout(() => {
                card.remove();
            }, 600);
        }
    }
</script>
