@extends('layouts.app')

@section('title', 'Concours Photo Rétro')
@section('description', 'Participez au concours photo vintage DINOR - Cuisine des années 60. Votez pour vos photos préférées !')

@section('content')
<div class="min-h-screen">
    <!-- Hero Section -->
    <section class="bg-gradient-dinor text-white py-20 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl md:text-7xl font-retro font-bold mb-4 text-shadow-lg">
                Concours Photo Rétro DINOR
            </h1>
            <h2 class="text-2xl md:text-3xl font-retro mb-6 text-dinor-cream">
                Cuisine Vintage des Années 60
            </h2>
            <p class="text-lg md:text-xl mb-8 max-w-2xl mx-auto">
                Participez ou votez for vos photos préférées ! Redécouvrez l'art culinaire d'antan avec DINOR.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <button onclick="openParticipationModal()" class="btn-dinor text-lg px-8 py-4">
                        🎯 Participer au concours
                    </button>
                @else
                    <a href="{{ route('auth.redirect', 'google') }}" class="btn-dinor text-lg px-8 py-4 inline-block text-center">
                        🎯 Participer avec Google
                    </a>
                @endauth
                <button onclick="scrollToGallery()" class="border-2 border-white text-white hover:bg-white hover:text-dinor-orange transition-all duration-300 px-8 py-4 text-lg font-bold rounded-lg">
                    ❤️ Voir les candidats
                </button>
            </div>
        </div>
    </section>

    <!-- Candidats Gallery -->
    <section id="gallery" class="py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl font-retro font-bold text-center mb-4 text-dinor-brown">
                Votez pour vos photos préférées
            </h2>
            <p class="text-center text-lg text-dinor-brown mb-12">
                @auth
                    🔒 1 vote par candidat par jour par compte connecté
                @else
                    🔒 Connectez-vous pour pouvoir voter
                @endauth
            </p>
            
            @livewire('candidates-gallery')
        </div>
    </section>

    <!-- Modal de participation -->
    @auth
        @livewire('candidate-registration-modal')
    @endauth
</div>

@push('scripts')
<script>
    function scrollToGallery() {
        document.getElementById('gallery').scrollIntoView({ behavior: 'smooth' });
    }
    
    function openParticipationModal() {
        window.livewire.emit('openModal');
    }
</script>
@endpush
@endsection