<div>
    <!-- Messages flash -->
    @if (session()->has('success'))
        <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl animate-fade-in-up">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-8 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl animate-fade-in-up">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="candidates-grid">
        @forelse($candidates as $index => $candidate)
            <div class="group relative overflow-hidden cursor-pointer transform hover:scale-105 transition-all duration-300">
                <!-- Image optimisée directe -->
                <div onclick="openPhotoLightbox('{{ $candidate['photo_url'] }}', '{{ $candidate['prenom'] }} {{ $candidate['nom'] }}')" class="relative">
                    <!-- Image optimisée avec lazy loading et cache intelligent -->
                    <img
                        src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect width='100%25' height='100%25' fill='%23f3f4f6'/%3E%3C/svg%3E"
                        data-src="{{ $candidate['thumb_url'] ?? $candidate['photo_url'] }}"
                        alt="Photo de {{ $candidate['prenom'] }} {{ $candidate['nom'] }}"
                        class="w-full h-[300px] object-cover rounded-lg lazy-image"
                        loading="lazy"
                        data-fallback="{{ $candidate['photo_url'] }}"
                    >

                    <!-- Overlay au hover -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-lg"></div>

                    <!-- Badge de votes -->
                    <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-full text-xs font-semibold text-gray-800 flex items-center">
                        <svg class="w-3 h-3 mr-1 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $candidate['votes_count'] }}
                    </div>

                    <!-- Bouton de vote rapide -->
                    <div class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        @if(($settings->votes_enabled ?? true))
                            @auth
                                @if(!$this->hasVotedToday($candidate['id']))
                                    <button
                                        wire:click="vote({{ $candidate['id'] }})"
                                        @if($this->isLoading($candidate['id'])) disabled @endif
                                        class="bg-red-500 hover:bg-red-600 disabled:bg-red-300 text-white p-2 rounded-full transition-colors shadow-lg"
                                        title="Voter pour {{ $candidate['prenom'] }}"
                                    >
                                        @if($this->isLoading($candidate['id']))
                                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </button>
                                @else
                                    <div class="bg-green-500 text-white p-2 rounded-full shadow-lg" title="Déjà voté aujourd'hui">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            @else
                                <button
                                    wire:click="vote({{ $candidate['id'] }})"
                                    class="bg-orange-500 hover:bg-orange-600 text-white p-2 rounded-full transition-colors shadow-lg"
                                    title="Se connecter pour voter"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            @endauth
                        @else
                            <div class="bg-gray-300 text-gray-700 p-2 rounded-full shadow-lg cursor-not-allowed" title="Votes désactivés">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm2-9a2 2 0 11-4 0 2 2 0 014 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Nom au hover -->
                    <div class="absolute bottom-0 left-0 right-0 p-3 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                        <h4 class="text-sm font-semibold">
                            {{ $candidate['prenom'] }} {{ substr($candidate['nom'], 0, 1) }}.
                        </h4>
                    </div>
                </div>

                <!-- Lien vers la page du candidat -->
                <a href="{{ route('candidate.detail', $candidate['id']) }}" class="absolute inset-0 z-10" aria-label="Voir le profil de {{ $candidate['prenom'] }} {{ $candidate['nom'] }}"></a>
            </div>
        @empty
            <div class="col-span-full text-center py-20 animate-fade-in-up">
                <div class="max-w-md mx-auto">
                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <h3 class="text-3xl font-bold text-gray-900 mb-4">Aucun candidat pour le moment</h3>
                    <p class="text-gray-600 mb-8 text-lg">Soyez le premier à participer au concours photo DINOR !</p>

                    @auth
                        <button
                            onclick="openCandidateModal()"
                            class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-4 text-lg inline-block rounded-lg font-medium transition-colors"
                        >
                            📸 S'inscrire et poster une photo
                        </button>
                    @else
                        <button
                            onclick="openCandidateModal()"
                            class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-4 text-lg inline-block rounded-lg font-medium transition-colors"
                        >
                            📸 S'inscrire et poster une photo
                        </button>
                    @endauth
                </div>
            </div>
        @endforelse
    </div>

    <!-- Bouton "Charger plus" -->
    @if($this->hasMoreCandidates())
        <div class="text-center mt-8">
            <button
                wire:click="loadMore"
                class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-3 rounded-lg font-medium transition-colors inline-flex items-center"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Voir plus de candidats
            </button>
        </div>
    @endif

    <!-- Modal de connexion -->
    @if($showAuthModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click="closeAuthModal">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6" wire:click.stop>
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Connectez-vous pour voter</h3>
                    <p class="text-gray-600 mb-6">Soutenez vos candidats préférés en vous connectant ou en créant un compte.</p>

                    <div class="space-y-3">
                        <a href="{{ route('login') }}" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 px-4 rounded-lg font-medium transition-colors block">
                            Se connecter
                        </a>
                        <a href="{{ route('register') }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 py-3 px-4 rounded-lg font-medium transition-colors block">
                            Créer un compte
                        </a>
                    </div>

                    <button wire:click="closeAuthModal" class="mt-4 text-gray-500 hover:text-gray-700 text-sm">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Lightbox simple pour la photo -->
    <div id="photo-lightbox" class="fixed inset-0 bg-black/90 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="relative max-w-4xl w-full max-h-full">
            <!-- Close Button -->
            <button onclick="closePhotoLightbox()" class="absolute top-4 right-4 z-10 text-white hover:text-gray-300 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Photo -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-2xl">
                <img id="lightbox-photo" src="" alt="" class="w-full h-auto max-h-[80vh] object-contain">
                <div class="p-4 text-center">
                    <h3 id="lightbox-candidate-name" class="text-lg font-semibold text-gray-900"></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    function openPhotoLightbox(photoUrl, candidateName) {
        // Utiliser l'image originale pour le lightbox (sans _thumb)
        let originalPhotoUrl = photoUrl.replace('_thumb.jpg', '.jpg');

        // Update lightbox content
        const lightboxImg = document.getElementById('lightbox-photo');
        lightboxImg.src = originalPhotoUrl;
        lightboxImg.alt = candidateName;

        // Fallback vers l'URL fournie si l'originale n'existe pas
        lightboxImg.onerror = function() {
            this.src = photoUrl;
            this.onerror = null;
        };

        document.getElementById('lightbox-candidate-name').textContent = candidateName;

        // Show lightbox
        document.getElementById('photo-lightbox').classList.remove('hidden');
        document.getElementById('photo-lightbox').classList.add('flex');

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }

    function closePhotoLightbox() {
        document.getElementById('photo-lightbox').classList.add('hidden');
        document.getElementById('photo-lightbox').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    // Close lightbox when clicking outside
    document.addEventListener('click', function(event) {
        const lightbox = document.getElementById('photo-lightbox');
        if (event.target === lightbox) {
            closePhotoLightbox();
        }
    });

    // Close lightbox with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closePhotoLightbox();
        }
    });

    // Lazy loading intelligent pour les images
    const lazyImageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                const src = img.dataset.src;
                const fallback = img.dataset.fallback;
                
                if (src) {
                    // Charger l'image optimisée
                    const newImg = new Image();
                    newImg.onload = () => {
                        img.src = src;
                        img.classList.remove('lazy-image');
                    };
                    newImg.onerror = () => {
                        // Fallback vers l'image originale seulement en cas d'erreur
                        if (fallback && fallback !== src) {
                            img.src = fallback;
                        }
                        img.classList.remove('lazy-image');
                    };
                    newImg.src = src;
                }
                observer.unobserve(img);
            }
        });
    });

    // Observer toutes les images lazy
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.lazy-image').forEach(img => {
            lazyImageObserver.observe(img);
        });
    });

    // Observer les nouvelles images après les mises à jour Livewire
    document.addEventListener('livewire:navigated', function() {
        document.querySelectorAll('.lazy-image').forEach(img => {
            lazyImageObserver.observe(img);
        });
    });

    // Écouter l'événement Livewire pour afficher le modal
    document.addEventListener('livewire:init', function() {
        Livewire.on('show-auth-modal', function() {
            // Le modal sera affiché via la propriété reactive $showAuthModal
        });
    });
</script>
