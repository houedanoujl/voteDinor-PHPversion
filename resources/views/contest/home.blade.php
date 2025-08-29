@extends('layouts.app')

@section('title', 'Concours Photo Rétro')
@section('description', 'Participez au concours photo vintage DINOR - Cuisine des années 60. Votez pour vos photos préférées !')

@section('content')
<div class="min-h-screen">
    <!-- Hero Section avec vidéo multistream -->
    <section class="relative bg-white py-20 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Contenu texte -->
                <div class="text-center lg:text-left">
                    <!-- Titre principal épuré -->
                    <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6 tracking-tight">
                        Concours Photo
                        <span class="block text-orange-600">DINOR</span>
                    </h1>

                    <!-- Sous-titre simple -->
                    <p class="text-xl text-gray-600 mb-12 max-w-2xl mx-auto lg:mx-0">
                        Participez au concours photo cuisine vintage. Votez pour vos créations préférées.
                    </p>

                    <!-- Boutons d'action épurés -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start items-center">
                        @auth
                            <livewire:candidate-registration-modal />
                        @else
                            <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                                <a href="{{ route('login') }}" class="bg-gray-900 text-white px-6 py-3 rounded-lg font-medium hover:bg-gray-800 transition-colors">
                                    Connexion
                                </a>
                                <a href="{{ route('register') }}" class="bg-orange-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-orange-700 transition-colors">
                                    Participer au concours
                                </a>
                            </div>
                        @endauth

                        <!-- Bouton voir candidats avec mêmes dimensions -->
                        <button onclick="scrollToGallery()" class="bg-gray-100 text-gray-900 px-6 py-3 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                            Voir les candidats →
                        </button>
                    </div>
                </div>

                <!-- Section vidéo multistream -->
                <div class="relative">
                    <div class="bg-gray-100 rounded-2xl p-6 shadow-lg">
                        <div class="aspect-video rounded-xl overflow-hidden bg-black">
                            <video
                                id="hero-video"
                                class="w-full h-full object-cover"
                                controls
                                preload="metadata"
                                poster="{{ asset('images/video-poster.jpg') }}">
                                <source src="{{ route('video.stream', 'video.mp4') }}" type="video/mp4">
                                <source src="{{ route('video.hls', 'video.mp4') }}" type="application/x-mpegURL">
                                Votre navigateur ne supporte pas la lecture vidéo.
                            </video>
                        </div>

                        <!-- Contrôles de streaming -->
                        <div class="mt-4 space-y-3">
                            <div class="flex gap-2">
                                <button onclick="loadStream('standard')" class="flex-1 bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-700 transition-colors">
                                    📺 Streaming Standard
                                </button>
                                <button onclick="loadStream('hls')" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                    📱 Streaming HLS
                                </button>
                            </div>

                            <!-- Informations vidéo -->
                            <div id="video-info" class="text-sm text-gray-600 bg-white p-3 rounded-lg border">
                                <div class="flex justify-between items-center">
                                    <span>🎥 Chargement des informations...</span>
                                    <span id="video-status" class="text-green-500">● En ligne</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

    // Fonctions pour le streaming vidéo
    function loadStream(type) {
        const video = document.getElementById('hero-video');
        const videoInfo = document.getElementById('video-info');

        if (type === 'standard') {
            video.src = '{{ route("video.stream", "video.mp4") }}';
            videoInfo.innerHTML = '<div class="flex justify-between items-center"><span>🎥 Streaming Standard</span><span id="video-status" class="text-green-500">● En ligne</span></div>';
        } else if (type === 'hls') {
            video.src = '{{ route("video.hls", "video.mp4") }}';
            videoInfo.innerHTML = '<div class="flex justify-between items-center"><span>📱 Streaming HLS</span><span id="video-status" class="text-green-500">● En ligne</span></div>';
        }

        video.load();
    }

    // Charger les informations de la vidéo
    async function loadVideoInfo() {
        try {
            const response = await fetch('{{ route("video.info", "video.mp4") }}');
            const data = await response.json();

            const videoInfo = document.getElementById('video-info');
            videoInfo.innerHTML = `
                <div class="flex justify-between items-center">
                    <span>🎥 ${data.size_formatted} - ${data.mime_type}</span>
                    <span id="video-status" class="text-green-500">● En ligne</span>
                </div>
            `;
        } catch (error) {
            console.log('Erreur lors du chargement des informations vidéo:', error);
        }
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

        // Charger les informations de la vidéo
        loadVideoInfo();
    });
</script>
@endpush
@endsection
