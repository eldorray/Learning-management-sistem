<div class="relative" x-data="{ open: @entangle('open') }" @click.outside="open = false; $wire.open = false">

    {{-- Bell Button --}}
    <button wire:click="toggle"
            class="p-2 rounded-full hover:bg-[#e5e9eb] transition-colors relative"
            :class="open ? 'bg-[#e5e9eb]' : ''">
        <span class="material-symbols-outlined text-[#595c5e] text-xl"
              style="{{ $this->unreadCount > 0 ? 'font-variation-settings: \'FILL\' 1;' : '' }}">notifications</span>
        @if($this->unreadCount > 0)
            <span class="absolute top-1 right-1 min-w-[18px] h-[18px] bg-[#b31b25] text-white text-[10px] font-bold rounded-full flex items-center justify-center px-0.5 leading-none">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @else
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[#d1d5db] rounded-full"></span>
        @endif
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
         style="display:none"
         class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
            <h3 class="font-bold text-sm text-[#2c2f31]">Notifikasi</h3>
            @if($this->notifications->isNotEmpty())
                <button wire:click="markAllRead" class="text-xs text-[#0058ba] font-medium hover:underline">
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        {{-- List --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-50">
            @forelse($this->notifications as $notif)
                <div wire:key="notif-{{ $notif->id }}"
                     class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 transition-colors cursor-pointer {{ $notif->isRead() ? 'opacity-70' : '' }}"
                     @if($notif->url)
                         wire:click="markRead({{ $notif->id }})"
                         onclick="window.location='{{ $notif->url }}'"
                     @else
                         wire:click="markRead({{ $notif->id }})"
                     @endif>
                    {{-- Icon --}}
                    <div class="shrink-0 w-9 h-9 rounded-full flex items-center justify-center mt-0.5
                        @if($notif->type === 'course_enrolled') bg-[#dbeafe] text-[#0058ba]
                        @elseif($notif->type === 'tahfidz_graded') bg-[#d1fae5] text-[#00675c]
                        @elseif($notif->type === 'streak_reminder') bg-[#fef3c7] text-[#d97706]
                        @elseif($notif->type === 'achievement') bg-[#ede9fe] text-[#7c3aed]
                        @else bg-[#f3f4f6] text-[#595c5e]
                        @endif">
                        <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">{{ $notif->icon }}</span>
                    </div>
                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-[#2c2f31] leading-tight">{{ $notif->title }}</p>
                        <p class="text-xs text-[#595c5e] mt-0.5 line-clamp-2">{{ $notif->message }}</p>
                        <p class="text-[10px] text-[#abadaf] mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                    {{-- Unread dot --}}
                    @if(!$notif->isRead())
                        <div class="shrink-0 w-2 h-2 bg-[#0058ba] rounded-full mt-1.5"></div>
                    @endif
                </div>
            @empty
                <div class="py-10 text-center">
                    <span class="material-symbols-outlined text-4xl text-slate-300">notifications_off</span>
                    <p class="text-sm text-[#abadaf] mt-2">Belum ada notifikasi</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if($this->notifications->count() >= 15)
            <div class="px-4 py-2.5 border-t border-slate-100 text-center">
                <span class="text-xs text-[#abadaf]">Menampilkan 15 notifikasi terbaru</span>
            </div>
        @endif
    </div>
</div>
