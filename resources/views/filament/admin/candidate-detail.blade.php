<div class="space-y-6">
    <!-- En-tête avec photo et informations principales -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-start space-x-6">
            <!-- Photo du candidat -->
            <div class="flex-shrink-0">
                @if($record->getPhotoUrl())
                    <img src="{{ $record->getPhotoUrl() }}"
                         alt="Photo de {{ $record->full_name }}"
                         class="w-32 h-32 rounded-lg object-cover shadow-md">
                @else
                    <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Informations principales -->
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-4">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $record->full_name }}</h1>
                    <span class="px-3 py-1 text-sm font-medium rounded-full
                        @if($record->status === 'approved') bg-green-100 text-green-800
                        @elseif($record->status === 'rejected') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        @if($record->status === 'pending') En attente
                        @elseif($record->status === 'approved') Approuvé
                        @elseif($record->status === 'rejected') Rejeté
                        @endif
                    </span>
                </div>

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
            </div>

            <!-- Statistiques -->
            <div class="flex-shrink-0">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $record->votes_count }}</div>
                    <div class="text-sm text-gray-500">Votes reçus</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Description -->
    @if($record->description)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Description</h2>
        <p class="text-gray-700 whitespace-pre-wrap">{{ $record->description }}</p>
    </div>
    @endif

    <!-- Informations détaillées -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations détaillées</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500">ID du candidat</p>
                <p class="text-gray-900">{{ $record->id }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Date de création</p>
                <p class="text-gray-900">{{ $record->created_at->format('d/m/Y à H:i') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Dernière modification</p>
                <p class="text-gray-900">{{ $record->updated_at->format('d/m/Y à H:i') }}</p>
            </div>
            @if($record->user_id)
            <div>
                <p class="text-sm font-medium text-gray-500">Utilisateur associé</p>
                <p class="text-gray-900">ID: {{ $record->user_id }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Historique des votes récents -->
    @if($record->votes->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Votes récents</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($record->votes()->latest()->take(10)->get() as $vote)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $vote->vote_date }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $vote->ip_address }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($vote->user)
                                {{ $vote->user->name }}
                            @else
                                Visiteur
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($record->votes->count() > 10)
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-500">
                Affichage des 10 derniers votes sur {{ $record->votes->count() }} au total
            </p>
        </div>
        @endif
    </div>
    @endif
</div>
