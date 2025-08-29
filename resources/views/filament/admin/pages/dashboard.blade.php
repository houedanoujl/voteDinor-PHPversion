<x-filament-panels::page>
    <div class="space-y-6">
        <!-- En-tête personnalisé -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">🏆 Concours DINOR</h1>
                    <p class="text-orange-100 mt-2">Tableau de bord en temps réel</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">{{ now()->format('d/m/Y') }}</div>
                    <div class="text-orange-100">{{ now()->format('H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Widgets d'en-tête -->
        @if ($this->hasHeaderWidgets())
            <x-filament-widgets::widgets
                :columns="$this->getHeaderWidgetsColumns()"
                :widgets="$this->getHeaderWidgets()"
            />
        @endif

        <!-- Contenu principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Graphiques Analytics -->
            <div class="lg:col-span-2">
                @if ($this->hasFooterWidgets())
                    <x-filament-widgets::widgets
                        :columns="$this->getFooterWidgetsColumns()"
                        :widgets="$this->getFooterWidgets()"
                    />
                @endif
            </div>

            <!-- Sidebar avec informations rapides -->
            <div class="space-y-6">
                <!-- Informations rapides -->
                <div class="bg-white rounded-lg border p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-blue-500 mr-2">⚡</span>
                        Informations Rapides
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                            <span class="text-sm font-medium">Dernière activité</span>
                            <span class="text-sm text-blue-600">{{ now()->diffForHumans() }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <span class="text-sm font-medium">Serveur</span>
                            <span class="text-sm text-green-600">● En ligne</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                            <span class="text-sm font-medium">Base de données</span>
                            <span class="text-sm text-purple-600">● Connectée</span>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white rounded-lg border p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-orange-500 mr-2">🚀</span>
                        Actions Rapides
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('filament.admin.resources.candidates.index') }}"
                           class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <span class="text-gray-600 mr-3">👥</span>
                            <span class="text-sm font-medium">Gérer les candidats</span>
                        </a>
                        <a href="{{ route('contest.home') }}"
                           class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <span class="text-gray-600 mr-3">🌐</span>
                            <span class="text-sm font-medium">Voir le site public</span>
                        </a>
                        <a href="{{ route('contest.rules') }}"
                           class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <span class="text-gray-600 mr-3">📋</span>
                            <span class="text-sm font-medium">Règles du concours</span>
                        </a>
                    </div>
                </div>

                <!-- Dernières notifications -->
                <div class="bg-white rounded-lg border p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-red-500 mr-2">🔔</span>
                        Dernières Notifications
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3 p-3 bg-red-50 rounded-lg">
                            <span class="text-red-500 text-sm">●</span>
                            <div class="flex-1">
                                <p class="text-sm font-medium">Nouveau candidat inscrit</p>
                                <p class="text-xs text-gray-600">Il y a 5 minutes</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg">
                            <span class="text-green-500 text-sm">●</span>
                            <div class="flex-1">
                                <p class="text-sm font-medium">Vote enregistré</p>
                                <p class="text-xs text-gray-600">Il y a 10 minutes</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg">
                            <span class="text-blue-500 text-sm">●</span>
                            <div class="flex-1">
                                <p class="text-sm font-medium">Candidat approuvé</p>
                                <p class="text-xs text-gray-600">Il y a 15 minutes</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
