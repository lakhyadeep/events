@props(['event', 'awards' => collect(), 'winners' => collect()])

<section id="awards" class="py-14 sm:py-20 bg-zinc-950 text-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-14">
            <span class="text-xs uppercase font-extrabold tracking-widest text-amber-500 block mb-1">
                RECOGNITION & HONORS
            </span>
            <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-white">
                Award Categories & Prize Matrix
            </h2>
            <p class="text-xs sm:text-sm text-zinc-400 mt-2">
                Honoring the grand traditions, sculptors, and community committees shaping Bengal's festival legacy.
            </p>
        </div>

        <!-- Awards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
            @foreach ($awards as $award)
                <div class="bg-zinc-900 rounded-3xl p-6 sm:p-7 border border-zinc-800 hover:border-amber-500/50 transition-all flex flex-col justify-between group shadow-sm hover:shadow-xl">
                    <div>
                        <!-- Category Badge -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg bg-zinc-800 text-zinc-300 border border-zinc-700">
                                {{ $award->category }}
                            </span>
                            <span class="w-8 h-8 rounded-full bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold text-xs">
                                ★
                            </span>
                        </div>

                        <!-- Award Name -->
                        <h3 class="text-lg font-black text-white group-hover:text-amber-400 transition-colors">
                            {{ $award->display_name }}
                        </h3>

                        <!-- Description -->
                        @if ($award->description)
                            <p class="text-xs text-zinc-400 mt-2 leading-relaxed">
                                {{ $award->description }}
                            </p>
                        @endif
                    </div>

                    <!-- Prize Specification -->
                    @if ($award->prize_money_or_award)
                        <div class="mt-6 pt-4 border-t border-zinc-800 flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Prize & Award:</span>
                            <span class="text-xs font-black text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-lg border border-amber-500/20">
                                {{ $award->prize_money_or_award }}
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Winners Podium (Rendered if event->is_awards_active == true and winners exist) -->
        @if ($event->is_awards_active && $winners->isNotEmpty())
            <div id="winners-podium" class="mt-8 bg-gradient-to-b from-zinc-900 to-black rounded-3xl p-6 sm:p-10 border border-amber-500/30 shadow-2xl relative overflow-hidden">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>

                <div class="text-center max-w-2xl mx-auto mb-10">
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-amber-500 text-black shadow-md inline-block mb-3">
                        OFFICIAL PODIUM
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        Hall of Champions: Winners 2026
                    </h3>
                    <p class="text-xs text-zinc-400 mt-1">
                        Congratulations to all winning committees, idol sculptors, and creative teams!
                    </p>
                </div>

                <!-- Winners Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($winners as $winner)
                        <div class="bg-zinc-950/80 rounded-2xl p-5 border border-zinc-800 hover:border-amber-400/40 transition flex flex-col items-center text-center">
                            <!-- Rank Badge -->
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-600 to-amber-400 text-white flex items-center justify-center font-black text-xl shadow-lg mb-4">
                                {{ $winner->rank_order }}
                            </div>

                            <!-- Award Title -->
                            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-500 mb-1">
                                {{ $winner->award->display_name }}
                            </span>

                            <!-- Winner Participant Name -->
                            <h4 class="text-base font-black text-white">
                                {{ $winner->participant->display_name }}
                            </h4>

                            <!-- Locality & Zone -->
                            <p class="text-xs text-zinc-400 mt-0.5">
                                {{ $winner->participant->locality }} ({{ $winner->participant->zone }})
                            </p>

                            <!-- Theme if present -->
                            @if ($winner->participant->puja_theme)
                                <div class="mt-3 px-3 py-1 rounded-full bg-zinc-900 border border-zinc-800 text-[11px] text-zinc-300">
                                    Theme: <span class="font-bold text-amber-400">{{ $winner->participant->puja_theme }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</section>
