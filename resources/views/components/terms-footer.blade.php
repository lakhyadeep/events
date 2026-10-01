@props(['event'])

<!-- Terms & Conditions Section -->
<section id="terms" class="py-12 bg-zinc-950 text-white border-t border-zinc-800" x-data="{ openTerms: false }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-zinc-900 rounded-3xl border border-zinc-800 overflow-hidden shadow-lg">
            <button
                type="button"
                @click="openTerms = !openTerms"
                class="w-full p-6 text-left flex items-center justify-between gap-4 hover:bg-zinc-850/50 transition"
            >
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-amber-500 block mb-1">
                        OFFICIAL GUIDELINES
                    </span>
                    <h3 class="text-lg font-black text-white">
                        Contest Rules & Terms and Conditions
                    </h3>
                </div>
                <div class="w-8 h-8 rounded-full bg-zinc-800 flex items-center justify-center text-zinc-400 group-hover:text-white transition">
                    <svg class="w-4 h-4 transform transition-transform duration-200" :class="openTerms ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </button>

            <div x-show="openTerms" x-collapse class="p-6 border-t border-zinc-800/80 bg-zinc-950/40 text-xs sm:text-sm text-zinc-300 leading-relaxed prose prose-invert max-w-none">
                {!! $event->terms_and_conditions !!}
            </div>
        </div>
    </div>
</section>

<!-- Main Footer -->
<footer class="bg-black text-zinc-400 border-t border-zinc-900 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-zinc-900">
            
            <!-- Brand -->
            <div class="md:col-span-2 space-y-3">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 text-black font-black flex items-center justify-center text-sm shadow">
                        24x7
                    </div>
                    <span class="text-lg font-black tracking-tight text-white">Dib 24x7 Digital</span>
                </div>
                <p class="text-xs text-zinc-500 max-w-sm leading-relaxed">
                    Connecting millions of viewers with real-time news, cultural festivals, awards, and exclusive live broadcast events across Bengal and worldwide.
                </p>
            </div>

            <!-- Contacts -->
            <div class="space-y-2 text-xs">
                <h4 class="font-black uppercase tracking-wider text-white text-xs mb-3">Event Contacts</h4>
                @if ($event->contact_email)
                    <p class="flex items-center gap-2">
                        <span class="text-zinc-500">Email:</span>
                        <a href="mailto:{{ $event->contact_email }}" class="text-amber-400 hover:underline">{{ $event->contact_email }}</a>
                    </p>
                @endif
                @if ($event->contact_phone)
                    <p class="flex items-center gap-2">
                        <span class="text-zinc-500">Phone:</span>
                        <a href="tel:{{ $event->contact_phone }}" class="text-amber-400 hover:underline">{{ $event->contact_phone }}</a>
                    </p>
                @endif
                @if ($event->external_link)
                    <p class="flex items-center gap-2 pt-1">
                        <a href="{{ $event->external_link }}" target="_blank" rel="noopener noreferrer" class="text-zinc-300 hover:text-white underline">
                            Visit Main Network Portal →
                        </a>
                    </p>
                @endif
            </div>

            <!-- Quick Links / Portal -->
            <div class="space-y-2 text-xs">
                <h4 class="font-black uppercase tracking-wider text-white text-xs mb-3">Administration</h4>
                <p><a href="{{ url('/admin') }}" class="text-zinc-400 hover:text-amber-400 transition">Filament Admin Console →</a></p>
                <p><a href="{{ url('/api/v1/events/' . $event->slug) }}" class="text-zinc-400 hover:text-amber-400 transition" target="_blank">Headless Event API (JSON) →</a></p>
                <p class="text-zinc-600 text-[11px] pt-2">Powered by Laravel 13, Livewire 4 & Filament 5</p>
            </div>
        </div>

        <!-- Copyright Strip -->
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-zinc-600 gap-3">
            <p>© {{ date('Y') }} Dib 24x7 Digital Media. All rights reserved.</p>
            <p class="text-zinc-500">Sharod Samman Festival Excellence Awards</p>
        </div>
    </div>
</footer>
