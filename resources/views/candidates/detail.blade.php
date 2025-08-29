@extends('layouts.app')

@section('title', $candidate->prenom . ' ' . $candidate->nom . ' - Concours Photo DINOR')
@section('description', 'Votez pour ' . $candidate->prenom . ' ' . $candidate->nom . ' au Concours Photo DINOR ! Découvrez sa candidature et soutenez-la.')

{{-- Open Graph Meta Tags --}}
@section('og_meta')
    <meta property="og:title" content="{{ $candidate->prenom }} {{ $candidate->nom }} - Concours Photo DINOR">
    <meta property="og:description" content="Votez pour {{ $candidate->prenom }} {{ $candidate->nom }} au Concours Photo DINOR ! Découvrez sa candidature et soutenez-la.">
    <meta property="og:image" content="{{ $candidate->getPhotoUrl() }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Concours Photo DINOR">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $candidate->prenom }} {{ $candidate->nom }} - Concours Photo DINOR">
    <meta name="twitter:description" content="Votez pour {{ $candidate->prenom }} {{ $candidate->nom }} au Concours Photo DINOR !">
    <meta name="twitter:image" content="{{ $candidate->getPhotoUrl() }}">

    {{-- Additional Meta --}}
    <meta name="author" content="Concours Photo DINOR">
    <meta name="robots" content="index, follow">
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-red-50">
    <!-- Header avec navigation -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <a href="{{ route('contest.home') }}" class="flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour au concours
                </a>

                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-sm text-gray-600">Bonjour, {{ auth()->user()->prenom }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                                Déconnexion
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">Connexion</a>
                        <a href="{{ route('register') }}" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">Inscription</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- En-tête du candidat -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <!-- Photo du candidat -->
                <div class="relative group cursor-pointer overflow-hidden" onclick="openLightbox()">
                    <img
                        src="{{ $candidate->getPhotoUrl() }}"
                        alt="Photo de {{ $candidate->prenom }} {{ $candidate->nom }}"
                        class="w-full h-96 lg:h-full object-cover transition-transform duration-200 group-hover:scale-105"
                        id="candidatePhoto"
                    >
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold text-gray-800 flex items-center">
                        <svg class="w-4 h-4 mr-1 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $votesCount }} votes
                    </div>
                    <!-- indication -->
                    <p>cliquez sur l'image l'afficher en plein écran </p>
                    <!-- Overlay avec icône de zoom -->
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-200 flex items-center justify-center">
                        <div class="transform scale-0 group-hover:scale-100 transition-transform duration-200">
                            <div class="bg-white/90 backdrop-blur-sm rounded-full p-3">
                                <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations du candidat -->
                <div class="p-8 flex flex-col justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">
                            {{ $candidate->prenom }} {{ $candidate->nom }}
                        </h1>
                        <p class="text-xl text-gray-600 mb-6">
                            Candidat au Concours Photo DINOR
                        </p>

                        <!-- Section de vote -->
                        <div class="mb-8">
                            @guest
                                <div class="bg-gray-50 rounded-xl p-6 text-center">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Connectez-vous pour voter</h3>
                                    <p class="text-gray-600 mb-4">Vous devez être connecté pour voter pour ce candidat</p>
                                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                        </svg>
                                        Se connecter
                                    </a>
                                </div>
                            @else
                                @if($hasVotedToday)
                                    <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
                                        <svg class="w-12 h-12 text-green-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-green-900 mb-2">Vous avez déjà voté aujourd'hui</h3>
                                        <p class="text-green-700">Merci pour votre vote ! Vous pourrez voter à nouveau demain.</p>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Soutenez {{ $candidate->prenom }} !</h3>
                                        <button
                                            onclick="voteForCandidate({{ $candidate->id }})"
                                            class="inline-flex items-center px-8 py-4 bg-red-600 hover:bg-red-700 text-white font-bold text-lg rounded-xl transition-colors transform hover:scale-105">
                                            <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                            </svg>
                                            Voter pour {{ $candidate->prenom }}
                                        </button>
                                        <p class="text-sm text-gray-500 mt-2">1 vote par jour par candidat</p>
                                    </div>
                                @endif
                            @endguest
                        </div>
                    </div>

                    <!-- Partage social -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            Partager cette candidature
                        </h3>
                        <div class="flex space-x-3">
                            <ul class="flex flex-col gap-3 w-full">
                                <li> <button onclick="shareOnFacebook()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                Facebook
                            </button></li>
                                <li> <button onclick="shareOnWhatsApp()" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                </svg>
                                WhatsApp
                            </button></li>
                                <li> <button onclick="shareOnTwitter()" class="flex-1 bg-blue-400 hover:bg-blue-500 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                                Twitter
                            </button></li>
                            </ul>



                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Votes récents -->
        @if($recentVotes->count() > 0)
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                </svg>
                Votes récents
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recentVotes as $vote)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ $vote->user ? $vote->user->name : 'Visiteur' }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ $vote->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Call to action -->
        <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-2xl shadow-xl p-8 text-center text-white">
            <h2 class="text-3xl font-bold mb-4">Soutenez {{ $candidate->prenom }} !</h2>
            <p class="text-xl mb-6 opacity-90">
                Partagez cette candidature pour aider {{ $candidate->prenom }} à gagner le concours
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('contest.home') }}" class="inline-flex items-center px-6 py-3 bg-white text-orange-600 font-medium rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Voir tous les candidats
                </a>
                <button onclick="shareOnWhatsApp()" class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                    </svg>
                    Partager sur WhatsApp
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function voteForCandidate(candidateId) {
    // Appeler l'API de vote
    fetch('/vote/' + candidateId, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur réseau');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Vote enregistré avec succès !', 'success');
            // Mettre à jour le compteur de votes
            const voteCountElement = document.querySelector('.bg-white\\/90');
            if (voteCountElement) {
                const voteText = voteCountElement.querySelector('span');
                if (voteText) {
                    voteText.textContent = data.votes_count;
                }
            }
            // Recharger la page pour mettre à jour l'état
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Erreur lors du vote', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur lors du vote', 'error');
    });
}

