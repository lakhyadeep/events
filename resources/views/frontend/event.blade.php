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
        @include('frontend.partials.about')

        <!-- Sponsor Tiers (Organised by, Title, Hospitality, Trophy & Associate Partners) -->
        @include('frontend.partials.sponsors')

        <!-- Vote for the Best — Sharad Samman 2026 Section -->
        <section class="pujas-voting-section" id="pujas">
            <!-- Awards to be Presented Matrix -->
            @include('frontend.partials.awards')

            <!-- Voting Hub (Best Puja, Best Pratima, Best Lighting) -->
            @include('frontend.partials.voting')
        </section>

        <!-- Glimpses of Puja 2026 (Vertical 9:16 Video Shorts Carousel) -->
        @include('frontend.partials.gallery')

        <!-- Event Timeline Tracker -->
        @include('frontend.partials.timeline')

        <!-- Official Guidelines & Terms -->
        @include('frontend.partials.terms')
    </main>

    <!-- Site Footer & Social Links -->
    @include('frontend.partials.footer')

    <!-- Interactive Lightbox & Voting Modals -->
    @include('frontend.partials.modals')

</x-layouts.microsite>
