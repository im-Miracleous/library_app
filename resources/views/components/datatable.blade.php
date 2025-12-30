@props([
    'data',
    'searchPlaceholder' => 'Cari data...',
    'searchId' => 'searchInput',
    'searchValue' => ''
])

<div class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-[#36271F] overflow-hidden animate-enter delay-100 shadow-sm dark:shadow-none transition-colors">

    <!-- Table Controls (Show limit, Search, Filter) -->
    <div class="p-4 border-b border-primary/20 dark:border-[#36271F] flex flex-col sm:flex-row justify-between items-center gap-4">

        <div class="flex items-center gap-2">
            <label class="text-xs font-bold text-slate-500 dark:text-white/60">Show</label>
            <select onchange="window.location.href = this.value"
                class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] text-xs rounded-lg p-2 focus:outline-none focus:ring-1 focus:ring-primary dark:focus:ring-accent dark:text-white cursor-pointer">
                @foreach([10, 25, 50, 100] as $limit)
                    <option value="{{ request()->fullUrlWithQuery(['limit' => $limit]) }}" {{ request('limit') == $limit ? 'selected' : '' }}>{{ $limit }}</option>
                @endforeach
            </select>
            <label class="text-xs font-bold text-slate-500 dark:text-white/60">entries</label>
        </div>

        <!-- Filter Slot -->
        @if(isset($filters))
            <div class="hidden sm:block">
                {{ $filters }}
            </div>
        @endif

        <!-- Search Bar -->
        <div class="relative w-full sm:w-64">
            <input type="text" id="{{ $searchId }}" placeholder="{{ $searchPlaceholder }}"
                value="{{ $searchValue }}"
                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg pl-10 pr-4 py-2 text-primary-dark dark:text-white text-sm focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none placeholder-primary-mid/60 dark:placeholder-white/40 shadow-sm transition-all">
            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400 dark:text-white/40">
                <span class="material-symbols-outlined text-lg">search</span>
            </div>
        </div>
    </div>

    <!-- Table Area -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="border-b border-primary/20 dark:border-border-dark text-slate-500 dark:text-white/40 text-xs uppercase tracking-wider bg-slate-50 dark:bg-white/5">
                    {{ $header }}
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#36271F] text-sm text-slate-600 dark:text-white/80">
                {{ $body }}
            </tbody>
        </table>
    </div>

    <!-- Footer Pagination & Info -->
    <div class="p-4 border-t border-slate-200 dark:border-border-dark flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50 dark:bg-white/5">
        <div class="text-xs text-slate-500 dark:text-white/60">
            Showing <span class="font-bold">{{ $data->firstItem() ?? 0 }}</span> to <span class="font-bold">{{ $data->lastItem() ?? 0 }}</span> of <span class="font-bold">{{ $data->total() }}</span> entries
        </div>
        <div class="flex gap-2">
            @if($data->onFirstPage())
                <button disabled
                    class="px-3 py-1 rounded-lg border border-slate-200 dark:border-[#36271F] text-slate-400 cursor-not-allowed">Previous</button>
            @else
                <a href="{{ $data->previousPageUrl() }}"
                    class="px-3 py-1 rounded-lg border border-slate-200 dark:border-[#36271F] text-primary hover:bg-primary/5 transition-colors">Previous</a>
            @endif

            @if($data->hasMorePages())
                <a href="{{ $data->nextPageUrl() }}"
                    class="px-3 py-1 rounded-lg border border-slate-200 dark:border-[#36271F] text-primary hover:bg-primary/5 transition-colors">Next</a>
            @else
                <button disabled
                    class="px-3 py-1 rounded-lg border border-slate-200 dark:border-[#36271F] text-slate-400 cursor-not-allowed">Next</button>
            @endif
        </div>
    </div>
</div>
