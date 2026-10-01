@props(['event', 'presentingPartner' => null, 'associateSponsors' => collect()])

<header class="w-full bg-zinc-950 text-white border-b border-zinc-800 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
            
            <!-- Left Zone: Dib 24x7 Main Entity & Event Logo -->
            <div class="flex items-center gap-4 sm:gap-6 w-full lg:w-auto justify-between lg:justify-start">
                <!-- Dib 24x7 Brand Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-600 to-amber-400 flex items-center justify-center font-black text-white text-base tracking-tighter shadow-md group-hover:scale-105 transition-transform">
                        24x7
                    </div>
                    <div>
                        <span class="text-xs uppercase tracking-widest text-zinc-400 block font-bold">DIGITAL NEWS</span>
                        <span class="text-lg font-black tracking-tight text-white group-hover:text-amber-400 transition-colors">Dib 24x7</span>
                    </div>
                </a>

                <!-- Vertical Divider -->
                <div class="h-8 w-px bg-zinc-800 hidden sm:block"></div>

                <!-- Event Branding -->
                <div class="flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider text-amber-500 font-bold block">{{ $event->year }} OFFICIAL MICROSITE</span>
                        <h1 class="text-sm sm:text-base font-extrabold text-zinc-100 tracking-tight leading-tight line-clamp-1">
                            {{ $event->display_name }}
                        </h1>
                    </div>
                </div>
            </div>

            <!-- Right Zone: Multi-tier Sponsors (Presenting + Max 5 Associate Slots) -->
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-4 sm:gap-6 w-full lg:w-auto justify-center lg:justify-end border-t lg:border-t-0 border-zinc-850 pt-3 lg:pt-0">
                
                <!-- Presenting Partner Slot -->
                @if ($presentingPartner)
                    <div class="flex items-center gap-3 pr-4 border-r border-zinc-800">
                        <span class="text-[10px] uppercase font-bold tracking-widest text-zinc-400 whitespace-nowrap">PRESENTED BY</span>
                        <a href="{{ $presentingPartner->landing_url ?: '#' }}" target="_blank" rel="noopener noreferrer" class="group transition transform hover:scale-105" title="{{ $presentingPartner->title }}">
                            <div class="h-9 px-3 py-1 bg-white/95 rounded-lg flex items-center justify-center border border-zinc-700 shadow-sm">
                                <img src="{{ $presentingPartner->logo }}" alt="{{ $presentingPartner->display_name }}" class="max-h-7 max-w-[90px] object-contain">
                            </div>
                        </a>
                    </div>
                @endif

                <!-- Associate Sponsors (Up to 5 Slots Provision) -->
                @if ($associateSponsors->isNotEmpty())
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] uppercase font-bold tracking-widest text-zinc-500 hidden xl:inline-block">ASSOCIATE SPONSORS</span>
                        <div class="grid grid-flow-col auto-cols-max gap-2 items-center">
                            @foreach ($associateSponsors as $sponsor)
                                <a href="{{ $sponsor->landing_url ?: '#' }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="group transition-all hover:scale-105"
                                   title="Slot {{ $sponsor->slot_order }}: {{ $sponsor->display_name }}">
                                    <div class="h-8 w-16 sm:w-20 px-2 py-0.5 bg-zinc-900 hover:bg-zinc-850 rounded-lg border border-zinc-800 flex items-center justify-center transition">
                                        <img src="{{ $sponsor->logo }}" alt="{{ $sponsor->display_name }}" class="max-h-6 max-w-full object-contain filter grayscale group-hover:grayscale-0 transition-all opacity-80 group-hover:opacity-100">
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</header>
