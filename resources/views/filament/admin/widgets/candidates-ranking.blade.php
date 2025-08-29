<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $candidates = $this->getCandidatesRanking();
            $stats = $this->getStats();
        @endphp

        <div class="space-y-6">
            <!-- En-tête avec statistiques -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    Classement des Candidats
                </h2>

                <!-- Statistiques rapides -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <div>
                                <p class="text-blue-100 text-sm">Total Votes</p>
                                <p class="text-2xl font-bold">{{ number_format($stats['totalVotes']) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <div>
                                <p class="text-green-100 text-sm">Candidats</p>
                                <p class="text-2xl font-bold">{{ $stats['totalCandidates'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg p-4 text-white">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-emerald-100 text-sm">Approuvés</p>
                                <p class="text-2xl font-bold">{{ $stats['approvedCandidates'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-4 text-white">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-yellow-100 text-sm">En Attente</p>
                                <p class="text-2xl font-bold">{{ $stats['pendingCandidates'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classement des candidats -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        Top 10 - Candidats les plus votés
                    </h3>
                </div>

                @if($candidates->count() > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($candidates as $index => $candidate)
                            <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <!-- Position et médaille -->
                                        <div class="flex-shrink-0">
                                            @if($index === 0)
                                                <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                                    🥇
                                                </div>
                                            @elseif($index === 1)
                                                <div class="w-12 h-12 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                                    🥈
                                                </div>
                                            @elseif($index === 2)
                                                <div class="w-12 h-12 bg-gradient-to-r from-orange-400 to-orange-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                                    🥉
                                                </div>
                                            @else
                                                <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                                    {{ $index + 1 }}
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
                                                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
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
                                        <div class="text-center">
                                            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-2 rounded-lg shadow-lg">
                                                <p class="text-2xl font-bold">{{ number_format($candidate->votes_count) }}</p>
                                                <p class="text-xs text-green-100">votes</p>
                                            </div>
                                        </div>

                                        <!-- Bouton voir détails -->
                                        <div>
                                            <a href="{{ route('filament.admin.resources.candidates.view', $candidate) }}"
                                               class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Voir
                                            </a>
                                        </div>
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
