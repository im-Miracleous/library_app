@php
    $themes = [
        'blue' => [
            'icon_bg' => 'bg-blue-50 dark:bg-blue-500/10',
            'icon_text' => 'text-blue-600 dark:text-blue-500',
            'badge_bg' => 'bg-blue-100 dark:bg-blue-500/10',
            'badge_text' => 'text-blue-600 dark:text-blue-500',
        ],
        'purple' => [
            'icon_bg' => 'bg-purple-50 dark:bg-purple-500/10',
            'icon_text' => 'text-purple-600 dark:text-purple-500',
            'badge_bg' => 'bg-purple-100 dark:bg-purple-500/10',
            'badge_text' => 'text-purple-600 dark:text-purple-500',
        ],
        'orange' => [
            'icon_bg' => 'bg-orange-50 dark:bg-orange-500/10',
            'icon_text' => 'text-orange-600 dark:text-orange-500',
            'badge_bg' => 'bg-orange-100 dark:bg-orange-500/10',
            'badge_text' => 'text-orange-600 dark:text-orange-500',
        ],
        'red' => [
            'icon_bg' => 'bg-red-50 dark:bg-red-500/10',
            'icon_text' => 'text-red-600 dark:text-red-500',
            'badge_bg' => 'bg-red-100 dark:bg-red-500/10',
            'badge_text' => 'text-red-600 dark:text-red-500',
        ],
        'green' => [
            'icon_bg' => 'bg-green-50 dark:bg-green-500/10',
            'icon_text' => 'text-green-600 dark:text-green-500',
            'badge_bg' => 'bg-green-100 dark:bg-green-500/10',
            'badge_text' => 'text-green-600 dark:text-green-500',
        ],
        'primary' => [ // Fallback
            'icon_bg' => 'bg-primary/10 dark:bg-accent/10',
            'icon_text' => 'text-primary dark:text-accent',
            'badge_bg' => 'bg-primary/20 dark:bg-accent/20',
            'badge_text' => 'text-primary dark:text-accent',
        ]
    ];

    $theme = $themes[$color] ?? $themes['primary'];
@endphp

<div class="bg-white dark:bg-surface-dark p-6 rounded-2xl border border-primary/20 dark:border-border-dark hover:border-primary/40 dark:hover:border-accent/50 hover:shadow-md hover:-translate-y-1 animate-enter shadow-sm dark:shadow-none transition-all duration-300 cursor-default">
    <div class="flex items-center justify-start gap-4 mb-4">
        <div class="size-14 rounded-2xl {{ $theme['icon_bg'] }} flex items-center justify-center {{ $theme['icon_text'] }}">
            <span class="material-symbols-outlined text-3xl">{{ $icon }}</span>
        </div>
        <span class="text-base font-bold {{ $theme['badge_text'] }} {{ $theme['badge_bg'] }} px-3 py-1.5 rounded-xl">
            {{ $title }}
        </span>
    </div>
    <h3 class="text-3xl font-bold text-primary-dark dark:text-white">
        {{ $value }}
    </h3>
    <p class="text-primary-mid dark:text-white/40 text-sm font-medium mt-1">{{ $desc }}</p>
</div>