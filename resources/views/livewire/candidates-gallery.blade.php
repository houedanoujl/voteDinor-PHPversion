<div>
    <!-- Messages flash -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($candidates as $candidate)
            <div class="card-dinor bg-white overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <div class="aspect-w-16 aspect-h-12">
                    <img 
                        src="{{ $candidate['photo_url'] }}" 
                        alt="Photo de {{ $candidate['prenom'] }} {{ $candidate['nom'] }}" 
                        class="w-full h-48 object-cover"
                    >
                </div>
                
                <div class="p-4">
                    <h4 class="text-lg font-bold text-dinor-brown mb-2">
                        {{ $candidate['prenom'] }} {{ substr($candidate['nom'], 0, 1) }}.
                    </h4>
                    
                    <p class="text-dinor-red-vintage font-semibold mb-3">
                        ❤️ {{ $candidate['votes_count'] }} vote(s)
                    </p>

                    <div class="space-y-2">
                        @guest
                            <!-- Utilisateur non connecté -->
                            <a href="{{ route('auth.redirect', 'google') }}" 
                               onclick="if(typeof trackLogin !== 'undefined') trackLogin('google');"
                               class="block w-full bg-dinor-beige text-dinor-brown py-2 px-4 rounded-lg text-center font-medium hover:bg-dinor-cream transition-colors">
                                🔒 Se connecter pour voter
                            </a>
                        @else
                            @if($this->hasVotedToday($candidate['id']))
                                <!-- Déjà voté aujourd'hui -->
                                <div class="w-full bg-green-100 text-green-700 py-2 px-4 rounded-lg text-center font-medium">
                                    ✅ Voté aujourd'hui
                                </div>
                            @else
                                <!-- Bouton de vote -->
                                <button 
                                    wire:click="vote({{ $candidate['id'] }})"
                                    onclick="if(typeof trackVote !== 'undefined') trackVote({{ $candidate['id'] }}, '{{ $candidate['prenom'] }} {{ substr($candidate['nom'], 0, 1) }}.');"
                                    @if($this->isLoading($candidate['id'])) disabled @endif
                                    class="w-full btn-dinor text-white py-2 px-4 rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    @if($this->isLoading($candidate['id']))
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Vote...
                                    @else
                                        ❤️ Voter
                                    @endif
                                </button>
                            @endif
                            
                            <!-- Partage social (optionnel) -->
                            <div class="flex justify-center space-x-2 mt-2">
                                <button 
                                    onclick="shareCandidate('{{ $candidate['prenom'] }}', '{{ $candidate['id'] }}')"
                                    class="text-xs text-dinor-olive hover:text-dinor-brown transition-colors"
                                    title="Partager"
                                >
                                    📢 Partager
                                </button>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16">
                <div class="text-6xl mb-4">📷</div>
                <h3 class="text-2xl font-retro text-dinor-brown mb-2">Aucun candidat pour le moment</h3>
                <p class="text-dinor-brown">Soyez le premier à participer !</p>
                
                @auth
                    <button 
                        onclick="openParticipationModal()" 
                        class="btn-dinor mt-4 px-6 py-3"
                    >
                        🎯 Participer maintenant
                    </button>
                @else
                    <a 
                        href="{{ route('auth.redirect', 'google') }}" 
                        class="btn-dinor mt-4 px-6 py-3 inline-block"
                    >
                        🎯 Se connecter pour participer
                    </a>
                @endauth
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
            alert('Lien copié ! Partagez-le pour soutenir ' + candidateName);
        }
    }
</script>