function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(`Votez pour {{ $candidate->prenom }} {{ $candidate->nom }} au Concours Photo DINOR !`);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${text}`, '_blank');
}

function shareOnWhatsApp() {
    const text = encodeURIComponent(`Votez pour {{ $candidate->prenom }} {{ $candidate->nom }} au Concours Photo DINOR ! ${window.location.href}`);
    window.open(`https://wa.me/?text=${text}`, '_blank');
}

function shareOnTwitter() {
    const text = encodeURIComponent(`Votez pour {{ $candidate->prenom }} {{ $candidate->nom }} au Concours Photo DINOR !`);
    const url = encodeURIComponent(window.location.href);
    window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank');
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-xl shadow-lg z-50 animate-fade-in-up ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            ${message}
        </div>
    `;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Fonctionnalité Lightbox
function openLightbox() {
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const candidatePhoto = document.getElementById('candidatePhoto');

    lightboxImg.src = candidatePhoto.src;
    lightbox.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Animation d'ouverture
    setTimeout(() => {
        lightbox.classList.add('opacity-100');
        lightbox.classList.remove('opacity-0');
    }, 10);
}

function closeLightbox() {
    const lightbox = document.getElementById('lightbox');

    lightbox.classList.add('opacity-0');
    lightbox.classList.remove('opacity-100');

    setTimeout(() => {
        lightbox.style.display = 'none';
        document.body.style.overflow = 'auto';
    }, 300);
}

// Fermer la lightbox avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLightbox();
    }
});
</script>

<!-- Lightbox Modal -->
<div id="lightbox" class="fixed inset-0 bg-black/90 backdrop-blur-sm z-50 flex items-center justify-center p-4 opacity-0 transition-opacity duration-300" style="display: none;" onclick="closeLightbox()">
    <div class="relative max-w-4xl max-h-full">
        <!-- Bouton de fermeture -->
        <button onclick="closeLightbox()" class="absolute -top-12 right-0 text-white/80 hover:text-white z-60 transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Image en grand -->
        <img
            id="lightbox-img"
            src=""
            alt="Photo de {{ $candidate->prenom }} {{ $candidate->nom }} - Grand format"
            class="max-w-full max-h-full object-contain rounded-lg shadow-2xl"
            onclick="event.stopPropagation()"
        >

        <!-- Informations du candidat dans la lightbox -->
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-6 text-white rounded-b-lg" onclick="event.stopPropagation()">
            <h3 class="text-2xl font-bold mb-2">{{ $candidate->prenom }} {{ $candidate->nom }}</h3>
            <div class="flex items-center space-x-4 text-sm">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $votesCount }} votes
                </div>
                <div class="text-white/80">Concours Photo DINOR</div>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 text-white/60 text-sm text-center">
        <p>Cliquez en dehors de l'image ou appuyez sur <kbd class="bg-white/20 px-2 py-1 rounded">Échap</kbd> pour fermer</p>
    </div>
</div>

@endsection
