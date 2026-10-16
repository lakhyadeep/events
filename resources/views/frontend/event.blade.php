<x-layouts.microsite :event="$event" :meta-title="$event->meta_title ?: 'Dibrugarh 24x7 - Festive Awards & Navigation'"
    :meta-description="$event->meta_description ?: 'Dibrugarh Durgo Utsav 2026'" :og-image="$event->og_image_url"
    :canonical-url="route('events.show', ['event' => $event->slug])">
    <!-- Top Navigation Header -->
    @include('frontend.partials.header')

    <main>
        <!-- Festive Awards Hero Section -->
        @include('frontend.partials.hero')


        @include('frontend.partials.about')


        @include('frontend.partials.sponsors')


        @include('frontend.partials.awards')


        @include('frontend.partials.explore-all-puja')


        @include('frontend.partials.new')



        @include('frontend.partials.awards-announced')


        @include('frontend.partials.gallery')



        @include('frontend.partials.timeline')


        @include('frontend.partials.terms')

        <!-- Marketing Promotional Banner -->
        @include('frontend.partials.marketing-banner')


    </main>

    <!-- Site Footer & Social Links -->
    @include('frontend.partials.footer')

    <!-- Interactive Lightbox & Voting Modals -->
    @include('frontend.partials.modals')

</x-layouts.microsite>