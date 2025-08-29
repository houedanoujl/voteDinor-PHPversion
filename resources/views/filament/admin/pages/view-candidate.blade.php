<x-filament-panels::page>
    <div class="space-y-6">
        <!-- En-tête avec photo et informations principales -->
        <x-filament::section>
            <div class="flex items-start space-x-6">
                <!-- Photo du candidat -->
                <div class="flex-shrink-0">
                    @if($record->getPhotoUrl())
                        <img src="{{ $record->getPhotoUrl() }}"
                             alt="Photo de {{ $record->prenom }} {{ $record->nom }}"
                             class="w-32 h-32 rounded-lg object-cover shadow-md">
                    @else
                        <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-user class="w-16 h-16 text-gray-400" />
                        </div>
                    @endif
                </div>

                <!-- Informations principales -->
                <div class="flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="text-gray-900">{{ $record->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">WhatsApp</p>
                            <p class="text-gray-900">{{ $record->whatsapp }}</p>
                        </div>
                    </div>
                    
                    @if($record->description)
                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-500">Description</p>
                        <p class="text-gray-900">{{ $record->description }}</p>
                    </div>
                    @endif
                </div>

                <!-- Statistiques -->
                <div class="flex-shrink-0">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $totalVotes }}</div>
                        <div class="text-sm text-gray-500">Votes reçus</div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Statistiques des votes -->
        <x-filament::section>
            <x-slot name="heading">
                Statistiques des votes
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $votesToday }}</div>
                    <div class="text-sm text-green-800">Votes aujourd'hui</div>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $votesWeek }}</div>
                    <div class="text-sm text-yellow-800">Cette semaine</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $votesMonth }}</div>
                    <div class="text-sm text-blue-800">Ce mois</div>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $totalVotes }}</div>
                    <div class="text-sm text-purple-800">Total votes</div>
                </div>
            </div>
        </x-filament::section>

        <!-- Informations détaillées -->
        <x-filament::section>
            <x-slot name="heading">
                Informations détaillées
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-500">ID du candidat</p>
                    <p class="text-gray-900 font-mono">{{ $record->id }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Date de création</p>
                    <p class="text-gray-900">{{ $record->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Dernière modification</p>
                    <p class="text-gray-900">{{ $record->updated_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
            
            @if($record->user_id)
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Utilisateur associé</p>
                <p class="text-gray-900">ID: {{ $record->user_id }}</p>
            </div>
            @endif
            
            @if($record->getPhotoUrl())
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">URL de la photo</p>
                <p class="text-gray-900 break-all text-sm">{{ $record->getPhotoUrl() }}</p>
            </div>
            @endif
        </x-filament::section>

        <!-- Historique des votes récents -->
        @if($recentVotes->count() > 0)
        <x-filament::section>
            <x-slot name="heading">
                Votes récents ({{ $recentVotes->count() }} derniers)
            </x-slot>
            
            <div class="space-y-3">
                @foreach($recentVotes as $vote)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">
                                {{ $vote->user ? $vote->user->name : 'Visiteur anonyme' }}
                                @if($vote->user)
                                    <span class="text-gray-500 text-sm">({{ $vote->user->email }})</span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $vote->created_at->format('d/m/Y à H:i') }} 
                                ({{ $vote->created_at->diffForHumans() }})
                            </div>
                        </div>
                        <div class="text-xs text-gray-400 font-mono">
                            IP: {{ $vote->ip_address ?? 'N/A' }}
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($totalVotes > 10)
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500">
                    Affichage des {{ $recentVotes->count() }} derniers votes sur {{ $totalVotes }} au total
                </p>
            </div>
            @endif
        </x-filament::section>
        @else
        <x-filament::section>
            <x-slot name="heading">
                Historique des votes
            </x-slot>
            
            <div class="text-center py-8">
                <x-heroicon-o-chart-bar class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                <p class="text-gray-500">Aucun vote reçu pour le moment</p>
            </div>
        </x-filament::section>
        @endif

        <!-- Actions rapides pour candidats en attente -->
        @if($record->status === 'pending')
        <x-filament::section>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-yellow-400 flex-shrink-0" />
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Candidature en attente de validation</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Ce candidat attend votre approbation. Utilisez les boutons "Approuver" ou "Rejeter" en haut de la page.</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>