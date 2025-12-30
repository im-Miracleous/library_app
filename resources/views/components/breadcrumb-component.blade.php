@props(['parent', 'current', 'middle' => null, 'middleLink' => null])

<div {{ $attributes->merge(['class' => 'flex items-center gap-2 text-sm text-slate-500 dark:text-white/60']) }}>
    <a href="{{ route('dashboard') }}" class="flex items-center hover:text-primary transition-colors">
        <span class="material-symbols-outlined text-base">home</span>
    </a>
    <span>/</span>
    <span>{{ $parent }}</span>

    @if($middle)
        <span>/</span>
        @if($middleLink)
            <a href="{{ $middleLink }}" class="hover:text-primary dark:hover:text-white transition-colors">{{ $middle }}</a>
        @else
            <span>{{ $middle }}</span>
        @endif
    @endif

    <span>/</span>
    <span class="font-bold text-primary dark:text-white">{{ $current }}</span>
</div>