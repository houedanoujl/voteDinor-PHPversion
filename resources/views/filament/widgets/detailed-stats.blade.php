<x-filament-widgets::widget>
    <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">{{ $this->getHeading() }}</h2>

    @php
        $stats = $this->getStats();
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Statistiques générales -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Statistiques Générales
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Candidats</span>
                    <span class="font-semibold text-gray-900">{{ $stats['general']['total_candidates'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Approuvés</span>
                    <span class="font-semibold text-green-600">{{ $stats['general']['approved_candidates'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">En attente</span>
                    <span class="font-semibold text-yellow-600">{{ $stats['general']['pending_candidates'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Rejetés</span>
                    <span class="font-semibold text-red-600">{{ $stats['general']['rejected_candidates'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Votes</span>
                    <span class="font-semibold text-blue-600">{{ $stats['general']['total_votes'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Utilisateurs</span>
                    <span class="font-semibold text-purple-600">{{ $stats['general']['total_users'] }}</span>
                </div>
            </div>
        </div>

        <!-- Activité par période -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                Activité Récente
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Votes aujourd'hui</span>
                    <span class="font-semibold text-green-600">{{ $stats['periods']['votes_today'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Votes cette semaine</span>
                    <span class="font-semibold text-blue-600">{{ $stats['periods']['votes_this_week'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Votes ce mois</span>
                    <span class="font-semibold text-purple-600">{{ $stats['periods']['votes_this_month'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Nouveaux utilisateurs</span>
                    <span class="font-semibold text-orange-600">{{ $stats['periods']['users_today'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Utilisateurs cette semaine</span>
                    <span class="font-semibold text-indigo-600">{{ $stats['periods']['users_this_week'] }}</span>
                </div>
            </div>
        </div>

        <!-- Moyennes et taux -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Analyses
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Moyenne votes/candidat</span>
                    <span class="font-semibold text-blue-600">{{ number_format($stats['averages']['avg_votes_per_candidate'], 1) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Votes/jour (7j)</span>
                    <span class="font-semibold text-green-600">{{ number_format($stats['averages']['avg_votes_per_day'], 1) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Taux participation</span>
                    <span class="font-semibold text-purple-600">{{ $stats['averages']['participation_rate'] }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Candidat le plus voté -->
    @if($stats['top_performers']['most_voted'])
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            </svg>
            Candidat le Plus Voté
        </h3>
        <div class="flex items-center space-x-4">
            <img src="{{ $stats['top_performers']['most_voted']->getPhotoUrl() }}"
                 alt="{{ $stats['top_performers']['most_voted']->prenom }}"
                 class="w-16 h-16 rounded-full object-cover">
            <div>
                <h4 class="text-xl font-bold text-gray-900">
                    {{ $stats['top_performers']['most_voted']->prenom }} {{ $stats['top_performers']['most_voted']->nom }}
                </h4>
                <p class="text-gray-600">{{ $stats['top_performers']['most_voted']->email }}</p>
                <div class="flex items-center mt-2">
                    <svg class="w-5 h-5 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-semibold text-red-600">{{ $stats['top_performers']['most_voted']->votes_count }} {{ Str::plural('vote', $stats['top_performers']['most_voted']->votes_count) }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Activité récente -->
    @if($stats['top_performers']['recent_activity']->count() > 0)
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Activité Récente
        </h3>
        <div class="space-y-3">
            @foreach($stats['top_performers']['recent_activity'] as $vote)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">
                            {{ $vote->user ? $vote->user->name : 'Visiteur' }}
                        </p>
                        <p class="text-sm text-gray-600">
                            a voté pour {{ $vote->candidate->prenom }} {{ $vote->candidate->nom }}
                        </p>
                    </div>
                </div>
                <span class="text-sm text-gray-500">
                    {{ $vote->created_at->locale('fr')->diffForHumans() }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</x-filament-widgets::widget>
