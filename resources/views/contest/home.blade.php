@extends('layouts.app')

@section('title', 'Concours Photo Rétro')
@section('description', 'Participez au concours photo vintage DINOR - Cuisine des années 60. Votez pour vos photos préférées !')

@section('content')
<div class="min-h-screen">
    <!-- Hero Section avec vidéo multistream -->
    <section class="relative bg-black py-20 px-4 overflow-hidden">
        <!-- Background video -->
        <video class="absolute inset-0 w-full h-full object-cover" autoplay muted loop playsinline>
            <source src="{{ asset('videos/video.mp4') }}" type="video/mp4">
        </video>
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/50"></div>

        <div class="relative z-10 max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Contenu texte -->
                <div class="text-center lg:text-left text-white">
                    <!-- Titre principal épuré -->
                    <h1 class="text-4xl md:text-6xl font-bold mb-6 tracking-tight">
                        Concours Photo
                        <span class="block text-orange-400">DINOR</span>
                    </h1>


                    <!-- Boutons d'action épurés -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 w-full max-w-2xl mx-auto lg:mx-0">
                        @guest
                            <!-- Boutons pour les invités -->
                                <button onclick="openVoterModal()" class="bg-orange-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-orange-700 transition-colors flex items-center justify-center w-full">
                                    🗳️ Devenir Votant
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                <button onclick="openCandidateModal()" class="bg-green-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-green-700 transition-colors flex items-center justify-center w-full">
                                    📸 Devenir Candidat
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                <a href="{{ route('login') }}" class="bg-gray-900 text-white px-6 py-3 rounded-lg font-medium hover:bg-gray-800 transition-colors w-full text-center">
                                    Connexion
                                </a>
                        @else
                            <!-- Boutons pour les utilisateurs connectés -->
                                @if(!auth()->user()->candidate)
                                    <button onclick="openCandidateModal()" class="bg-green-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-green-700 transition-colors flex items-center justify-center w-full">
                                        📸 Devenir Candidat
                                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                @endif
                                <a href="{{ route('dashboard') }}" class="bg-gray-900 text-white px-6 py-3 rounded-lg font-medium hover:bg-gray-800 transition-colors w-full text-center">
                                    Mon tableau de bord
                                </a>
                        @endguest
                        <!-- Bouton voir candidats avec même largeur -->
                        <button onclick="scrollToGallery()" class="bg-white/80 backdrop-blur text-gray-900 px-6 py-3 rounded-lg font-medium hover:bg-white transition-colors w-full col-span-1 sm:col-span-2 lg:col-span-3">
                            Voir les candidats →
                        </button>
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

    <!-- Formulaire d'inscription -->
    <!-- <section id="inscription" class="py-16 px-4 bg-white">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900">
                    Rejoignez le concours
                </h2>
                <p class="text-xl text-gray-600 mb-8">
                    Partagez votre passion culinaire et tentez de gagner de superbes prix !
                </p>
            </div>

            <div class="text-center">
                <p class="text-gray-600">
                    Cliquez sur les boutons ci-dessus pour créer votre compte et participer au concours !
                </p>
            </div>
        </div>
    </section> -->

    <!-- Candidats Gallery simple -->
    <section id="gallery" class="py-16 px-4 bg-gray-50">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900">
                    Votez pour vos photos préférées
                </h2>

            </div>

            @livewire('candidates-gallery')
        </div>
    </section>

    <!-- Modales -->
    <!-- Modal Votant -->
    <div id="voterModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Créer un compte votant</h2>
                    <button onclick="closeVoterModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                @livewire('voter-registration-form')
            </div>
        </div>
    </div>

    <!-- Modal Candidat -->
    <div id="candidateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Devenir candidat</h2>
                    <button onclick="closeCandidateModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                @livewire('candidate-registration-form')
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Fonctions pour les modales
    function openVoterModal() {
        document.getElementById('voterModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeVoterModal() {
        document.getElementById('voterModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openCandidateModal() {
        document.getElementById('candidateModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCandidateModal() {
        document.getElementById('candidateModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Fermer les modales en cliquant à l'extérieur
    document.getElementById('voterModal').addEventListener('click', function(e) {
        if (e.target === this) closeVoterModal();
    });

    document.getElementById('candidateModal').addEventListener('click', function(e) {
        if (e.target === this) closeCandidateModal();
    });

    // Fermer avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeVoterModal();
            closeCandidateModal();
        }
    });

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
