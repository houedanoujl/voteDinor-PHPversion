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

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @forelse($candidates as $candidate)
            <div class="group relative overflow-hidden rounded-xl cursor-pointer transform hover:scale-105 transition-all duration-300">
                <a href="{{ route('candidate.detail', $candidate['id']) }}" class="block">
                    <div onclick="openLightbox({{ $candidate['id'] }}, '{{ $candidate['prenom'] }}', '{{ $candidate['nom'] }}', '{{ $candidate['photo_url'] }}', {{ $candidate['votes_count'] }})">
                <!-- Image -->
                <img
                    src="{{ $candidate['photo_url'] }}"
                    alt="Photo de {{ $candidate['prenom'] }} {{ $candidate['nom'] }}"
                    class="w-full h-48 object-cover"
                >
                <!-- Overlay au hover -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                <!-- Badge de votes -->
                <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-full text-xs font-semibold text-gray-800 flex items-center">
                    <svg class="w-3 h-3 mr-1 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $candidate['votes_count'] }}
                </div>

                                    <!-- Nom au hover -->
                    <div class="absolute bottom-0 left-0 right-0 p-3 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                        <h4 class="text-sm font-semibold">
                            {{ $candidate['prenom'] }} {{ substr($candidate['nom'], 0, 1) }}.
                        </h4>
                    </div>
                </div>
                </a>
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
                        <livewire:candidate-registration-modal />
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-4 text-lg inline-block rounded-lg font-medium transition-colors"
                        >
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Se connecter pour participer
                        </a>
                    @endauth
                </div>
            </div>
        @endforelse
    </div>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="fixed inset-0 bg-black/90 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="relative max-w-4xl w-full max-h-full">
            <!-- Close Button -->
            <button onclick="closeLightbox()" class="absolute top-4 right-4 z-10 text-white hover:text-gray-300 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Content -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-2xl">
                <div class="grid grid-cols-1 lg:grid-cols-2">
                    <!-- Image -->
                    <div class="relative">
                        <img id="lightbox-image" src="" alt="" class="w-full h-96 lg:h-full object-cover">
                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold text-gray-800 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                            </svg>
                            <span id="lightbox-votes">0</span> votes
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="p-8 flex flex-col justify-between">
                        <div>
                            <h2 id="lightbox-name" class="text-3xl font-bold text-gray-900 mb-4"></h2>
                            <p class="text-gray-600 mb-6">Candidat au concours photo DINOR</p>

                            <!-- Vote Section -->
                            <div id="vote-section" class="space-y-4">
                                @guest
                                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        <p class="text-gray-600 mb-3">Connectez-vous pour voter</p>
                                        <a href="{{ route('login') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                            Se connecter
                                        </a>
                                    </div>
                                @else
                                    <div id="vote-button-container">
                                        <!-- Vote button will be inserted here by JavaScript -->
                                    </div>
                                @endguest
                            </div>
                        </div>

                        <!-- Social Sharing -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                </svg>
                                Partager
                            </h3>
                            <div class="flex space-x-3">
                                <button onclick="shareOnFacebook()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium transition-colors flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    Facebook
                                </button>
                                <button onclick="shareOnWhatsApp()" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg font-medium transition-colors flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                    </svg>
                                    WhatsApp
                                </button>
                                <button onclick="shareOnTwitter()" class="flex-1 bg-blue-400 hover:bg-blue-500 text-white py-2 px-4 rounded-lg font-medium transition-colors flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                    Twitter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentCandidateId = null;
    let currentCandidateName = '';
    let currentCandidatePhoto = '';
    let currentCandidateVotes = 0;

    function openLightbox(id, prenom, nom, photoUrl, votes) {
        currentCandidateId = id;
        currentCandidateName = prenom + ' ' + nom;
        currentCandidatePhoto = photoUrl;
        currentCandidateVotes = votes;

        // Update lightbox content
        document.getElementById('lightbox-image').src = photoUrl;
        document.getElementById('lightbox-image').alt = currentCandidateName;
        document.getElementById('lightbox-name').textContent = currentCandidateName;
        document.getElementById('lightbox-votes').textContent = votes;

        // Show lightbox
        document.getElementById('lightbox').classList.remove('hidden');
        document.getElementById('lightbox').classList.add('flex');

        // Update vote button for authenticated users
        updateVoteButton();

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.add('hidden');
        document.getElementById('lightbox').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function updateVoteButton() {
        const container = document.getElementById('vote-button-container');
        if (!container) return;

        // Pour l'instant, on affiche toujours le bouton de vote
        // La vérification se fait côté serveur
        container.innerHTML = `
            <button onclick="voteFromLightbox()" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-4 px-6 rounded-xl font-medium transition-colors flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                </svg>
                Voter pour ${currentCandidateName}
            </button>
        `;
    }

    function voteFromLightbox() {
        if (!currentCandidateId) return;

        // Appeler l'API de vote
        fetch('/vote/' + currentCandidateId, {
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
                // Update vote count immediately for better UX
                currentCandidateVotes = data.votes_count;
                document.getElementById('lightbox-votes').textContent = currentCandidateVotes;

                // Show success message
                showNotification('Vote enregistré avec succès !', 'success');

                // Update the vote button to show "already voted"
                updateVoteButton();
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
        const text = encodeURIComponent(`Votez pour ${currentCandidateName} au Concours Photo DINOR !`);
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${text}`, '_blank');
    }

    function shareOnWhatsApp() {
        const text = encodeURIComponent(`Votez pour ${currentCandidateName} au Concours Photo DINOR ! ${window.location.href}`);
        window.open(`https://wa.me/?text=${text}`, '_blank');
    }

    function shareOnTwitter() {
        const text = encodeURIComponent(`Votez pour ${currentCandidateName} au Concours Photo DINOR !`);
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

    // Close lightbox when clicking outside
    document.addEventListener('click', function(event) {
        const lightbox = document.getElementById('lightbox');
        if (event.target === lightbox) {
            closeLightbox();
        }
    });

    // Close lightbox with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeLightbox();
        }
    });
</script>
