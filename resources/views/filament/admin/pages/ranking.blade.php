<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header avec stats -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-600">{{ $candidates->count() }}</div>
                    <div class="text-sm text-gray-600">Candidats approuvés</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $candidates->sum('votes_count') }}</div>
                    <div class="text-sm text-gray-600">Total des votes</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $candidates->first()?->votes_count ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Meilleur score</div>
                </div>
            </div>
        </div>

        <!-- Podium -->
        @if($candidates->count() >= 3)
        <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-center mb-6 text-yellow-800">🏆 Podium</h2>
            <div class="grid grid-cols-3 gap-4">
                <!-- 2ème place -->
                @if(isset($candidates[1]))
                <div class="text-center">
                    <div class="text-4xl mb-2">🥈</div>
                    <div class="font-medium">{{ $candidates[1]->full_name }}</div>
                    <div class="text-lg font-bold text-gray-600">{{ $candidates[1]->votes_count }} votes</div>
                </div>
                @endif

                <!-- 1ère place -->
                @if(isset($candidates[0]))
                <div class="text-center">
                    <div class="text-5xl mb-2">👑</div>
                    <div class="font-bold text-lg">{{ $candidates[0]->full_name }}</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $candidates[0]->votes_count }} votes</div>
                </div>
                @endif

                <!-- 3ème place -->
                @if(isset($candidates[2]))
                <div class="text-center">
                    <div class="text-4xl mb-2">🥉</div>
                    <div class="font-medium">{{ $candidates[2]->full_name }}</div>
                    <div class="text-lg font-bold text-gray-600">{{ $candidates[2]->votes_count }} votes</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Classement complet -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold">Classement complet</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Candidat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Votes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($candidates as $index => $candidate)
                            <tr class="hover:bg-gray-50 {{ $index < 3 ? 'bg-yellow-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($index == 0)
                                            <span class="text-2xl">🥇</span>
                                        @elseif($index == 1)
                                            <span class="text-2xl">🥈</span>
                                        @elseif($index == 2)
                                            <span class="text-2xl">🥉</span>
                                        @else
                                            <span class="text-lg font-bold text-gray-600">#{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $candidate->full_name }}</div>
                                    @if($candidate->description)
                                        <div class="text-sm text-gray-500 truncate max-w-xs">{{ $candidate->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $candidate->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-2xl font-bold {{ $index < 3 ? 'text-yellow-600' : 'text-blue-600' }}">
                                        {{ $candidate->votes_count }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Approuvé
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Aucun candidat approuvé pour le moment
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Actions rapides</h3>
            <div class="flex flex-wrap gap-4">
                <a href="/admin/candidates" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Gérer les candidats
                </a>
                <a href="/admin/vote-resources" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Voir tous les votes
                </a>
                <a href="/admin/analytics" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Voir les analytics
                </a>
            </div>
        </div>
    </div>
</x-filament-panels::page>