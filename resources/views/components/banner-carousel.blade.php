@props(['banners' => collect()])

@if ($banners->isNotEmpty())
    <section class="relative w-full overflow-hidden bg-black"
             x-data="{
                 activeSlide: 0,
                 totalSlides: {{ $banners->count() }},
                 interval: null,
                 init() {
                     if (this.totalSlides > 1) {
                         this.interval = setInterval(() => {
                             this.next();
                         }, 6000);
                     }
                 },
                 next() {
                     this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
                 },
                 prev() {
                     this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides;
                 }
             }">
        
        <!-- Slides Container -->
        <div class="relative w-full h-[360px] sm:h-[460px] lg:h-[540px]">
            @foreach ($banners as $index => $banner)
                <div x-show="activeSlide === {{ $index }}"
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-500"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute inset-0 w-full h-full">
                    
                    <!-- Responsive Picture Tag -->
                    <picture class="w-full h-full">
                        <source media="(min-width: 768px)" srcset="{{ $banner->image_desktop }}">
                        <img src="{{ $banner->image_mobile }}"
                             alt="{{ $banner->title }}"
                             class="w-full h-full object-cover">
                    </picture>

                    <!-- Gradient Vignette -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-black/30"></div>

                    <!-- Slide Content Overlay -->
                    <div class="absolute inset-0 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col justify-end pb-12 sm:pb-16 text-white">
                        <span class="inline-block px-3 py-1 bg-amber-500/90 text-white text-xs font-black uppercase tracking-widest rounded-full mb-3 backdrop-blur-sm shadow w-max">
                            Featured Highlight
                        </span>
                        <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black tracking-tight max-w-3xl leading-tight drop-shadow-md">
                            {{ $banner->title }}
                        </h2>

                        @if ($banner->cta_link)
                            <div class="mt-5">
                                <a href="{{ $banner->cta_link }}"
                                   class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-xs font-black uppercase tracking-wider bg-amber-500 hover:bg-amber-400 text-zinc-950 shadow-lg hover:shadow-amber-500/40 transform hover:-translate-y-0.5 transition-all">
                                    <span>Explore Now</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Carousel Indicators -->
        @if ($banners->count() > 1)
            <div class="absolute bottom-4 left-0 right-0 flex items-center justify-center gap-2 z-20">
                @foreach ($banners as $index => $banner)
                    <button
                        type="button"
                        @click="activeSlide = {{ $index }}"
                        class="h-2 rounded-full transition-all duration-300"
                        :class="activeSlide === {{ $index }} ? 'w-8 bg-amber-500' : 'w-2 bg-white/50 hover:bg-white/80'"
                        aria-label="Slide {{ $index + 1 }}"
                    ></button>
                @endforeach
            </div>

            <!-- Left / Right Nav Arrows -->
            <button
                type="button"
                @click="prev()"
                class="hidden sm:flex absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/40 hover:bg-black/70 text-white items-center justify-center backdrop-blur-sm border border-white/20 transition z-20"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <button
                type="button"
                @click="next()"
                class="hidden sm:flex absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/40 hover:bg-black/70 text-white items-center justify-center backdrop-blur-sm border border-white/20 transition z-20"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        @endif
    </section>
@endif
