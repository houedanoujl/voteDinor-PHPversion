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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @forelse($candidates as $candidate)
            <div class="group card-dinor bg-white overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:scale-105 animate-fade-in-up">
                <!-- Image avec overlay -->
                <div class="relative overflow-hidden">
                    <img
                        src="{{ $candidate['photo_url'] }}"
                        alt="Photo de {{ $candidate['prenom'] }} {{ $candidate['nom'] }}"
                        class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500"
                    >
                    <!-- Overlay au hover -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <!-- Badge de votes -->
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold text-dinor-brown">
                        ❤️ {{ $candidate['votes_count'] }}
                    </div>
                </div>

                <div class="p-6">
                    <h4 class="text-xl font-bold text-dinor-brown mb-3 group-hover:text-dinor-orange transition-colors">
                        {{ $candidate['prenom'] }} {{ substr($candidate['nom'], 0, 1) }}.
                    </h4>

                    <p class="text-dinor-gray-600 mb-4">
                        {{ $candidate['description'] ?? 'Candidat au concours photo DINOR' }}
                    </p>

                    <div class="space-y-3">
                        @guest
                            <!-- Utilisateur non connecté -->
                            <a href="{{ route('auth.redirect', 'google') }}"
                               onclick="if(typeof trackLogin !== 'undefined') trackLogin('google');"
                               class="block w-full bg-dinor-gray-100 text-dinor-brown py-3 px-4 rounded-xl text-center font-medium hover:bg-dinor-gray-200 transition-all duration-300 group-hover:bg-dinor-orange group-hover:text-white">
                                <span class="flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    Se connecter pour voter
                                </span>
                            </a>
                        @else
                            @if($this->hasVotedToday($candidate['id']))
                                <!-- Déjà voté aujourd'hui -->
                                <div class="w-full bg-green-50 text-green-700 py-3 px-4 rounded-xl text-center font-medium border border-green-200">
                                    <span class="flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Voté aujourd'hui
                                    </span>
                                </div>
                            @else
                                <!-- Bouton de vote -->
                                <button
                                    wire:click="vote({{ $candidate['id'] }})"
                                    onclick="if(typeof trackVote !== 'undefined') trackVote({{ $candidate['id'] }}, '{{ $candidate['prenom'] }} {{ substr($candidate['nom'], 0, 1) }}.');"
                                    @if($this->isLoading($candidate['id'])) disabled @endif
                                    class="w-full btn-dinor text-white py-3 px-4 rounded-xl font-medium disabled:opacity-50 disabled:cursor-not-allowed group-hover:scale-105 transition-all duration-300"
                                >
                                    @if($this->isLoading($candidate['id']))
                                        <span class="flex items-center justify-center">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Vote en cours...
                                        </span>
                                    @else
                                        <span class="flex items-center justify-center">
                                            ❤️ Voter
                                        </span>
                                    @endif
                                </button>
                            @endif

                            <!-- Partage social -->
                            <div class="flex justify-center space-x-4 mt-4">
                                <button
                                    onclick="shareCandidate('{{ $candidate['prenom'] }}', '{{ $candidate['id'] }}')"
                                    class="text-sm text-dinor-gray-500 hover:text-dinor-orange transition-colors flex items-center"
                                    title="Partager"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                    </svg>
                                    Partager
                                </button>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-20 animate-fade-in-up">
                <div class="max-w-md mx-auto">
                    <div class="text-8xl mb-6">📷</div>
                    <h3 class="text-3xl font-bold text-dinor-brown mb-4">Aucun candidat pour le moment</h3>
                    <p class="text-dinor-gray-600 mb-8 text-lg">Soyez le premier à participer au concours photo vintage DINOR !</p>

                    @auth
                        <livewire:candidate-registration-modal />
                    @else
                        <a
                            href="{{ route('auth.redirect', 'google') }}"
                            class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-4 text-lg inline-block rounded-lg font-medium transition-colors"
                        >
                            🎯 Se connecter pour participer
                        </a>
                    @endauth
                </div>
            </div>
        @endforelse
    </div>
</div>

<script>
    function shareCandidate(candidateName, candidateId) {
        if (navigator.share) {
            navigator.share({
                title: `Vote pour ${candidateName} - Concours Photo DINOR`,
                text: `Votez pour ${candidateName} au Concours Photo Rétro DINOR !`,
                url: window.location.href
            });
        } else {
            // Fallback - copier l'URL
            navigator.clipboard.writeText(window.location.href);

            // Notification moderne
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-50 animate-fade-in-up';
            notification.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Lien copié ! Partagez-le pour soutenir ${candidateName}
                </div>
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    }
</script>
