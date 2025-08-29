<x-filament-panels::page>
    @php
        $stats = $this->getStats();
        $votingStats = $this->getVotingStats();
        $candidateStats = $this->getCandidateStats();
        $userStats = $this->getUserStats();
    @endphp

    <div class="space-y-6">
        <!-- Statistiques de vote en temps réel -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <h3 class="text-lg font-medium text-gray-900">Statistiques du Concours</h3>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-semibold text-blue-600">{{ number_format($votingStats['total_votes']) }}</div>
                        <div class="text-sm text-gray-600">Total votes</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-semibold text-green-600">{{ number_format($votingStats['votes_today']) }}</div>
                        <div class="text-sm text-gray-600">Votes aujourd'hui</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-semibold text-purple-600">{{ number_format($votingStats['unique_voters']) }}</div>
                        <div class="text-sm text-gray-600">Votants uniques</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-semibold text-orange-600">{{ $votingStats['top_candidate'] }}</div>
                        <div class="text-lg font-semibold text-orange-600">{{ $votingStats['top_candidate_votes'] }} votes</div>
                        <div class="text-xs text-gray-600">Candidat en tête</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques des candidats et utilisateurs -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Candidats -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Candidats</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total candidats</span>
                            <span class="font-semibold">{{ $candidateStats['total_candidates'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">En attente</span>
                            <span class="font-semibold text-yellow-600">{{ $candidateStats['pending_candidates'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Approuvés</span>
                            <span class="font-semibold text-green-600">{{ $candidateStats['approved_candidates'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Rejetés</span>
                            <span class="font-semibold text-red-600">{{ $candidateStats['rejected_candidates'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Utilisateurs -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Utilisateurs</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total utilisateurs</span>
                            <span class="font-semibold">{{ $userStats['total_users'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Inscrits aujourd'hui</span>
                            <span class="font-semibold text-green-600">{{ $userStats['users_today'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Emails vérifiés</span>
                            <span class="font-semibold text-blue-600">{{ $userStats['verified_users'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Connexions sociales</span>
                            <span class="font-semibold text-purple-600">{{ $userStats['social_users'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques Google Analytics simulées -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Visiteurs</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_visitors']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pages vues</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['page_views']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Taux de rebond</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['bounce_rate'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Durée moyenne</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['avg_session_duration'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pages les plus visitées -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pages les plus visitées</h3>
                    <div class="space-y-3">
                        @foreach($stats['top_pages'] as $page)
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $page['page'] }}</div>
                                <div class="text-sm text-gray-500">{{ number_format($page['views']) }} vues</div>
                            </div>
                            <div class="text-sm font-medium text-green-600">{{ $page['percentage'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sources de trafic -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Sources de trafic</h3>
                    <div class="space-y-3">
                        @foreach($stats['traffic_sources'] as $source)
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $source['source'] }}</div>
                                <div class="text-sm text-gray-500">{{ number_format($source['visitors']) }} visiteurs</div>
                            </div>
                            <div class="text-sm font-medium text-blue-600">{{ $source['percentage'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Note sur Google Analytics -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Intégration Google Analytics
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Les statistiques Google Analytics ci-dessus sont simulées. Pour obtenir de vraies données, configurez votre ID de suivi dans les variables d'environnement et intégrez l'API Google Analytics.</p>
                        <p class="mt-1"><strong>ID actuel :</strong> {{ env('GOOGLE_ANALYTICS_TRACKING_ID', 'Non configuré') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>