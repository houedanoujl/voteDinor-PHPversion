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
                    <livewire:candidate-registration-modal />
                @else
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('login') }}" class="btn-dinor text-lg px-8 py-4 inline-block text-center">
                            🔐 Connexion
                        </a>
                        <a href="{{ route('register') }}" class="btn-dinor bg-dinor-olive hover:bg-dinor-brown text-lg px-8 py-4 inline-block text-center">
                            📝 Inscription
                        </a>
                    </div>
                    <div class="text-center mt-4">
                        <p class="text-dinor-cream text-sm mb-3">Ou utilisez l'authentification rapide :</p>
                        <div class="flex gap-3 justify-center">
                            <a href="{{ route('auth.redirect', 'google') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                <svg class="w-4 h-4 inline mr-2" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                Google
                            </a>
                            <a href="{{ route('auth.redirect', 'facebook') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                Facebook
                            </a>
                        </div>
                    </div>
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