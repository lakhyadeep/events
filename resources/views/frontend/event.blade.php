<x-layouts.microsite
    :meta-title="$event->meta_title ?: 'Dibrugarh 24x7 - Festive Awards & Navigation'"
    :meta-description="$event->meta_description ?: 'Dibrugarh Durgo Utsav 2026'"
    :og-image="$event->og_image"
>
    <!-- Top Navigation Header -->
    @include('frontend.partials.header')

    <main>
        <!-- Festive Awards Hero Section -->
        @include('frontend.partials.hero')

        <!-- Durgo Utsav About & Mandala Section -->
        @if (!empty($event->about_text))
            @include('frontend.partials.about')
        @endif

        <!-- Sponsor Tiers (Organised by, Title, Hospitality, Trophy & Associate Partners) -->
        @if ($event->sponsors && $event->sponsors->where('is_active', true)->isNotEmpty())
            @include('frontend.partials.sponsors')
        @endif

        <!-- Vote for the Best — Sharad Samman 2026 Section -->
        @if (($event->awards && $event->awards->where('is_active', true)->isNotEmpty()) || (isset($participants) && $participants->isNotEmpty()))
            <section class="pujas-voting-section" id="pujas">
                <!-- Awards to be Presented Matrix -->
                @if ($event->awards && $event->awards->where('is_active', true)->isNotEmpty())
                    @include('frontend.partials.awards')
                @endif

                <!-- Voting Hub (Best Puja, Best Pratima, Best Lighting) -->
                @if (isset($participants) && $participants->isNotEmpty())
                    @include('frontend.partials.voting')
                @endif
            </section>
        @endif

        <!-- Official Award Winners Section -->
        @if (isset($publishedWinners) && $publishedWinners->isNotEmpty())
            @include('frontend.partials.awards-announced')
        @endif

        <!-- Glimpses of Puja (Vertical 9:16 Video Shorts Carousel) -->
        @if ($event->videoShorts && $event->videoShorts->where('is_active', true)->isNotEmpty())
            @include('frontend.partials.gallery')
        @endif

        <!-- Event Timeline Tracker -->
        @if ($event->timelineItems && $event->timelineItems->isNotEmpty())
            @include('frontend.partials.timeline')
        @endif

        <!-- Official Guidelines & Terms -->
        @if (!empty($event->terms_and_conditions))
            @include('frontend.partials.terms')
        @endif
    </main>

    <!-- Site Footer & Social Links -->
    @include('frontend.partials.footer')

    <!-- Interactive Lightbox & Voting Modals -->
    @include('frontend.partials.modals')

</x-layouts.microsite>
