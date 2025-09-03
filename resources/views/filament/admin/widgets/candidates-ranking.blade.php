<x-filament-widgets::widget class="fi-wi-stats-overview">
    <x-filament::section icon="heroicon-m-trophy" heading="Classement des Candidats"></x-filament::section>
        @php
            $candidates = $this->getCandidatesRanking();
            $stats = $this->getStats();
        @endphp

        <div class="space-y-6">

            <!-- Classement des candidats -->
            <div class="fi-section-content rounded-xl overflow-hidden border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-medium text-gray-900">Top 10 - Candidats les plus votés</h3>
                </div>

                @if($candidates->count() > 0)
                    <!-- Top 3 en grille 3 colonnes -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6">
                        @foreach($candidates->take(3) as $index => $candidate)
                            <div class="bg-white rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        @if($index === 0)
                                            <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center text-white font-bold">1</div>
                                        @elseif($index === 1)
                                            <div class="w-10 h-10 bg-gray-400 rounded-full flex items-center justify-center text-white font-bold">2</div>
                                        @else
                                            <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold">3</div>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0">
                                        @if($candidate->getPhotoUrl())
                                            <img src="{{ $candidate->getPhotoUrl() }}" alt="Photo de {{ $candidate->prenom }} {{ $candidate->nom }}" class="h-14 w-14 rounded-full object-cover ring-2 ring-white shadow"/>
                                        @else
                                            <div class="h-14 w-14 bg-gray-200 rounded-full flex items-center justify-center ring-2 ring-white shadow">
                                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20" style="color: #9CA3AF;">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $candidate->prenom }} {{ $candidate->nom }}</p>
                                        <x-filament::badge color="success">{{ number_format($candidate->votes_count) }} votes</x-filament::badge>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <x-filament::button tag="a" size="sm" icon="heroicon-m-eye" href="{{ route('filament.admin.resources.candidates.view', $candidate) }}">Voir</x-filament::button>
                                        <x-filament::button size="sm" color="success" icon="heroicon-m-paper-airplane"
                                                            x-data
                                                            x-on:click.prevent="
                                                                fetch('{{ route('admin.whatsapp.send') }}', {
                                                                    method: 'POST',
                                                                    headers: {
                                                                        'Content-Type': 'application/json',
                                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                                    },
                                                                    body: JSON.stringify({
                                                                        candidate_id: {{ $candidate->id }},
                                                                        message_type: 'notification'
                                                                    })
                                                                }).then(r => r.json()).then(data => {
                                                                    if (data.success) {
                                                                        $dispatch('notify', { status: 'success', message: 'Message test envoyé.' })
                                                                    } else {
                                                                        $dispatch('notify', { status: 'danger', message: 'Envoi échoué.' })
                                                                    }
                                                                }).catch(() => {
                                                                    $dispatch('notify', { status: 'danger', message: 'Erreur technique.' })
                                                                });
                                                            "
                                        >Tester</x-filament::button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Le reste en liste -->
                    <div class="divide-y divide-gray-100">
                        @foreach($candidates->skip(3) as $index => $candidate)
                            <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <!-- Position et médaille -->
                                        <div class="flex-shrink-0">
                                            @if($loop->first && $index === 0)
                                                <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                                    🥇
                                                </div>
                                            @elseif($loop->index === 1)
                                                <div class="w-12 h-12 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                                    🥈
                                                </div>
                                            @elseif($loop->index === 2)
                                                <div class="w-12 h-12 bg-gradient-to-r from-orange-400 to-orange-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                                    🥉
                                                </div>
                                            @else
                                                <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                                    {{ $loop->index + 4 }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Photo du candidat -->
                                        <div class="flex-shrink-0">
                                            @if($candidate->getPhotoUrl())
                                                <img src="{{ $candidate->getPhotoUrl() }}"
                                                     alt="Photo de {{ $candidate->prenom }} {{ $candidate->nom }}"
                                                     class="h-16 w-16 rounded-full object-cover ring-4 ring-white shadow-lg">
                                            @else
                                                <div class="h-16 w-16 bg-gray-200 rounded-full flex items-center justify-center ring-4 ring-white shadow-lg">
                                                    <svg width="32" height="32" fill="currentColor" viewBox="0 0 20 20" style="color: #9CA3AF;">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Informations du candidat -->
                                        <div class="flex-1 min-w-0">
                                            <p class="text-lg font-semibold text-gray-900 truncate">
                                                {{ $candidate->prenom }} {{ $candidate->nom }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                {{ $candidate->email }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                Inscrit le {{ $candidate->created_at->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Nombre de votes et actions -->
                                    <div class="flex items-center space-x-4">
                                        <!-- Nombre de votes -->
                                        <x-filament::badge color="success">{{ number_format($candidate->votes_count) }} votes</x-filament::badge>

                                        <!-- Bouton voir détails -->
                                        <x-filament::button tag="a" size="sm" icon="heroicon-m-eye" href="{{ route('filament.admin.resources.candidates.view', $candidate) }}">
                                            Voir
                                        </x-filament::button>
                                    </div>
                                </div>

                                <!-- Barre de progression (optionnel) -->
                                @if($candidates->first() && $candidates->first()->votes_count > 0)
                                    <div class="mt-3">
                                        <div class="bg-gray-200 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-green-400 to-green-500 h-2 rounded-full transition-all duration-500"
                                                 style="width: {{ ($candidate->votes_count / $candidates->first()->votes_count) * 100 }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun candidat approuvé</h3>
                        <p class="text-gray-500">Les candidats approuvés apparaîtront ici avec leur nombre de votes.</p>
                    </div>
                @endif
            </div>

            <!-- Lien vers la liste complète -->
            <div class="text-center">
                <a href="{{ route('filament.admin.resources.candidates.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Voir tous les candidats
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
