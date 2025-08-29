<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <!-- Titre avec statut de configuration -->
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <span class="mr-3">📊</span>
                    Google Analytics - Statistiques du Site
                </h2>
                <div class="flex items-center">
                    @if($isConfigured)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Configuré
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            <span class="w-2 h-2 bg-orange-500 rounded-full mr-2"></span>
                            Configuration requise
                        </span>
                    @endif
                </div>
            </div>

            @if(!$isConfigured)
                <!-- Alerte de configuration -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-amber-800">Configuration Google Analytics requise</h3>
                            <div class="mt-2 text-sm text-amber-700">
                                <p>Pour afficher les vraies données Analytics, configurez les variables d'environnement :</p>
                                <ul class="mt-1 list-disc list-inside">
                                    <li><code class="bg-amber-100 px-1 rounded">GOOGLE_ANALYTICS_TRACKING_ID</code></li>
                                    <li><code class="bg-amber-100 px-1 rounded">GOOGLE_ANALYTICS_MEASUREMENT_ID</code></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Données en temps réel -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-6 text-white">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <span class="w-3 h-3 bg-green-400 rounded-full mr-3 animate-pulse"></span>
                    Activité en temps réel
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ $realTimeData['active_users'] }}</div>
                        <div class="text-sm opacity-90">Utilisateurs actifs</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ $realTimeData['active_sessions'] }}</div>
                        <div class="text-sm opacity-90">Sessions actives</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ $realTimeData['page_views_per_minute'] }}</div>
                        <div class="text-sm opacity-90">Vues/minute</div>
                    </div>
                </div>
            </div>

            <!-- Graphiques principaux -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Sessions -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-blue-500 mr-2">👥</span>
                        Sessions (30 derniers jours)
                    </h3>
                    <div class="h-64">
                        <canvas id="sessionsChart"></canvas>
                    </div>
                    <div class="mt-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ collect($analyticsData['sessions'])->sum('value') }}</div>
                        <div class="text-sm text-gray-600">Total sessions</div>
                    </div>
                </div>

                <!-- Utilisateurs -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-green-500 mr-2">🧑‍💻</span>
                        Utilisateurs (30 derniers jours)
                    </h3>
                    <div class="h-64">
                        <canvas id="usersChart"></canvas>
                    </div>
                    <div class="mt-4 text-center">
                        <div class="text-2xl font-bold text-green-600">{{ collect($analyticsData['users'])->sum('value') }}</div>
                        <div class="text-sm text-gray-600">Total utilisateurs</div>
                    </div>
                </div>
            </div>

            <!-- Pages les plus consultées -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold mb-6 flex items-center">
                    <span class="text-purple-500 mr-2">📄</span>
                    Pages les plus consultées
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-medium text-gray-700">Page</th>
                                <th class="text-right py-3 px-4 font-medium text-gray-700">Vues</th>
                                <th class="text-right py-3 px-4 font-medium text-gray-700">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topPages as $page)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $page['title'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $page['page'] }}</div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-right font-medium">
                                        {{ number_format($page['views']) }}
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $page['percentage'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Statistiques complémentaires -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Appareils -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-orange-500 mr-2">📱</span>
                        Appareils utilisés
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-blue-500 rounded mr-3"></div>
                                <span class="font-medium">Desktop</span>
                            </div>
                            <span class="text-lg font-bold">{{ $deviceStats['desktop'] }}%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-green-500 rounded mr-3"></div>
                                <span class="font-medium">Mobile</span>
                            </div>
                            <span class="text-lg font-bold">{{ $deviceStats['mobile'] }}%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-purple-500 rounded mr-3"></div>
                                <span class="font-medium">Tablet</span>
                            </div>
                            <span class="text-lg font-bold">{{ $deviceStats['tablet'] }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Sources de trafic -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-red-500 mr-2">🌍</span>
                        Sources de trafic
                    </h3>
                    <div class="space-y-4">
                        @foreach($trafficSources as $source)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 rounded mr-3" style="background-color: {{ $source['color'] }}"></div>
                                    <span class="font-medium">{{ $source['source'] }}</span>
                                </div>
                                <span class="text-lg font-bold">{{ $source['percentage'] }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Géolocalisation -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold mb-6 flex items-center">
                    <span class="text-indigo-500 mr-2">🗺️</span>
                    Répartition géographique
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-medium text-gray-700">Pays</th>
                                <th class="text-right py-3 px-4 font-medium text-gray-700">Utilisateurs</th>
                                <th class="text-right py-3 px-4 font-medium text-gray-700">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($geoData as $country)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium">{{ $country['country'] }}</td>
                                    <td class="py-3 px-4 text-right">{{ number_format($country['users']) }}</td>
                                    <td class="py-3 px-4 text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $country['percentage'] }}%
                                        </span>
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
        document.addEventListener('DOMContentLoaded', function() {
            const analyticsData = @json($analyticsData);

            // Configuration commune
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

            // Graphique Sessions
            if (analyticsData.sessions && analyticsData.sessions.length > 0) {
                new Chart(document.getElementById('sessionsChart'), {
                    type: 'line',
                    data: {
                        labels: analyticsData.sessions.map(item => item.formatted_date),
                        datasets: [{
                            label: 'Sessions',
                            data: analyticsData.sessions.map(item => item.value),
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#3B82F6'
                        }]
                    },
                    options: commonConfig
                });
            }

            // Graphique Utilisateurs
            if (analyticsData.users && analyticsData.users.length > 0) {
                new Chart(document.getElementById('usersChart'), {
                    type: 'bar',
                    data: {
                        labels: analyticsData.users.map(item => item.formatted_date),
                        datasets: [{
                            label: 'Utilisateurs',
                            data: analyticsData.users.map(item => item.value),
                            backgroundColor: '#10B981',
                            borderColor: '#059669',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: commonConfig
                });
            }
        });
    </script>
    @endpush
</x-filament-widgets::widget>