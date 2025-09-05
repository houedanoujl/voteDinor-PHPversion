<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Informations personnelles -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Prénom</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $record->prenom }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nom</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $record->nom }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $record->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">WhatsApp</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $record->whatsapp }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Photo soumise -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Photo soumise</h3>
                @if($record->getPhotoUrl())
                    <div class="text-center">
                        <img src="{{ $record->getPhotoUrl() }}" 
                             alt="Photo de {{ $record->full_name }}" 
                             class="max-h-96 mx-auto rounded-lg shadow-lg">
                        <p class="mt-2 text-sm text-gray-500">Photo soumise par {{ $record->full_name }}</p>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Aucune photo disponible</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistiques et statut -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nombre de votes</label>
                        <p class="mt-1 text-2xl font-bold text-blue-600">{{ $record->votes_count }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Statut</label>
                        <p class="mt-1">
                            @if($record->status === 'pending')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    En attente
                                </span>
                            @elseif($record->status === 'approved')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Approuvé
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Rejeté
                                </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Candidature soumise</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $record->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($record->description)
        <!-- Description -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
                <p class="text-gray-700">{{ $record->description }}</p>
            </div>
        </div>
        @endif

        <!-- Historique des votes récents -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Votes récents</h3>
                @if($record->votes->count() > 0)
                    <div class="space-y-2">
                        @foreach($record->votes()->latest()->take(5)->get() as $vote)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <div>
                                    <span class="text-sm text-gray-900">
                                        {{ $vote->user ? $vote->user->name : 'Utilisateur anonyme' }}
                                    </span>
                                    <span class="text-xs text-gray-500 ml-2">
                                        IP: {{ substr($vote->ip_address, 0, -2) }}**
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{ $vote->created_at->locale('fr')->diffForHumans() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Aucun vote pour ce candidat</p>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>