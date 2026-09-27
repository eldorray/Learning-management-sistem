<div x-data="{
    open: false,
    duration: @entangle('duration'),
    endsAt: 0,
    totalSeconds: 0,
    timeLeft: 0,
    progress: 0,
    timer: null,

    get running() { return this.endsAt > 0; },

    init() {
        // A session keeps running while the student moves between pages.
        let saved = null;
        try { saved = JSON.parse(localStorage.getItem('focusSession') || 'null'); } catch (e) {}
        if (saved && saved.endsAt > Date.now()) {
            this.endsAt = saved.endsAt;
            this.totalSeconds = saved.total;
            this.run();
        }
    },

    async begin() {
        // Ask for notification permission only now, in answer to the student's own tap.
        if ('Notification' in window && Notification.permission === 'default') {
            try { await Notification.requestPermission(); } catch (e) {}
        }
        await $wire.start();
        if (! $wire.active) return;
        this.totalSeconds = this.duration * 60;
        this.endsAt = Date.now() + this.totalSeconds * 1000;
        try { localStorage.setItem('focusSession', JSON.stringify({ endsAt: this.endsAt, total: this.totalSeconds })); } catch (e) {}
        this.open = false;
        this.run();
    },

    run() {
        clearInterval(this.timer);
        this.tick();
        this.timer = setInterval(() => this.tick(), 1000);
    },

    tick() {
        this.timeLeft = Math.max(0, Math.round((this.endsAt - Date.now()) / 1000));
        this.progress = this.totalSeconds ? Math.round(((this.totalSeconds - this.timeLeft) / this.totalSeconds) * 100) : 0;
        if (this.timeLeft === 0) this.finish(true);
    },

    finish(completed) {
        clearInterval(this.timer);
        this.endsAt = 0;
        this.timeLeft = 0;
        this.progress = 0;
        try { localStorage.removeItem('focusSession'); } catch (e) {}
        $wire.stop();
        if (! completed) return;
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Sesi fokus selesai! 🎉', { body: 'Waktu istirahat sebentar sebelum sesi berikutnya.' });
        }
        try {
            const ctx = new AudioContext();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain); gain.connect(ctx.destination);
            osc.frequency.value = 523; // C5
            gain.gain.setValueAtTime(0.4, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 1.5);
            osc.start(); osc.stop(ctx.currentTime + 1.5);
        } catch (e) {}
        this.open = true;
    },

    formatTime(s) {
        return String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
    },

    get circumference() { return 2 * Math.PI * 54; },
    get strokeDashoffset() { return this.circumference - (this.progress / 100) * this.circumference; },
}" @keydown.escape.window="open = false">

    {{-- Focus Mode Trigger Button --}}
    <button type="button" @click="open = true" :aria-expanded="open.toString()"
            :class="running ? 'bg-gradient-to-br from-[#7a5765] to-[#644755]' : 'bg-gradient-to-br from-[#8a5a31] to-[#6f4826]'"
            class="w-full text-[#fbf6ee] py-3.5 rounded-full font-bold flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20 hover:scale-[1.02] transition-all">
        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;" aria-hidden="true" x-text="running ? 'timer' : 'bolt'"></span>
        <span x-text="running ? 'Sesi Fokus · ' + formatTime(timeLeft) : 'Mode Fokus'"></span>
    </button>

    {{-- Teleported so the fixed layers escape the (transformed) mobile sidebar --}}
    @teleport('body')
    <div>
        {{-- Floating timer: the page stays usable while a session runs --}}
        <div x-show="running && !open" x-transition.opacity style="display:none"
             class="fixed z-[90] right-4 bottom-[calc(5rem+env(safe-area-inset-bottom,0px))] lg:bottom-6 flex items-center gap-1 pl-4 pr-1.5 py-1.5 rounded-full bg-[#644755] text-[#fbf6ee] shadow-xl">
            <button type="button" @click="open = true" class="flex items-center gap-2 font-bold tabular-nums text-sm py-1" aria-label="Buka sesi fokus">
                <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">timer</span>
                <span x-text="formatTime(timeLeft)"></span>
            </button>
            <button type="button" @click="finish(false)" class="ml-1 w-8 h-8 rounded-full hover:bg-white/15 flex items-center justify-center" aria-label="Akhiri sesi fokus">
                <span class="material-symbols-outlined text-base" aria-hidden="true">stop</span>
            </button>
        </div>

        {{-- Modal --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display:none"
             class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-md flex items-center justify-center p-4"
             @click.self="open = false">

            <div role="dialog" aria-modal="true" aria-labelledby="focus-mode-title" class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">

                {{-- Header --}}
                <div class="relative bg-gradient-to-br from-[#8a5a31] to-[#6f4826] px-6 py-5 text-white">
                    <button type="button" @click="open = false" aria-label="Tutup"
                            class="absolute top-3 right-3 w-10 h-10 rounded-full hover:bg-white/20 transition-colors flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg" aria-hidden="true">close</span>
                    </button>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">bolt</span>
                        </div>
                        <div>
                            <h2 id="focus-mode-title" class="font-bold text-lg leading-tight">Mode Fokus</h2>
                            <p class="text-xs text-blue-100">Belajar tanpa distraksi</p>
                        </div>
                    </div>
                </div>

                {{-- Setup State --}}
                <div x-show="!running" class="p-6 space-y-5">
                    <div>
                        <p class="block text-xs font-bold text-[#6b6358] uppercase tracking-wide mb-2">Durasi Sesi</p>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach([15, 25, 45, 60] as $min)
                                <button type="button"
                                        @click="$wire.set('duration', {{ $min }})"
                                        :aria-pressed="(duration == {{ $min }}).toString()"
                                        :class="duration == {{ $min }} ? 'bg-[#8a5a31] text-white' : 'bg-[#f3efe6] text-[#6b6358] hover:bg-[#e4dccc]'"
                                        class="py-2.5 rounded-xl text-sm font-bold transition-colors">
                                    {{ $min }}m
                                </button>
                            @endforeach
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <input type="range" wire:model.live="duration" min="5" max="120" step="5" aria-label="Durasi sesi dalam menit"
                                   class="flex-1 accent-[#8a5a31]">
                            <span class="text-sm font-bold text-[#8a5a31] w-12 text-right">
                                <span x-text="duration"></span>m
                            </span>
                        </div>
                        @error('duration')<p class="text-xs text-[#a3402c] mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="focus-course" class="block text-xs font-bold text-[#6b6358] uppercase tracking-wide mb-2">Fokus pada kursus (opsional)</label>
                        <select id="focus-course" wire:model="selectedCourseId"
                                class="w-full text-sm rounded-xl border border-[#e4dccc] px-3 py-2.5 bg-[#f3efe6] focus:outline-none focus:ring-2 focus:ring-[#8a5a31]/30">
                            <option value="">Pilih bebas / tanpa kursus</option>
                            @foreach($this->activeEnrollments as $enrollment)
                                <option value="{{ $enrollment->course_id }}">
                                    {{ $enrollment->course->title }} ({{ $enrollment->progress_percentage }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bg-[#f7f0e4] rounded-2xl p-4 flex gap-3">
                        <span class="material-symbols-outlined text-[#8a5a31] mt-0.5 shrink-0" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">lightbulb</span>
                        <p class="text-xs text-[#6b6358] leading-relaxed">
                            Teknik <strong class="text-[#2b2721]">Pomodoro</strong>: fokus 25 menit, istirahat 5 menit.
                            Setelah dimulai, jendela ini tertutup dan timer tampil di pojok layar, jadi Anda tetap bisa membuka materi.
                            Izinkan notifikasi bila ingin diberi tahu saat sesi selesai.
                        </p>
                    </div>

                    <button type="button" @click="begin()"
                            class="w-full bg-gradient-to-r from-[#8a5a31] to-[#9a6a3e] text-white py-3.5 rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-[1.01] transition-all">
                        <span class="material-symbols-outlined align-middle mr-1 text-base" aria-hidden="true">play_circle</span>
                        Mulai Fokus
                    </button>
                </div>

                {{-- Active Timer State --}}
                <div x-show="running" class="p-6 text-center space-y-5">
                    <div class="flex justify-center">
                        <div class="relative w-36 h-36">
                            <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120" aria-hidden="true">
                                <circle cx="60" cy="60" r="54" fill="none" stroke="#e4dccc" stroke-width="8"/>
                                <circle cx="60" cy="60" r="54" fill="none" stroke="#8a5a31" stroke-width="8"
                                        stroke-linecap="round"
                                        :stroke-dasharray="circumference"
                                        :stroke-dashoffset="strokeDashoffset"
                                        style="transition: stroke-dashoffset 1s linear;"/>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-3xl font-bold text-[#8a5a31] tabular-nums" x-text="formatTime(timeLeft)"></span>
                                <span class="text-xs text-[#6b6358] mt-0.5">tersisa</span>
                            </div>
                        </div>
                    </div>

                    <p class="text-sm font-bold text-[#2b2721]"><span x-text="progress"></span>% selesai</p>

                    <div class="bg-gradient-to-r from-[#f7f0e4] to-[#f0fdf4] rounded-2xl p-4">
                        <p class="text-xs italic text-[#6b6358]">"Sesungguhnya bersama kesulitan ada kemudahan." — QS. Al-Insyirah: 6</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="open = false"
                                class="w-full bg-[#f3efe6] text-[#2b2721] py-3 rounded-2xl font-bold text-sm hover:bg-[#e4dccc] transition-colors">
                            Lanjut Belajar
                        </button>
                        <button type="button" @click="finish(false)"
                                class="w-full border-2 border-[#a3402c] text-[#a3402c] py-3 rounded-2xl font-bold text-sm hover:bg-[#a3402c] hover:text-white transition-all">
                            Akhiri Sesi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endteleport
</div>
