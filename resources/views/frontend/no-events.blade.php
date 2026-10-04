<x-layouts.microsite
    meta-title="Dib 24x7 Events - Setup"
    meta-description="Official event portal by Dib 24x7"
>
    <div class="min-h-screen bg-zinc-950 text-white flex flex-col items-center justify-center p-6 text-center">
        <!-- Logo Badge -->
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-amber-600 to-amber-400 flex items-center justify-center font-black text-white text-2xl tracking-tighter shadow-xl shadow-amber-500/20 mb-6">
            24x7
        </div>

        <span class="text-xs uppercase tracking-widest text-amber-500 font-extrabold block mb-2">
            DIB 24x7 EVENT MICROSITE
        </span>

        <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight mb-4 max-w-lg">
            No Active Event Configured Yet
        </h1>

        <p class="text-zinc-400 text-sm max-w-md mx-auto mb-8 leading-relaxed">
            The event portal is online and ready. To display the event microsite, please log in to the administrative panel and activate your event or run the database seeder.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a
                href="{{ url('/admin') }}"
                class="px-6 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-zinc-950 font-bold text-sm tracking-wide shadow-lg shadow-amber-500/25 hover:from-amber-400 hover:to-amber-500 transition-all flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Go to Admin Dashboard
            </a>

            <a
                href="{{ url('/admin/events') }}"
                class="px-6 py-3 rounded-xl bg-zinc-900 border border-zinc-800 text-zinc-300 hover:text-white hover:border-zinc-700 font-semibold text-sm transition-all"
            >
                Manage Events
            </a>
        </div>
    </div>
</x-layouts.microsite>
