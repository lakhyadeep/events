<x-layouts.microsite :event="$event" :meta-title="$event->meta_title ?: 'Dibrugarh 24x7 - Festive Awards & Navigation'"
    :meta-description="$event->meta_description ?: 'Dibrugarh Durgo Utsav 2026'" :og-image="$event->og_image_url"
    :canonical-url="route('events.show', ['event' => $event->slug])">

    @if (!empty($isPreview))
        <!-- Verification Preview Banner -->
        <div class="bg-warning text-dark py-2 px-3 text-center fw-bold sticky-top shadow-sm d-flex flex-wrap align-items-center justify-content-center gap-2" style="z-index: 99999;">
            <span class="badge bg-dark text-warning text-uppercase px-2 py-1">
                <i class="bi bi-eye-fill me-1"></i> Preview Mode
            </span>
            <span>Displaying Draft &amp; Published Awards for Verification Before Publishing</span>
            @auth
                <a href="{{ url('/admin/winners') }}" class="btn btn-sm btn-dark ms-2 py-0 px-2 text-warning fw-semibold text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Back to Admin
                </a>
            @endauth
        </div>
    @endif

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