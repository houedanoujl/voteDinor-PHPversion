@extends('layouts.app')

@section('title', 'Concours Photo Rétro')
@section('description', 'Participez au concours photo vintage DINOR - Cuisine des années 60. Votez pour vos photos préférées !')

@section('content')
<div class="min-h-screen">
    <!-- Hero Section simple et épuré -->
    <section class="relative bg-white py-20 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <!-- Badge discret -->
            <div class="inline-flex items-center px-3 py-1 bg-gray-100 rounded-full text-sm text-gray-600 mb-8">
                <span class="w-1.5 h-1.5 bg-orange-500 rounded-full mr-2"></span>
                Concours Photo 2024
            </div>

            <!-- Titre principal épuré -->
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6 tracking-tight">
                Concours Photo
                <span class="block text-orange-600">DINOR</span>
            </h1>

            <!-- Sous-titre simple -->
            <p class="text-xl text-gray-600 mb-12 max-w-2xl mx-auto">
                Participez au concours photo cuisine vintage. Votez pour vos créations préférées.
            </p>

            <!-- Boutons d'action épurés -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                @auth
                    <livewire:candidate-registration-modal />
                @else
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('login') }}" class="bg-gray-900 text-white px-6 py-3 rounded-lg font-medium hover:bg-gray-800 transition-colors">
                            Connexion
                        </a>
                        <a href="{{ route('register') }}" class="bg-orange-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-orange-700 transition-colors">
                            Inscription
                        </a>
                    </div>

                    <!-- OAuth épuré -->
                    <div class="text-center mt-6">
                        <div class="flex gap-2 justify-center">
                            <a href="{{ route('auth.redirect', 'google') }}" class="flex items-center justify-center w-10 h-10 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                                <svg class="w-5 h-5" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                            </a>
                            <a href="{{ route('auth.redirect', 'facebook') }}" class="flex items-center justify-center w-10 h-10 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endauth

                <!-- Bouton voir candidats simplifié -->
                <button onclick="scrollToGallery()" class="mt-6 bg-gray-100 text-gray-900 px-6 py-3 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                    Voir les candidats →
                </button>
            </div>
        </div>
    </section>

    <!-- Stats Section épurée -->
    <section class="py-16 px-4 bg-gray-50">
        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['total_candidates'] }}</div>
                    <p class="text-gray-600">Candidats</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['total_votes'] }}</div>
                    <p class="text-gray-600">Votes</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900 mb-1">🏆</div>
                    <p class="text-gray-600"><a href="{{ route('contest.ranking') }}" class="text-orange-600 hover:text-orange-700 font-medium">Voir le classement</a></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Candidats Gallery simple -->
    <section id="gallery" class="py-16 px-4 bg-white">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900">
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
