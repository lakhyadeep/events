@props(['event'])

<nav class="sticky top-0 z-40 bg-zinc-900/90 dark:bg-black/90 backdrop-blur-md border-b border-zinc-800 shadow-sm" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">
            
            <!-- Desktop Nav Links -->
            <div class="hidden md:flex items-center gap-7 text-xs font-bold uppercase tracking-wider text-zinc-300">
                <a href="#about-awards" class="hover:text-amber-400 transition-colors">About the Awards</a>
                <a href="#criteria" class="hover:text-amber-400 transition-colors">Selection Criteria</a>
                <a href="#timeline" class="hover:text-amber-400 transition-colors">Timeline</a>
                <a href="#participants" class="hover:text-amber-400 transition-colors">Participants</a>
                <a href="#shorts" class="hover:text-amber-400 transition-colors">Event Shorts</a>
                <a href="#terms" class="hover:text-amber-400 transition-colors">Terms & Rules</a>
            </div>

            <!-- Right CTA: Vote Now -->
            <div class="flex items-center gap-3">
                @if ($event->is_voting_active)
                    <a href="#participants"
                       class="relative inline-flex items-center gap-2 px-5 py-2 rounded-full text-xs font-black uppercase tracking-wider bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white shadow-lg shadow-amber-500/25 hover:shadow-amber-500/40 transform hover:-translate-y-0.5 transition-all">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                        </span>
                        <span>Vote Now</span>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold bg-zinc-800 text-zinc-400 border border-zinc-700">
                        <span>Voting Closed</span>
                    </span>
                @endif

                <!-- Mobile Hamburger Button -->
                <button
                    type="button"
                    @click="mobileOpen = !mobileOpen"
                    class="md:hidden p-2 rounded-lg text-zinc-400 hover:text-white hover:bg-zinc-800 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Nav Menu -->
        <div x-show="mobileOpen" x-collapse class="md:hidden py-4 border-t border-zinc-800 space-y-2 text-xs font-bold uppercase tracking-wider text-zinc-300">
            <a href="#about-awards" @click="mobileOpen = false" class="block px-3 py-2 rounded-lg hover:bg-zinc-800 hover:text-amber-400">About the Awards</a>
            <a href="#criteria" @click="mobileOpen = false" class="block px-3 py-2 rounded-lg hover:bg-zinc-800 hover:text-amber-400">Selection Criteria</a>
            <a href="#timeline" @click="mobileOpen = false" class="block px-3 py-2 rounded-lg hover:bg-zinc-800 hover:text-amber-400">Timeline</a>
            <a href="#participants" @click="mobileOpen = false" class="block px-3 py-2 rounded-lg hover:bg-zinc-800 hover:text-amber-400">Participants</a>
            <a href="#shorts" @click="mobileOpen = false" class="block px-3 py-2 rounded-lg hover:bg-zinc-800 hover:text-amber-400">Event Shorts</a>
            <a href="#terms" @click="mobileOpen = false" class="block px-3 py-2 rounded-lg hover:bg-zinc-800 hover:text-amber-400">Terms & Rules</a>
        </div>
    </div>
</nav>
