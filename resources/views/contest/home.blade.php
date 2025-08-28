@extends('layouts.app')

@section('title', 'Concours Photo Rétro')
@section('description', 'Participez au concours photo vintage DINOR - Cuisine des années 60. Votez pour vos photos préférées !')

@section('content')
<div class="min-h-screen">
    <!-- Hero Section moderne -->
    <section class="relative bg-gradient-dinor text-white py-24 px-4 overflow-hidden">
        <!-- Background pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>

        <div class="relative max-w-6xl mx-auto text-center">
            <!-- Badge -->
            <div class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium mb-8 animate-fade-in-up">
                <span class="w-2 h-2 bg-white rounded-full mr-2"></span>
                Concours Photo Vintage 2024
            </div>

            <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fade-in-up" style="animation-delay: 0.1s;">
                Concours Photo
                <span class="block text-dinor-cream font-retro">Rétro DINOR</span>
            </h1>

            <h2 class="text-xl md:text-2xl font-medium mb-8 text-dinor-cream animate-fade-in-up" style="animation-delay: 0.2s;">
                Cuisine Vintage des Années 60
            </h2>

            <p class="text-lg md:text-xl mb-12 max-w-3xl mx-auto text-dinor-cream/90 animate-fade-in-up" style="animation-delay: 0.3s;">
                Participez ou votez pour vos photos préférées ! Redécouvrez l'art culinaire d'antan avec DINOR.
            </p>

            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center animate-fade-in-up" style="animation-delay: 0.4s;">
                @auth
                    <livewire:candidate-registration-modal />
                @else
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('login') }}" class="btn-dinor text-lg px-8 py-4 inline-block text-center">
                            🔐 Connexion
                        </a>
                        <a href="{{ route('register') }}" class="btn-dinor bg-dinor-olive hover:bg-dinor-brown text-lg px-8 py-4 inline-block text-center">
                            📝 Inscription
                        </a>
                    </div>

                    <div class="text-center mt-6">
                        <p class="text-dinor-cream/80 text-sm mb-4">Ou utilisez l'authentification rapide :</p>
                        <div class="flex gap-3 justify-center">
                            <a href="{{ route('auth.redirect', 'google') }}" class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl text-sm font-medium hover:bg-white/30 transition-all duration-300 flex items-center">
                                <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                Google
                            </a>
                            <a href="{{ route('auth.redirect', 'facebook') }}" class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl text-sm font-medium hover:bg-white/30 transition-all duration-300 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                Facebook
                            </a>
                        </div>
                    </div>
                @endauth

                <button onclick="scrollToGallery()" class="group border-2 border-white/30 text-white hover:bg-white hover:text-dinor-orange transition-all duration-300 px-8 py-4 text-lg font-bold rounded-xl backdrop-blur-sm hover:border-white">
                    <span class="flex items-center">
                        ❤️ Voir les candidats
                        <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </span>
                </button>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 px-4 bg-white">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold text-dinor-orange mb-2">📷</div>
                    <h3 class="text-2xl font-bold text-dinor-brown mb-2">Candidats</h3>
                    <p class="text-dinor-gray-600">Photos soumises</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-dinor-orange mb-2">❤️</div>
                    <h3 class="text-2xl font-bold text-dinor-brown mb-2">Votes</h3>
                    <p class="text-dinor-gray-600">Total des votes</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-dinor-orange mb-2">🏆</div>
                    <h3 class="text-2xl font-bold text-dinor-brown mb-2">Prix</h3>
                    <p class="text-dinor-gray-600">À gagner</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Candidats Gallery moderne -->
    <section id="gallery" class="py-20 px-4 bg-gradient-to-br from-dinor-gray-50 to-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-6 text-dinor-brown">
                    Votez pour vos photos préférées
                </h2>
                <p class="text-xl text-dinor-gray-600 max-w-2xl mx-auto">
                    @auth
                        🔒 1 vote par candidat par jour par compte connecté
                    @else
                        🔒 Connectez-vous pour pouvoir voter
                    @endauth
                </p>
            </div>

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
        document.getElementById('gallery').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    function openParticipationModal() {
        window.livewire.emit('openModal');
    }

    // Intersection Observer pour les animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
            }
        });
    }, observerOptions);

    // Observer les éléments
    document.addEventListener('DOMContentLoaded', () => {
        const elements = document.querySelectorAll('.card-dinor');
        elements.forEach(el => observer.observe(el));
    });
</script>
@endpush
@endsection
