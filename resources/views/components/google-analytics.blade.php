@if(config('services.google_analytics.tracking_id'))
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.tracking_id') }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ config('services.google_analytics.tracking_id') }}', {
        page_title: '{{ $title ?? 'Concours Photo DINOR' }}',
        page_location: window.location.href,
        custom_map: {
            'custom_dimension_1': 'user_type'
        }
    });

    // Événements personnalisés
    @auth
        gtag('config', '{{ config('services.google_analytics.tracking_id') }}', {
            user_id: '{{ auth()->id() }}',
            custom_dimension_1: 'authenticated'
        });
    @else
        gtag('config', '{{ config('services.google_analytics.tracking_id') }}', {
            custom_dimension_1: 'guest'
        });
    @endauth

    // Fonction pour tracker les votes
    window.trackVote = function(candidateId, candidateName) {
        gtag('event', 'vote', {
            event_category: 'engagement',
            event_label: candidateName,
            candidate_id: candidateId,
            value: 1
        });
    };

    // Fonction pour tracker les inscriptions
    window.trackRegistration = function(candidateName) {
        gtag('event', 'registration', {
            event_category: 'conversion',
            event_label: candidateName,
            value: 1
        });
    };

    // Fonction pour tracker les connexions
    window.trackLogin = function(method) {
        gtag('event', 'login', {
            event_category: 'engagement',
            event_label: method,
            method: method
        });
    };

    // Tracker la durée de visite
    let startTime = Date.now();
    window.addEventListener('beforeunload', function() {
        let timeSpent = Math.round((Date.now() - startTime) / 1000);
        gtag('event', 'time_on_site', {
            event_category: 'engagement',
            value: timeSpent
        });
    });
</script>
@endif