@props(['event'])

<section id="about-awards" class="py-12 sm:py-16 bg-gradient-to-b from-zinc-950 via-zinc-900 to-zinc-950 text-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Tagline Banner -->
        @if ($event->tagline)
            <div class="text-center max-w-3xl mx-auto mb-10">
                <span class="inline-block px-3 py-1 bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-bold uppercase tracking-widest rounded-full mb-3">
                    OFFICIAL FESTIVAL STATEMENT
                </span>
                <h2 class="text-xl sm:text-3xl font-black text-white tracking-tight leading-snug">
                    "{{ $event->tagline }}"
                </h2>
            </div>
        @endif

        <!-- Live Countdown Ticker -->
        @if ($event->countdown_datetime)
            <div class="max-w-3xl mx-auto bg-zinc-900/90 rounded-3xl p-6 sm:p-8 border border-zinc-800 shadow-2xl mb-12"
                 x-data="{
                     target: new Date('{{ $event->countdown_datetime->toIso8601String() }}').getTime(),
                     now: new Date().getTime(),
                     days: 0,
                     hours: 0,
                     minutes: 0,
                     seconds: 0,
                     isExpired: false,
                     init() {
                         this.update();
                         setInterval(() => this.update(), 1000);
                     },
                     update() {
                         this.now = new Date().getTime();
                         const diff = this.target - this.now;
                         if (diff <= 0) {
                             this.isExpired = true;
                             return;
                         }
                         this.days = Math.floor(diff / (1000 * 60 * 60 * 24));
                         this.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                         this.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                         this.seconds = Math.floor((diff % (1000 * 60)) / 1000);
                     }
                 }">
                
                <template x-if="!isExpired">
                    <div>
                        <div class="text-center mb-5">
                            <span class="text-xs uppercase font-extrabold tracking-widest text-amber-400 block mb-1">
                                COUNTDOWN TO GRAND GALA & RESULTS
                            </span>
                            <p class="text-xs text-zinc-400">Award ceremony begins on {{ $event->countdown_datetime->format('M d, Y h:i A') }}</p>
                        </div>

                        <div class="grid grid-cols-4 gap-2 sm:gap-4 max-w-lg mx-auto">
                            <!-- Days -->
                            <div class="bg-zinc-850 border border-zinc-800 rounded-2xl p-3 sm:p-4 text-center">
                                <span class="text-2xl sm:text-4xl font-black text-amber-400 tracking-tight block" x-text="days">0</span>
                                <span class="text-[10px] sm:text-xs uppercase tracking-wider text-zinc-400 font-semibold block mt-1">Days</span>
                            </div>
                            <!-- Hours -->
                            <div class="bg-zinc-850 border border-zinc-800 rounded-2xl p-3 sm:p-4 text-center">
                                <span class="text-2xl sm:text-4xl font-black text-amber-400 tracking-tight block" x-text="hours">0</span>
                                <span class="text-[10px] sm:text-xs uppercase tracking-wider text-zinc-400 font-semibold block mt-1">Hours</span>
                            </div>
                            <!-- Minutes -->
                            <div class="bg-zinc-850 border border-zinc-800 rounded-2xl p-3 sm:p-4 text-center">
                                <span class="text-2xl sm:text-4xl font-black text-amber-400 tracking-tight block" x-text="minutes">0</span>
                                <span class="text-[10px] sm:text-xs uppercase tracking-wider text-zinc-400 font-semibold block mt-1">Minutes</span>
                            </div>
                            <!-- Seconds -->
                            <div class="bg-zinc-850 border border-zinc-800 rounded-2xl p-3 sm:p-4 text-center">
                                <span class="text-2xl sm:text-4xl font-black text-amber-400 tracking-tight block" x-text="seconds">0</span>
                                <span class="text-[10px] sm:text-xs uppercase tracking-wider text-zinc-400 font-semibold block mt-1">Seconds</span>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="isExpired">
                    <div class="text-center py-4">
                        <span class="inline-block px-3 py-1 bg-amber-500 text-black text-xs font-black uppercase tracking-wider rounded-full mb-3">
                            EVENT CONCLUDED
                        </span>
                        <p class="text-sm sm:text-base text-zinc-300 font-medium">
                            {{ $event->closure_message ?: 'The voting and official ceremony for this event have concluded.' }}
                        </p>
                    </div>
                </template>
            </div>
        @endif

        <!-- About the Event & Criteria (2 Columns) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 pt-4">
            <!-- About Editorial -->
            <div class="bg-zinc-900/60 rounded-3xl p-6 sm:p-8 border border-zinc-800">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-sm">01</span>
                    <h3 class="text-lg font-black uppercase tracking-wider text-white">About the Event</h3>
                </div>
                <div class="prose prose-invert prose-sm text-zinc-300 leading-relaxed">
                    {!! $event->about_text !!}
                </div>
            </div>

            <!-- Selection Criteria -->
            <div id="criteria" class="bg-zinc-900/60 rounded-3xl p-6 sm:p-8 border border-zinc-800">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-sm">02</span>
                    <h3 class="text-lg font-black uppercase tracking-wider text-white">Selection Criteria</h3>
                </div>
                <div class="prose prose-invert prose-sm text-zinc-300 leading-relaxed">
                    {!! $event->criteria_text !!}
                </div>
            </div>
        </div>

    </div>
</section>
