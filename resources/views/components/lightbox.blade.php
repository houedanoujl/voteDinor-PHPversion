@props(['candidate'])

<div id="lightbox-{{ $candidate->id }}" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-4xl w-full max-h-full overflow-auto bg-white rounded-lg shadow-2xl">
        <!-- Bouton fermer -->
        <button onclick="closeLightbox({{ $candidate->id }})"
                class="absolute top-4 right-4 z-10 bg-white bg-opacity-80 hover:bg-opacity-100 text-gray-800 rounded-full p-2 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Contenu de la lightbox -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
            <!-- Image -->
            <div class="relative">
                <img src="{{ $candidate->getPhotoUrl() }}"
                     alt="{{ $candidate->prenom }} {{ $candidate->nom }}"
                     class="w-full h-full object-cover rounded-t-lg lg:rounded-l-lg lg:rounded-t-none">

                <!-- Overlay avec informations -->
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-6 text-white">
                    <h3 class="text-2xl font-bold">{{ $candidate->prenom }} {{ $candidate->nom }}</h3>
                    <p class="text-lg opacity-90">{{ $candidate->email }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-yellow-400 text-xl mr-2">⭐</span>
                        <span class="text-xl font-bold">{{ $candidate->votes()->count() }} votes</span>
                    </div>
                </div>
            </div>

            <!-- Informations et actions -->
            <div class="p-6 flex flex-col justify-between">
                <!-- Informations du candidat -->
                <div class="space-y-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">{{ $candidate->prenom }} {{ $candidate->nom }}</h2>
                        <p class="text-gray-600">{{ $candidate->email }}</p>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $candidate->votes()->count() }}</div>
                            <div class="text-sm text-gray-600">Votes</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $candidate->created_at->format('d/m/Y') }}</div>
                            <div class="text-sm text-gray-600">Inscrit le</div>
                        </div>
                    </div>

                    <!-- Description ou bio -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-800 mb-2">À propos</h4>
                        <p class="text-gray-600">
                            {{ $candidate->description ?? 'Ce candidat participe au concours DINOR. Soutenez-le en votant !' }}
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-4">
                    <!-- Bouton de vote -->
                    <div class="text-center">
                        <button onclick="voteForCandidate({{ $candidate->id }})"
                                class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white py-3 px-6 rounded-lg font-semibold hover:from-purple-700 hover:to-purple-800 transition-all transform hover:scale-105">
                            <span class="flex items-center justify-center">
                                <span class="text-xl mr-2">❤️</span>
                                Voter pour {{ $candidate->prenom }}
                            </span>
                        </button>
                    </div>

                    <!-- Boutons de partage -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">Partager</h4>
                        <x-share-buttons
                            :url="route('contest.home') . '#candidate-' . $candidate->id"
                            :title="'Votez pour ' . $candidate->prenom . ' ' . $candidate->nom . ' au concours DINOR !'"
                            :description="'Découvrez ce candidat et votez pour lui dans le concours DINOR !'"
                            :image="$candidate->getPhotoUrl()"
                        />
                    </div>

                    <!-- Liens rapides -->
                    <div class="flex space-x-2">
                        <a href="{{ route('contest.home') }}"
                           class="flex-1 bg-gray-100 text-gray-700 py-2 px-4 rounded-lg text-center hover:bg-gray-200 transition-colors">
                            Voir tous les candidats
                        </a>
                        <a href="{{ route('contest.rules') }}"
                           class="flex-1 bg-gray-100 text-gray-700 py-2 px-4 rounded-lg text-center hover:bg-gray-200 transition-colors">
                            Règles du concours
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openLightbox(candidateId) {
    const lightbox = document.getElementById(`lightbox-${candidateId}`);
    if (lightbox) {
        lightbox.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeLightbox(candidateId) {
    const lightbox = document.getElementById(`lightbox-${candidateId}`);
    if (lightbox) {
        lightbox.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function voteForCandidate(candidateId) {
    // Vérifier si l'utilisateur est connecté
    @if(auth()->check())
        // Envoyer le vote via AJAX
        fetch('/vote', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                candidate_id: candidateId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher un message de succès
                showNotification('Vote enregistré avec succès !', 'success');
                // Mettre à jour le compteur de votes
                const voteCount = document.querySelector(`#lightbox-${candidateId} .text-purple-600`);
                if (voteCount) {
                    voteCount.textContent = parseInt(voteCount.textContent) + 1;
                }
            } else {
                showNotification(data.message || 'Erreur lors du vote', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur lors du vote', 'error');
        });
    @else
        // Rediriger vers la page de connexion
        window.location.href = '{{ route("login") }}';
    @endif
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Fermer la lightbox en cliquant à l'extérieur
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed') && event.target.classList.contains('bg-black')) {
        event.target.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
});

// Fermer la lightbox avec la touche Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const lightboxes = document.querySelectorAll('[id^="lightbox-"]');
        lightboxes.forEach(lightbox => {
            if (!lightbox.classList.contains('hidden')) {
                lightbox.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        });
    }
});
</script>
