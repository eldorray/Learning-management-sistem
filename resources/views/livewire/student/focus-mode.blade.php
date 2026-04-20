<div x-data="{
    open: false,
    active: @entangle('active'),
    duration: @entangle('duration'),
    timeLeft: 0,
    running: false,
    timer: null,
    progress: 0,
    totalSeconds: 0,

    startTimer() {
        this.totalSeconds = this.duration * 60;
        this.timeLeft = this.totalSeconds;
        this.progress = 0;
        this.running = true;
        this.timer = setInterval(() => {
            if (this.timeLeft <= 0) {
                this.running = false;
                clearInterval(this.timer);
                this.onComplete();
                return;
            }
            this.timeLeft--;
            this.progress = Math.round(((this.totalSeconds - this.timeLeft) / this.totalSeconds) * 100);
        }, 1000);
    },

    stopTimer() {
        clearInterval(this.timer);
        this.running = false;
        this.timeLeft = 0;
        this.progress = 0;
        $wire.stop();
    },

    onComplete() {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Sesi fokus selesai! 🎉', { body: 'Waktu istirahat sebentar sebelum sesi berikutnya.' });
        }
        // Bell sound via Web Audio API
        try {
            const ctx = new AudioContext();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain); gain.connect(ctx.destination);
            osc.frequency.value = 523; // C5
            gain.gain.setValueAtTime(0.4, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 1.5);
            osc.start(); osc.stop(ctx.currentTime + 1.5);
        } catch(e) {}
        $wire.stop();
        this.open = false;
    },

    formatTime(s) {
        const m = Math.floor(s / 60);
        const sec = s % 60;
        return String(m).padStart(2, '0') + ':' + String(sec).padStart(2, '0');
    },

    get circumference() { return 2 * Math.PI * 54; },
    get strokeDashoffset() { return this.circumference - (this.progress / 100) * this.circumference; },
}"
x-init="
    $watch('active', v => { if (v) { startTimer(); } });
    if ('Notification' in window) Notification.requestPermission();
