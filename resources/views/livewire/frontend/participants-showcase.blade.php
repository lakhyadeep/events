<div>
    <!-- Search & Filter Controls -->
    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md rounded-2xl p-6 shadow-sm border border-zinc-200/80 dark:border-zinc-800 mb-8">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <!-- Search Input -->
            <div class="relative w-full md:w-80">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by club, locality, theme or artist..."
                    class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                >
            </div>

            <!-- Zone Filter Buttons -->
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                @foreach ($zones as $label => $val)
                    <button
                        type="button"
                        wire:click="setZone('{{ $val }}')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all {{ ($selectedZone === $val) ? 'bg-amber-500 text-white shadow-sm' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <!-- Shortlist Toggle -->
            <div>
                <button
                    type="button"
                    wire:click="toggleShortlisted"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl border transition {{ $shortlistedOnly ? 'bg-amber-50 border-amber-400 text-amber-700 dark:bg-amber-950/40 dark:border-amber-600 dark:text-amber-300' : 'bg-zinc-50 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:border-amber-500' }}"
                >
                    <svg class="w-4 h-4 {{ $shortlistedOnly ? 'text-amber-500 fill-amber-500' : 'text-zinc-400' }}" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span>Shortlisted Only</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Candidate Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($participants as $participant)
            <div class="group bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-xl hover:border-amber-400 dark:hover:border-amber-500/50 transition-all duration-300 flex flex-col">
                <!-- Card Image -->
                <div class="relative h-56 w-full overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                    <img
                        src="{{ $participant->primary_display_image }}"
                        alt="{{ $participant->display_name }}"
                        loading="lazy"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>

                    <!-- Shortlisted Badge -->
                    @if ($participant->is_shortlisted)
                        <div class="absolute top-3 left-3 bg-amber-500/90 text-white text-[11px] font-bold px-2.5 py-1 rounded-full backdrop-blur-sm shadow flex items-center gap-1">
                            <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span>SHORTLISTED</span>
                        </div>
                    @endif

                    <!-- Zone Pill -->
                    <div class="absolute top-3 right-3 bg-zinc-900/80 text-zinc-200 text-[11px] font-semibold px-2.5 py-1 rounded-full backdrop-blur-sm border border-zinc-700">
                        {{ $participant->zone }}
                    </div>

                    <!-- Card Header Details Overlay -->
                    <div class="absolute bottom-3 left-3 right-3 text-white">
                        <h3 class="text-lg font-bold tracking-tight leading-tight drop-shadow-sm">
                            {{ $participant->display_name }}
                        </h3>
                        <p class="text-xs text-zinc-300 flex items-center gap-1 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ $participant->locality }}</span>
                        </p>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        @if ($participant->puja_theme)
                            <div class="mb-3">
                                <span class="text-[11px] uppercase tracking-wider font-bold text-amber-600 dark:text-amber-400 block mb-0.5">Theme:</span>
                                <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                                    {{ $participant->puja_theme }}
                                </p>
                            </div>
                        @endif

                        @if ($participant->idol_artist)
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-2">
                                <span class="font-medium text-zinc-700 dark:text-zinc-300">Idol Artist:</span> {{ $participant->idol_artist }}
                            </div>
                        @endif

                        <p class="text-xs text-zinc-600 dark:text-zinc-400 line-clamp-2 mt-2">
                            {{ $participant->short_introduction }}
                        </p>
                    </div>

                    <!-- Card Actions -->
                    <div class="pt-4 mt-4 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between">
                        @if ($participant->first_year_of_puja)
                            <span class="text-[11px] font-medium text-zinc-400">
                                Est. {{ $participant->first_year_of_puja }}
                            </span>
                        @else
                            <span></span>
                        @endif

                        <button
                            type="button"
                            wire:click="selectCandidate({{ $participant->id }})"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 transition"
                        >
                            <span>View Concept & Details</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="mt-3 text-sm font-semibold text-zinc-900 dark:text-zinc-100">No participants found</h3>
                <p class="mt-1 text-xs text-zinc-500">Try adjusting your search query or selecting a different zone.</p>
            </div>
        @endforelse
    </div>
</div>
