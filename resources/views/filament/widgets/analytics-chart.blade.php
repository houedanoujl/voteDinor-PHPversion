<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <!-- Statistiques générales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Total Candidats</p>
                            <p class="text-2xl font-bold">{{ $contestStats['total_candidates'] }}</p>
                        </div>
                        <div class="text-3xl">👥</div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Candidats Approuvés</p>
                            <p class="text-2xl font-bold">{{ $contestStats['approved_candidates'] }}</p>
                        </div>
                        <div class="text-3xl">✅</div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Total Votes</p>
                            <p class="text-2xl font-bold">{{ $contestStats['total_votes'] }}</p>
                        </div>
                        <div class="text-3xl">🗳️</div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Votes Aujourd'hui</p>
                            <p class="text-2xl font-bold">{{ $contestStats['votes_today'] }}</p>
                        </div>
                        <div class="text-3xl">📊</div>
                    </div>
                </div>
            </div>

            <!-- Graphiques Google Analytics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Graphique Visiteurs -->
                <div class="bg-white rounded-lg border p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-blue-500 mr-2">📈</span>
                        Visiteurs (7 derniers jours)
                    </h3>
                    <div class="h-64">
                        <canvas id="visitorsChart"></canvas>
                    </div>
                </div>

                <!-- Graphique Pages Vues -->
                <div class="bg-white rounded-lg border p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-green-500 mr-2">👁️</span>
                        Pages Vues (7 derniers jours)
                    </h3>
                    <div class="h-64">
                        <canvas id="pageViewsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Graphique Votes -->
            <div class="bg-white rounded-lg border p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <span class="text-purple-500 mr-2">🗳️</span>
                    Votes (7 derniers jours)
                </h3>
                <div class="h-64">
                    <canvas id="votesChart"></canvas>
                </div>
            </div>

            <!-- Classement des candidats -->
            <div class="bg-white rounded-lg border p-6">
                <h3 class="text-lg font-semibold mb-6 flex items-center">
                    <span class="text-orange-500 mr-2">🏆</span>
                    Classement des Candidats
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-3 px-4 font-medium">Rang</th>
                                <th class="text-left py-3 px-4 font-medium">Candidat</th>
                                <th class="text-left py-3 px-4 font-medium">Photo</th>
                                <th class="text-right py-3 px-4 font-medium">Votes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCandidates as $candidate)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center">
                                            <span class="text-2xl mr-2">{{ $candidate['medal'] }}</span>
                                            <span class="font-semibold text-gray-700">#{{ $candidate['rank'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-medium">{{ $candidate['name'] }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($candidate['photo'])
                                            <img src="{{ $candidate['photo'] }}"
                                                 alt="{{ $candidate['name'] }}"
                                                 class="w-12 h-12 rounded-full object-cover">
                                        @else
                                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-500 text-sm">📷</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <span class="font-bold text-lg text-purple-600">{{ $candidate['votes'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-filament::section>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Données pour les graphiques
        const analyticsData = @json($analyticsData);

        // Configuration commune pour les graphiques
        const commonConfig = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        };

        // Graphique Visiteurs
        new Chart(document.getElementById('visitorsChart'), {
            type: 'line',
            data: {
                labels: analyticsData.visitors.map(item => item.date),
                datasets: [{
                    label: 'Visiteurs',
                    data: analyticsData.visitors.map(item => item.value),
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                ...commonConfig,
                plugins: {
                    ...commonConfig.plugins,
                    title: {
                        display: true,
                        text: 'Évolution des visiteurs'
                    }
                }
            }
        });

        // Graphique Pages Vues
        new Chart(document.getElementById('pageViewsChart'), {
            type: 'bar',
            data: {
                labels: analyticsData.pageViews.map(item => item.date),
                datasets: [{
                    label: 'Pages Vues',
                    data: analyticsData.pageViews.map(item => item.value),
                    backgroundColor: '#10B981',
                    borderColor: '#059669',
                    borderWidth: 1
                }]
            },
            options: {
                ...commonConfig,
                plugins: {
                    ...commonConfig.plugins,
                    title: {
                        display: true,
                        text: 'Pages vues par jour'
                    }
                }
            }
        });

        // Graphique Votes
        new Chart(document.getElementById('votesChart'), {
            type: 'doughnut',
            data: {
                labels: analyticsData.votes.map(item => item.date),
                datasets: [{
                    label: 'Votes',
                    data: analyticsData.votes.map(item => item.value),
                    backgroundColor: [
                        '#8B5CF6',
                        '#A855F7',
                        '#C084FC',
                        '#D8B4FE',
                        '#E9D5FF',
                        '#F3E8FF',
                        '#FAF5FF',
                        '#FEF3C7'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Répartition des votes par jour'
                    }
                }
            }
        });
    </script>
    @endpush
</x-filament-widgets::widget>
