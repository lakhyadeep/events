@props(['shorts' => collect()])

@if ($shorts->isNotEmpty())
    <section id="shorts" class="py-14 sm:py-20 bg-zinc-900 border-t border-b border-zinc-800 text-white"
             x-data="{ activeVideo: null, isVideoOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
                <div>
                    <span class="text-xs uppercase font-extrabold tracking-widest text-amber-500 block mb-1">
                        VIRAL GLIMPSES & REELS
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-white">
                        Event Video Shorts (9:16)
                    </h2>
                </div>
                <p class="text-xs text-zinc-400 max-w-sm">
                    Watch exclusive pandal walk-throughs, artisan interviews, and behind-the-scenes glimpses.
                </p>
            </div>

            <!-- 9:16 Vertical Carousel Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach ($shorts as $short)
                    <div class="group relative rounded-2xl overflow-hidden bg-black aspect-[9/16] border border-zinc-800 shadow-lg cursor-pointer transform hover:-translate-y-1 transition duration-300"
                         @click="activeVideo = '{{ $short->embed_url }}'; isVideoOpen = true">
                        
                        <!-- Thumbnail Image -->
                        <img
                            src="{{ $short->thumbnail_image_url ?: 'https://images.unsplash.com/photo-1567157577867-05ccb1388e66?auto=format&fit=crop&w=600&q=80' }}"
                            alt="{{ $short->name }}"
                            loading="lazy"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        >

                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-black/20"></div>

                        <!-- Platform Badge -->
                        <div class="absolute top-3 left-3 bg-red-600/90 text-white text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full shadow backdrop-blur-sm">
                            Shorts
                        </div>

                        <!-- Play Icon Button Overlay -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-12 h-12 rounded-full bg-amber-500 text-zinc-950 flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 fill-current ml-0.5" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Title & Caption -->
                        <div class="absolute bottom-3 left-3 right-3 text-white">
                            <h3 class="text-xs sm:text-sm font-bold line-clamp-2 leading-snug drop-shadow-sm group-hover:text-amber-400 transition-colors">
                                {{ $short->name }}
                            </h3>
                            @if ($short->caption)
                                <p class="text-[11px] text-zinc-400 line-clamp-1 mt-0.5">
                                    {{ $short->caption }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Video Player Lightbox Modal -->
            <div x-show="isVideoOpen"
                 x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto bg-black/90 backdrop-blur-md flex items-center justify-center p-4"
                 @keydown.escape.window="isVideoOpen = false; activeVideo = null">
                
                <div class="relative w-full max-w-sm aspect-[9/16] bg-black rounded-3xl overflow-hidden border border-zinc-800 shadow-2xl">
                    <!-- Close Button -->
                    <button
                        type="button"
                        @click="isVideoOpen = false; activeVideo = null"
                        class="absolute top-4 right-4 z-20 w-9 h-9 rounded-full bg-zinc-900/80 hover:bg-zinc-800 text-white flex items-center justify-center border border-zinc-700 transition"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <!-- Iframe Player -->
                    <template x-if="isVideoOpen && activeVideo">
                        <iframe
                            :src="activeVideo"
                            class="w-full h-full border-0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                    </template>
                </div>
            </div>

        </div>
    </section>
@endif
