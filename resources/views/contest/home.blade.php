@extends('layouts.app')

@section('title', 'Concours Photo Rétro')
@section('description', 'Participez au concours photo vintage DINOR - Cuisine des années 60. Votez pour vos photos préférées !')

@section('content')
<div class="min-h-screen">
    <!-- Hero Section avec vidéo -->
    <section class="relative bg-black py-20 px-4 overflow-hidden min-h-screen">
        <!-- Background video -->
        <video class="absolute inset-0 w-full h-full object-cover" autoplay muted loop playsinline>
            <source src="{{ asset('videos/video.mp4') }}" type="video/mp4">
        </video>
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/60"></div>

        <div class="relative z-10 max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center min-h-screen px-6 py-12">
                <!-- Contenu texte (CTA) -> à gauche -->
                <div class="text-center lg:text-left text-white">
                    <!-- Titre principal avec fond pour améliorer la lisibilité -->
                    <div class="bg-black/50 rounded-2xl p-8 mb-8">
                        <div class="flex">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-0 tracking-tight flex items-center justify-center">
                            Mon festival des grillades
                        </h1>
                         <img class='contempt' src="{{asset('images/LOGO_DINOR_monochrome.svg')}}" style="width: 200px; height: 200px;filter: invert(1)" alt="Dinor"/>

                        </div>
                        

                        <div class="text-lg md:text-xl text-yellow-300 mt-4 font-medium">
                            Fais nous vivre ton expérience !
                        </div>
                    </div>


                    <!-- Boutons d'action avec fond pour améliorer la lisibilité -->
                    <div class="bg-black/40 rounded-2xl p-6 mb-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 w-full max-w-2xl mx-auto lg:mx-0 mb-6">
                        @guest
                            <!-- Boutons pour les invités -->
                                <button onclick="openVoterModal()" class="btn-dinor w-full">
                                    Voter
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                <button onclick="openCandidateModal()" class="btn-dinor btn-dinor-accent w-full">
                                    Poster ma photo du FGA
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                <!-- <a href="{{ route('login') }}" class="btn-dinor btn-dinor-secondary w-full">
                                    Connexion
                                </a> -->
                        @else
                            <!-- Boutons pour les utilisateurs connectés -->
                                @if(!auth()->user()->candidate)
                                    <button onclick="openCandidateModal()" class="btn-dinor btn-dinor-accent w-full">
                                        Poster ma photo du FGA
                                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                @endif
                                <a href="{{ route('dashboard') }}" class="btn-dinor btn-dinor-secondary w-full">
                                    Mon tableau de bord
                                </a>
                        @endguest
                        </div>
                        
                  
                    </div>
                </div>
                
                <!-- Classement (podium) -> à droite -->
                <div class="text-white">
                    <div class="classment">
                        @php($top = $stats['top_candidates'])
                        @if($top->count() > 0)
                            <!-- Fond pour améliorer la lisibilité du classement -->
                            <div class="bg-black/50 rounded-2xl p-8 mb-8">
                            
                                <div class="grid grid-cols-3 gap-4 items-end mb-6">
                                    <!-- 2ème place -->
                                    <div class="text-center">
                                        @if($top->get(1))
                                            <div class="bg-white/15  rounded-xl p-4 border border-white/20">
                                                <div class="w-16 h-16 rounded-full mx-auto mb-3 overflow-hidden border-3 border-gray-400 shadow-lg">
                                                    <img src="{{ $top->get(1)->getPhotoUrl() ?: asset('images/placeholder-avatar.svg') }}" alt="{{ $top->get(1)->full_name }}" class="w-full h-full object-cover">
                                                </div>
                                                <div class="text-sm font-semibold text-white">{{ Str::limit($top->get(1)->full_name, 12) }}</div>
                                                <div class="text-xs text-yellow-300 font-medium">{{ $top->get(1)->votes_count }} votes</div>
                                                <div class="mt-2 text-2xl font-bold text-gray-400">2</div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- 1ère place -->
                                    <div class="text-center">
                                        @if($top->get(0))
                                            <div class="bg-gradient-to-b from-yellow-400/20 to-yellow-600/20  rounded-xl p-5 border-2 border-yellow-400/40 transform scale-110 shadow-2xl">
                                                <div class="w-20 h-20 rounded-full mx-auto mb-3 overflow-hidden border-4 border-yellow-400 shadow-xl">
                                                    <img src="{{ $top->get(0)->getPhotoUrl() ?: asset('images/placeholder-avatar.svg') }}" alt="{{ $top->get(0)->full_name }}" class="w-full h-full object-cover">
                                                </div>
                                                <div class="text-base font-bold text-white">{{ Str::limit($top->get(0)->full_name, 12) }}</div>
                                                <div class="text-sm text-yellow-300 font-semibold">{{ $top->get(0)->votes_count }} votes</div>
                                                <div class="mt-2 text-3xl font-extrabold text-yellow-400">1</div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- 3ème place -->
                                    <div class="text-center">
                                        @if($top->get(2))
                                            <div class="bg-white/15  rounded-xl p-4 border border-white/20">
                                                <div class="w-16 h-16 rounded-full mx-auto mb-3 overflow-hidden border-3 border-orange-600 shadow-lg">
                                                    <img src="{{ $top->get(2)->getPhotoUrl() ?: asset('images/placeholder-avatar.svg') }}" alt="{{ $top->get(2)->full_name }}" class="w-full h-full object-cover">
                                                </div>
                                                <div class="text-sm font-semibold text-white">{{ Str::limit($top->get(2)->full_name, 12) }}</div>
                                                <div class="text-xs text-yellow-300 font-medium">{{ $top->get(2)->votes_count }} votes</div>
                                                <div class="mt-2 text-2xl font-bold text-orange-600">3</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                               
                            </div>
                            <div class="bg-black/50 rounded-2xl p-8 mb-8">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 w-full max-w-2xl mx-auto lg:mx-0 mb-6">
                                <a href="{{ route('contest.ranking') }}" class="btn-dinor btn-dinor-accent px-6 py-3 w-full">Classement complet</a>
                                <button onclick="scrollToGallery()" class="btn-dinor btn-dinor-outline text-lg px-6 py-3 w-full">Voir les photos</button>
                                </div>  
                            </div>
                        @endif
                    </div>
                </div>


            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 px-4 bg-gray-50">
        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center card-dinor-clean">
                    <div class="text-4xl font-bold mb-2" style="color: var(--accent);">{{ $stats['total_candidates'] }}</div>
                    <p class="text-gray-700 font-semibold">Candidats</p>
                </div>
                <div class="text-center card-dinor-clean">
                    <div class="text-4xl font-bold mb-2" style="color: var(--primary);">{{ $stats['total_votes'] }}</div>
                    <p class="text-gray-700 font-semibold">Votes</p>
                </div>
                <div class="text-center card-dinor-clean">
                    <div class="text-4xl mb-2" style="color: var(--muted);">★</div>
                    <p class="text-gray-700"><a href="{{ route('contest.ranking') }}" class="font-bold btn-dinor-outline" style="color: var(--accent); text-decoration: none;">Voir le classement</a></p>
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

    <!-- Candidats Gallery -->
    <section id="gallery" class="py-16 px-4 bg-white">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: var(--secondary);">
                    Votez pour vos candidats préférés
                </h2>
                <div class="text-lg text-gray-600 mb-6">
                    Découvrez tous les participants au concours
                </div>
            </div>

            @livewire('candidates-gallery')
        </div>
    </section>

    <!-- Modales -->
    <!-- Modal Votant -->
    <div id="voterModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
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
    <div id="candidateModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Poster ma photo du FGA</h2>
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

    // Anciennes fonctions de streaming (désactivées). Protection si éléments absents
    function loadStream(type) {
        const video = document.getElementById('hero-video');
        const videoInfo = document.getElementById('video-info');
        if (!video) return;
        if (type === 'standard') {
            video.src = '{{ route("video.stream", "video.mp4") }}';
            if (videoInfo) videoInfo.innerHTML = '<div class="flex justify-between items-center"><span>🎥 Streaming Standard</span><span id="video-status" class="text-green-500">● En ligne</span></div>';
        } else if (type === 'hls') {
            video.src = '{{ route("video.hls", "video.mp4") }}';
            if (videoInfo) videoInfo.innerHTML = '<div class="flex justify-between items-center"><span>📱 Streaming HLS</span><span id="video-status" class="text-green-500">● En ligne</span></div>';
        }
        video.load();
    }

    async function loadVideoInfo() {
        const videoInfo = document.getElementById('video-info');
        if (!videoInfo) return;
        try {
            const response = await fetch('{{ route("video.info", "video.mp4") }}');
            const data = await response.json();
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

        // Charger les informations de la vidéo si la zone existe
        if (document.getElementById('video-info')) {
            loadVideoInfo();
        }
    });
</script>
@endpush
@endsection
