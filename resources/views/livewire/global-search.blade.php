<div class="relative w-full md:w-auto" x-data="{ focused: false }" @click.outside="$wire.open = false; focused = false">

    <!-- Input -->
    <div class="relative">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#6b6358] text-sm pointer-events-none">search</span>
        <input
            type="text"
            wire:model.live.debounce.300ms="query"
            placeholder="{{ auth()->user()?->isStudent() ? 'Cari kursus...' : 'Cari data...' }}"
            class="pl-10 pr-8 py-2 bg-[#ebe5d8] border-none rounded-full text-sm w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-[#8a5a31]/20 text-[#2b2721]"
            @focus="focused = true"
            @keydown.escape="$wire.open = false; focused = false"
        >
        @if($query)
        <button wire:click="clear" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#6b6358] hover:text-[#2b2721]">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
        @endif
    </div>

    <!-- Dropdown -->
    @if($open && count($results))
    <div class="absolute top-full mt-2 right-0 md:right-auto md:left-0 w-[calc(100vw-2rem)] max-w-80 bg-white rounded-2xl shadow-xl border border-[#bfb5a3]/15 overflow-hidden z-50">

        {{-- Group by type --}}
        @php
            $grouped = collect($results)->groupBy('type');
            $typeLabels = [
                'course'     => 'Kursus',
                'student'    => 'Siswa',
                'instructor' => 'Instruktur',
            ];
        @endphp

        @foreach($grouped as $type => $items)
        <div>
            <div class="px-4 pt-3 pb-1">
                <span class="text-[10px] font-bold uppercase tracking-widest text-[#6b6358]">{{ $typeLabels[$type] ?? $type }}</span>
            </div>
            @foreach($items as $item)
            <a href="{{ $item['url'] }}"
               wire:navigate
               class="flex items-center gap-3 px-4 py-2.5 hover:bg-[#ebe5d8] transition-colors group">
                <div class="w-8 h-8 rounded-full bg-[#ebe5d8] group-hover:bg-[#ddd4c2] flex items-center justify-center flex-shrink-0 transition-colors">
                    <span class="material-symbols-outlined text-[#6b6358] text-sm">{{ $item['icon'] }}</span>
                </div>
                <div class="overflow-hidden flex-1 min-w-0">
                    <p class="text-sm font-semibold text-[#2b2721] truncate">{{ $item['label'] }}</p>
                    <p class="text-xs text-[#6b6358] truncate">{{ $item['sub'] }}</p>
                </div>
                <span class="material-symbols-outlined text-[#bfb5a3] text-sm opacity-0 group-hover:opacity-100 transition-opacity">arrow_forward</span>
            </a>
            @endforeach
        </div>
        @endforeach

        <div class="px-4 py-2.5 border-t border-[#bfb5a3]/10">
            <p class="text-xs text-[#bfb5a3] text-center">{{ count($results) }} hasil ditemukan</p>
        </div>
    </div>
    @endif

    @if($open && !count($results) && strlen(trim($query)) >= 2)
    <div class="absolute top-full mt-2 left-0 w-72 bg-white rounded-2xl shadow-xl border border-[#bfb5a3]/15 p-6 text-center z-50">
        <span class="material-symbols-outlined text-[#bfb5a3] text-3xl">search_off</span>
        <p class="text-sm text-[#6b6358] mt-2">Tidak ada hasil untuk<br><span class="font-bold text-[#2b2721]">"{{ $query }}"</span></p>
    </div>
    @endif

</div>
