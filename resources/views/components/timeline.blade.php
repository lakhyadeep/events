@props(['event', 'items' => collect()])

@if ($event->is_timeline_active && $items->isNotEmpty())
    <section id="timeline" class="py-14 sm:py-20 bg-zinc-900 border-t border-zinc-800 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-xl mx-auto mb-12">
                <span class="text-xs uppercase font-extrabold tracking-widest text-amber-500 block mb-1">
                    KEY MILESTONES
                </span>
                <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-white">
                    Event Roadmap & Schedule
                </h2>
            </div>

            <!-- Vertical Timeline Track -->
            <div class="relative border-l-2 border-zinc-700 ml-4 sm:ml-32 space-y-8">
                @foreach ($items as $item)
                    <div class="relative pl-6 sm:pl-8 group">
                        <!-- Node Pin -->
                        <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full border-2 transition-all {{ $item->status === 'ongoing' ? 'bg-amber-500 border-white ring-4 ring-amber-500/30' : ($item->status === 'completed' ? 'bg-emerald-500 border-zinc-900' : 'bg-zinc-700 border-zinc-900') }}">
                        </div>

                        <!-- Date Badge (Desktop Left Offset) -->
                        <div class="sm:absolute sm:-left-32 sm:top-1 text-xs font-black uppercase tracking-wider text-amber-400 sm:text-right sm:w-28 mb-1 sm:mb-0">
                            {{ $item->milestone_date }}
                        </div>

                        <!-- Card Body -->
                        <div class="bg-zinc-850 rounded-2xl p-5 border border-zinc-800 hover:border-zinc-700 transition">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <h3 class="text-sm sm:text-base font-black text-white">
                                    {{ $item->title }}
                                </h3>
                                
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $item->status === 'ongoing' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/40' : ($item->status === 'completed' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-zinc-800 text-zinc-400') }}">
                                    {{ $item->status }}
                                </span>
                            </div>

                            @if ($item->description)
                                <p class="text-xs text-zinc-400 leading-relaxed mt-1">
                                    {{ $item->description }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>
@endif
