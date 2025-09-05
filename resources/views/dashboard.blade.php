@extends('layouts.app')

@section('title', 'Mon Dashboard')
@section('description', 'Votre tableau de bord personnel - Vos statistiques')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header personnel -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900">
                Bonjour {{ Auth::user()->name }} 👋
            </h1>
            <p class="text-gray-600 mt-2">Voici vos statistiques personnelles</p>
        </div>

        <!-- Statistiques personnelles simplifiées -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Nombre de votes reçus -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 text-center">
                <div class="p-3 bg-red-100 rounded-lg mx-auto w-16 h-16 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <p class="text-sm text-gray-600 mb-2">Votes reçus</p>
                <p class="text-3xl font-bold text-gray-900">{{ $personalStats['total_votes_received'] }}</p>
            </div>

            <!-- Position dans le classement -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 text-center">
                <div class="p-3 bg-yellow-100 rounded-lg mx-auto w-16 h-16 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <p class="text-sm text-gray-600 mb-2">Position</p>
                <p class="text-3xl font-bold text-gray-900">
                    @if(isset($personalStats['ranking_position']) && $personalStats['ranking_position'])
                        #{{ $personalStats['ranking_position'] }}
                    @else
                        -
                    @endif
                </p>
            </div>

            <!-- Photos approuvées -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 text-center">
                <div class="p-3 bg-green-100 rounded-lg mx-auto w-16 h-16 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <p class="text-sm text-gray-600 mb-2">Photos approuvées</p>
                <p class="text-3xl font-bold text-gray-900">{{ $personalStats['photos_approved'] }}</p>
            </div>
        </div>

        <!-- Mes candidatures avec liens de partage -->
        @if($candidatesWithRanking->count() > 0)
        <div class="bg-white rounded-lg border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Mes candidatures</h2>
            </div>
            <div class="p-6">
                @foreach($candidatesWithRanking as $candidate)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-4 last:mb-0">
                    <div class="flex items-center">
                        @if($candidate->getPhotoUrl())
                            <img src="{{ $candidate->getPhotoUrl() }}" alt="Photo" class="w-16 h-16 object-cover rounded-lg mr-4">
                        @else
                            <div class="w-16 h-16 bg-gray-200 rounded-lg mr-4 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-medium text-gray-900">{{ $candidate->full_name }}</h3>
                            <div class="flex items-center mt-1 space-x-4">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($candidate->status === 'approved') bg-green-100 text-green-800
                                    @elseif($candidate->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    @if($candidate->status === 'pending')
                                        En cours de validation par l'administrateur
                                    @elseif($candidate->status === 'approved')
                                        Approuvé
                                    @elseif($candidate->status === 'rejected')
                                        Rejeté
                                    @else
                                        {{ ucfirst($candidate->status) }}
                                    @endif
                                </span>
                                @if($candidate->status === 'approved')
                                    <span class="text-sm text-gray-500">Position: #{{ $candidate->ranking_position }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-gray-900 mb-2">{{ $candidate->votes_count }}</p>
                        <p class="text-sm text-gray-500 mb-3">votes</p>
                        @if($candidate->status === 'approved')
                            <a href="{{ route('candidate.detail', $candidate->id) }}"
                               class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                </svg>
                                Partager mon profil
                            </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Actions rapides -->
        @php($liveUrl = \App\Models\SiteSetting::first()?->live_url)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
                <div class="space-y-3">
                    <a href="{{ route('contest.home') }}" class="block w-full bg-blue-600 text-white text-center px-4 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        Voir tous les candidats
                    </a>
                    <a href="{{ route('contest.ranking') }}" class="block w-full bg-gray-100 text-gray-900 text-center px-4 py-3 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                        Voir le classement complet
                    </a>
                </div>
            </div>

            <!-- Ressource Live (copie du lien) -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full {{ $liveUrl ? 'bg-red-600 animate-pulse' : 'bg-gray-300' }}"></span>
                    Live Facebook
                </h3>
                @if($liveUrl)
                    <div class="space-y-3">
                        <div class="text-sm text-gray-600 break-all border rounded p-3 bg-gray-50" id="liveLink">{{ $liveUrl }}</div>
                        <div class="flex gap-3">
                            <button type="button" onclick="copyLiveLink()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-900 px-4 py-2 rounded-lg font-medium transition-colors">Copier le lien</button>
                            <a href="{{ $liveUrl }}" target="_blank" rel="noopener" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center">Ouvrir le live</a>
                        </div>
                        <p class="text-xs text-gray-500">Partagez ce lien à votre audience pour suivre la diffusion en direct.</p>
                    </div>
                @else
                    <p class="text-sm text-gray-600">Aucun live renseigné pour le moment.</p>
                    <p class="text-xs text-gray-500 mt-2">Lorsque l'administrateur ajoute un lien dans les Paramètres du site, il apparaîtra ici avec des boutons pour le copier et l'ouvrir.</p>
                @endif
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations</h3>
                <div class="space-y-2 text-sm text-gray-600">
                    <p>• 1 vote par candidat par jour</p>
                    <p>• Partagez votre profil pour plus de votes</p>
                    <p>• Le classement est mis à jour en temps réel</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyLiveLink() {
    const el = document.getElementById('liveLink');
    if (!el) return;
    const text = el.textContent.trim();
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showLiveToast('Lien copié dans le presse-papiers');
        }).catch(() => fallbackCopy(text));
    } else {
        fallbackCopy(text);
    }
}

function fallbackCopy(text) {
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try { document.execCommand('copy'); showLiveToast('Lien copié dans le presse-papiers'); } catch(e) {}
    document.body.removeChild(ta);
}

function showLiveToast(message) {
    const n = document.createElement('div');
    n.className = 'fixed bottom-6 right-6 z-50 bg-gray-900 text-white px-4 py-2 rounded-lg shadow-lg';
    n.textContent = message;
    document.body.appendChild(n);
    setTimeout(() => n.remove(), 2000);
}
</script>
@endpush