">

    {{-- Focus Mode Trigger Button --}}
    <button @click="open = !open"
            :class="active ? 'bg-gradient-to-br from-[#7c3aed] to-[#5b21b6] animate-pulse' : 'bg-gradient-to-br from-[#0058ba] to-[#004da4]'"
            class="w-full text-[#f0f2ff] py-3.5 rounded-full font-bold flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20 hover:scale-[1.02] transition-all">
        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;" x-text="active ? 'timer' : 'bolt'"></span>
        <span x-text="active ? 'Sesi Berjalan' : 'Focus Mode'"></span>
    </button>

    {{-- Modal Overlay --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display:none"
         class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-md flex items-center justify-center p-4"
         @click.self="if (!active) open = false">

        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            {{-- Header --}}
            <div class="relative bg-gradient-to-br from-[#0058ba] to-[#004da4] px-6 py-5 text-white">
                <button x-show="!active" @click="open = false"
                        class="absolute top-4 right-4 p-1 rounded-full hover:bg-white/20 transition-colors">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">bolt</span>
                    </div>
                    <div>
                        <h2 class="font-bold text-lg leading-tight">Focus Mode</h2>
                        <p class="text-xs text-blue-100">Belajar tanpa distraksi</p>
                    </div>
                </div>
            </div>

            {{-- Setup State (not active) --}}
            <div x-show="!active" class="p-6 space-y-5">

                {{-- Duration Selector --}}
                <div>
                    <label class="block text-xs font-bold text-[#595c5e] uppercase tracking-wide mb-2">Durasi Sesi</label>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach([15, 25, 45, 60] as $min)
                            <button type="button"
                                    @click="$wire.set('duration', {{ $min }})"
                                    :class="duration == {{ $min }} ? 'bg-[#0058ba] text-white' : 'bg-[#f5f7f9] text-[#595c5e] hover:bg-[#e5e9eb]'"
                                    class="py-2 rounded-xl text-sm font-bold transition-colors">
                                {{ $min }}m
                            </button>
                        @endforeach
                    </div>
                    <div class="mt-2 flex items-center gap-2">
                        <input type="range" wire:model.live="duration" min="5" max="120" step="5"
                               class="flex-1 accent-[#0058ba]">
                        <span class="text-sm font-bold text-[#0058ba] w-12 text-right">
                            <span x-text="duration"></span>m
                        </span>
                    </div>
                </div>

                {{-- Course Selector --}}
                <div>
                    <label class="block text-xs font-bold text-[#595c5e] uppercase tracking-wide mb-2">Fokus pada kursus (opsional)</label>
                    <select wire:model="selectedCourseId"
                            class="w-full text-sm rounded-xl border border-[#e5e9eb] px-3 py-2.5 bg-[#f5f7f9] focus:outline-none focus:ring-2 focus:ring-[#0058ba]/30">
                        <option value="">Pilih bebas / tanpa kursus</option>
                        @foreach($this->activeEnrollments as $enrollment)
                            <option value="{{ $enrollment->course_id }}">
                                {{ $enrollment->course->title }} ({{ $enrollment->progress_percentage }}%)
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tips --}}
                <div class="bg-[#f0f7ff] rounded-2xl p-4 flex gap-3">
                    <span class="material-symbols-outlined text-[#0058ba] mt-0.5 shrink-0" style="font-variation-settings: 'FILL' 1;">lightbulb</span>
                    <p class="text-xs text-[#595c5e] leading-relaxed">
                        Teknik <strong class="text-[#2c2f31]">Pomodoro</strong>: Fokus 25 menit, istirahat 5 menit.
                        Notifikasi browser akan berbunyi saat sesi selesai.
                    </p>
                </div>

                {{-- Start Button --}}
                <button wire:click="start"
                        class="w-full bg-gradient-to-r from-[#0058ba] to-[#0073e6] text-white py-3.5 rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-[1.01] transition-all">
                    <span class="material-symbols-outlined align-middle mr-1 text-base">play_circle</span>
                    Mulai Fokus
                </button>
            </div>

            {{-- Active Timer State --}}
            <div x-show="active" class="p-6 text-center space-y-5">

                {{-- SVG Countdown Ring --}}
                <div class="flex justify-center">
                    <div class="relative w-36 h-36">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="54" fill="none" stroke="#e5e9eb" stroke-width="8"/>
                            <circle cx="60" cy="60" r="54" fill="none" stroke="#0058ba" stroke-width="8"
                                    stroke-linecap="round"
                                    :stroke-dasharray="circumference"
                                    :stroke-dashoffset="strokeDashoffset"
                                    style="transition: stroke-dashoffset 1s linear;"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-3xl font-bold text-[#0058ba] tabular-nums" x-text="formatTime(timeLeft)"></span>
                            <span class="text-xs text-[#595c5e] mt-0.5">tersisa</span>
                        </div>
                    </div>
                </div>

                {{-- Progress --}}
                <div>
                    <p class="text-sm font-bold text-[#2c2f31]">
                        <span x-text="progress"></span>% selesai
                    </p>
                    @if($selectedCourseId)
                        @php $selCourse = $this->activeEnrollments->firstWhere('course_id', $selectedCourseId) @endphp
                        @if($selCourse)
                            <p class="text-xs text-[#595c5e] mt-1">
                                <span class="material-symbols-outlined text-xs align-middle">auto_stories</span>
                                {{ $selCourse->course->title }}
                            </p>
                        @endif
                    @endif
                </div>

                {{-- Motivation Quote --}}
                <div class="bg-gradient-to-r from-[#f0f7ff] to-[#f0fdf4] rounded-2xl p-4">
                    <p class="text-xs italic text-[#595c5e]">"Sesungguhnya bersama kesulitan ada kemudahan." — QS. Al-Insyirah: 6</p>
                </div>

                {{-- Stop Button --}}
                <button @click="stopTimer()"
                        class="w-full border-2 border-[#b31b25] text-[#b31b25] py-3 rounded-2xl font-bold text-sm hover:bg-[#b31b25] hover:text-white transition-all">
                    <span class="material-symbols-outlined align-middle mr-1 text-base">stop_circle</span>
                    Akhiri Sesi
                </button>
            </div>
        </div>
    </div>
</div>
