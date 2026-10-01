<x-layouts.microsite
    :meta-title="$event->meta_title ?: $event->display_name"
    :meta-description="$event->meta_description ?: $event->tagline"
    :og-image="$event->og_image"
>
    <!-- Top Masthead: Dib 24x7 + Event + Presenting Partner + 5 Associate Sponsors -->
    <x-masthead
        :event="$event"
        :presenting-partner="$presentingPartner"
        :associate-sponsors="$associateSponsors"
    />

    <!-- Sticky Main Navigation Bar with 'Vote Now' CTA -->
    <x-navbar :event="$event" />

    <!-- Above-The-Fold (ATF) Banner Carousel (Desktop & Mobile Responsive) -->
    <x-banner-carousel :banners="$event->banners" />

    <!-- Live Countdown Ticker & Editorial Overview -->
    <x-countdown :event="$event" />

    <!-- Participants / Candidates Showcase Section -->
    <section id="participants" class="py-14 sm:py-20 bg-zinc-950 text-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
                <div>
                    <span class="text-xs uppercase font-extrabold tracking-widest text-amber-500 block mb-1">
                        CANDIDATE DIRECTORY
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-white">
                        Participants Showcase
                    </h2>
                </div>
                <p class="text-xs text-zinc-400 max-w-sm">
                    Filter by Kolkata zones, explore pandal concept themes, and discover master idol sculptors.
                </p>
            </div>

            <!-- Livewire 4 Reactive Showcase Component -->
            <livewire:frontend.participants-showcase :event-id="$event->id" />
        </div>
    </section>

    <!-- Reactive Lightbox Modal for Candidate Details & Puja Attributes -->
    <livewire:frontend.candidate-detail-modal />

    <!-- Event Video Shorts (Vertical 9:16 Carousel) -->
    <x-video-shorts :shorts="$event->videoShorts" />

    <!-- Awards Matrix & Winners Podium -->
    <x-awards-winners
        :event="$event"
        :awards="$event->awards"
        :winners="$publishedWinners"
    />

    <!-- Event Timeline & Milestones Tracker -->
    <x-timeline
        :event="$event"
        :items="$event->timelineItems"
    />

    <!-- Terms and Conditions Accordion & Network Footer -->
    <x-terms-footer :event="$event" />

</x-layouts.microsite>
