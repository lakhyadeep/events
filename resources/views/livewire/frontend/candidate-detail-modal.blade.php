<div>
    @if ($isOpen && $candidate)
        <!-- Modal Backdrop -->
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6"
             x-data
             @keydown.escape.window="$wire.closeModal()">
            
            <div class="relative w-full max-w-4xl bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 animate-in fade-in zoom-in-95 duration-200">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300">
                                {{ $candidate->zone }}
                            </span>
                            @if ($candidate->is_shortlisted)
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500 text-white">
                                    SHORTLISTED
                                </span>
                            @endif
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-zinc-100 mt-1">
                            {{ $candidate->display_name }}
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $candidate->locality }} @if($candidate->landmark) • Near {{ $candidate->landmark }} @endif
                        </p>
                    </div>

                    <!-- Close Button -->
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="p-2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content Grid -->
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8 max-h-[75vh] overflow-y-auto">
                    <!-- Left: Gallery & Image Viewer -->
                    <div>
                        <div class="relative h-72 sm:h-80 rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 shadow-inner">
                            <img
                                src="{{ $activeImage ?: $candidate->primary_display_image }}"
                                alt="{{ $candidate->display_name }}"
                                class="w-full h-full object-cover transition duration-300"
                            >
                        </div>

                        <!-- Thumbnails -->
                        <div class="flex gap-2.5 mt-3 overflow-x-auto pb-1">
                            <button
                                type="button"
                                wire:click="setActiveImage('{{ $candidate->primary_display_image }}')"
                                class="relative w-16 h-16 rounded-xl overflow-hidden border-2 flex-shrink-0 transition {{ $activeImage === $candidate->primary_display_image ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-zinc-200 dark:border-zinc-700 opacity-70 hover:opacity-100' }}"
                            >
                                <img src="{{ $candidate->primary_display_image }}" class="w-full h-full object-cover">
                            </button>

                            @if ($candidate->image_1)
                                <button
                                    type="button"
                                    wire:click="setActiveImage('{{ $candidate->image_1 }}')"
                                    class="relative w-16 h-16 rounded-xl overflow-hidden border-2 flex-shrink-0 transition {{ $activeImage === $candidate->image_1 ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-zinc-200 dark:border-zinc-700 opacity-70 hover:opacity-100' }}"
                                >
                                    <img src="{{ $candidate->image_1 }}" class="w-full h-full object-cover">
                                </button>
                            @endif

                            @if ($candidate->image_2)
                                <button
                                    type="button"
                                    wire:click="setActiveImage('{{ $candidate->image_2 }}')"
                                    class="relative w-16 h-16 rounded-xl overflow-hidden border-2 flex-shrink-0 transition {{ $activeImage === $candidate->image_2 ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-zinc-200 dark:border-zinc-700 opacity-70 hover:opacity-100' }}"
                                >
                                    <img src="{{ $candidate->image_2 }}" class="w-full h-full object-cover">
                                </button>
                            @endif

                            @if ($candidate->concept_note_image)
                                <button
                                    type="button"
                                    wire:click="setActiveImage('{{ $candidate->concept_note_image }}')"
                                    class="relative w-16 h-16 rounded-xl overflow-hidden border-2 flex-shrink-0 transition {{ $activeImage === $candidate->concept_note_image ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-zinc-200 dark:border-zinc-700 opacity-70 hover:opacity-100' }}"
                                >
                                    <img src="{{ $candidate->concept_note_image }}" class="w-full h-full object-cover">
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Right: Candidate Details & Puja Specifications -->
                    <div class="space-y-6">
                        <!-- About -->
                        @if ($candidate->short_introduction)
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-1">About the Entry</h4>
                                <p class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed">
                                    {{ $candidate->short_introduction }}
                                </p>
                            </div>
                        @endif

                        <!-- Puja Contest Specifics -->
                        @if ($candidate->is_puja_contest)
                            <div class="bg-amber-500/5 dark:bg-amber-500/10 rounded-2xl p-4 border border-amber-500/20 space-y-3">
                                <h4 class="text-xs font-black uppercase tracking-wider text-amber-700 dark:text-amber-300 flex items-center gap-1.5">
                                    <span>Durga Puja Artistic Credits</span>
                                </h4>

                                <div class="grid grid-cols-2 gap-3 text-xs">
                                    @if ($candidate->puja_theme)
                                        <div class="col-span-2">
                                            <span class="text-zinc-500 block">Theme Concept:</span>
                                            <span class="font-bold text-zinc-900 dark:text-zinc-100 text-sm">{{ $candidate->puja_theme }}</span>
                                        </div>
                                    @endif

                                    @if ($candidate->idol_artist)
                                        <div>
                                            <span class="text-zinc-500 block">Idol Artist:</span>
                                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $candidate->idol_artist }}</span>
                                        </div>
                                    @endif

                                    @if ($candidate->theme_artist)
                                        <div>
                                            <span class="text-zinc-500 block">Theme Artist:</span>
                                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $candidate->theme_artist }}</span>
                                        </div>
                                    @endif

                                    @if ($candidate->light_designer)
                                        <div>
                                            <span class="text-zinc-500 block">Light Designer:</span>
                                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $candidate->light_designer }}</span>
                                        </div>
                                    @endif

                                    @if ($candidate->sound_designer)
                                        <div>
                                            <span class="text-zinc-500 block">Sound Designer:</span>
                                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $candidate->sound_designer }}</span>
                                        </div>
                                    @endif

                                    @if ($candidate->first_year_of_puja)
                                        <div>
                                            <span class="text-zinc-500 block">First Year (Est):</span>
                                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $candidate->first_year_of_puja }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Contact & Address -->
                        <div class="border-t border-zinc-100 dark:border-zinc-800 pt-4 text-xs space-y-2 text-zinc-600 dark:text-zinc-400">
                            @if ($candidate->address)
                                <p><strong class="text-zinc-700 dark:text-zinc-300">Address:</strong> {{ $candidate->address }}</p>
                            @endif

                            @if ($candidate->key_contact_1_name && $candidate->key_contact_1_phone)
                                <p><strong class="text-zinc-700 dark:text-zinc-300">Key Contact:</strong> {{ $candidate->key_contact_1_name }} (<a href="tel:{{ $candidate->key_contact_1_phone }}" class="text-amber-600 hover:underline">{{ $candidate->key_contact_1_phone }}</a>)</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-100 dark:border-zinc-800 flex justify-end">
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="px-5 py-2 text-xs font-bold rounded-xl bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-300 dark:hover:bg-zinc-600 transition"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
