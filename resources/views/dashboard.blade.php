@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('description', 'Tableau de bord administrateur - Statistiques du concours photo DINOR')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-dinor-gray-50 to-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="mb-12 text-center">
            <div class="inline-flex items-center px-4 py-2 bg-dinor-orange/10 text-dinor-orange rounded-full text-sm font-medium mb-6">
                <span class="w-2 h-2 bg-dinor-orange rounded-full mr-2"></span>
                Dashboard Administrateur
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-dinor-brown mb-4">
                📊 Statistiques DINOR
            </h1>
            <p class="text-xl text-dinor-gray-600 max-w-2xl mx-auto">
                Tableau de bord du concours photo - Données en temps réel
            </p>
        </div>

        <!-- Stats principales modernes -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <div class="card-dinor group hover:bg-gradient-to-br hover:from-dinor-orange/5 hover:to-dinor-orange/10 transition-all duration-300">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl mr-6 group-hover:scale-110 transition-transform">
                        👥
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-dinor-brown mb-1">{{ $stats['total_candidates'] }}</h3>
                        <p class="text-dinor-gray-600 font-medium">Candidats total</p>
                        <div class="flex items-center mt-2 text-sm">
                            <span class="text-green-600 font-medium">{{ $stats['approved_candidates'] }} approuvés</span>
                            <span class="mx-2">•</span>
                            <span class="text-yellow-600 font-medium">{{ $stats['pending_candidates'] }} en attente</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-dinor group hover:bg-gradient-to-br hover:from-red-500/5 hover:to-red-600/10 transition-all duration-300">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center text-white text-2xl mr-6 group-hover:scale-110 transition-transform">
                        ❤️
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-dinor-brown mb-1">{{ $stats['total_votes'] }}</h3>
                        <p class="text-dinor-gray-600 font-medium">Votes total</p>
                        <div class="flex items-center mt-2 text-sm">
                            <span class="text-green-600 font-medium">{{ $stats['votes_today'] }} aujourd'hui</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-dinor group hover:bg-gradient-to-br hover:from-green-500/5 hover:to-green-600/10 transition-all duration-300">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white text-2xl mr-6 group-hover:scale-110 transition-transform">
                        🔐
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-dinor-brown mb-1">{{ $stats['total_users'] }}</h3>
                        <p class="text-dinor-gray-600 font-medium">Utilisateurs inscrits</p>
                        <div class="flex items-center mt-2 text-sm">
                            <span class="text-blue-600 font-medium">Via Google/Facebook OAuth</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top candidats et graphiques -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Top 5 Candidats moderne -->
            <div class="card-dinor">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-dinor-brown flex items-center">
                        <span class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center text-white text-sm mr-3">🏆</span>
                        Top 5 Candidats
                    </h2>
                </div>
                <div class="space-y-4">
                    @forelse($topCandidates as $candidate)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-dinor-gray-50 to-white rounded-xl border border-dinor-gray-200 hover:border-dinor-orange/30 transition-all duration-300">
                            <div class="flex items-center">
                                <div class="relative">
                                    <img
                                        src="{{ $candidate->getPhotoUrl() ?: '/images/placeholder-avatar.svg' }}"
                                        alt="{{ $candidate->prenom }}"
                                        class="w-12 h-12 rounded-xl mr-4 object-cover shadow-md"
                                    >
                                    @if($loop->index < 3)
                                        <div class="absolute -top-2 -right-2 w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold
                                            @if($loop->index == 0) bg-yellow-500
                                            @elseif($loop->index == 1) bg-gray-400
                                            @else bg-orange-500
                                            @endif">
                                            {{ $loop->index + 1 }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-semibold text-dinor-brown text-lg">
                                        {{ $candidate->prenom }} {{ substr($candidate->nom, 0, 1) }}.
                                    </h4>
                                    <p class="text-dinor-gray-600">{{ $candidate->votes_count }} votes</p>
                                </div>
                            </div>
                            <div class="text-2xl">
                                @if($loop->index == 0) 🥇
                                @elseif($loop->index == 1) 🥈
                                @elseif($loop->index == 2) 🥉
                                @else ⭐
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">📷</div>
                            <h3 class="text-xl font-semibold text-dinor-brown mb-2">Aucun candidat pour le moment</h3>
                            <p class="text-dinor-gray-600">Les candidats apparaîtront ici une fois qu'ils auront reçu des votes</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Graphique des votes moderne -->
            <div class="card-dinor">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-dinor-brown flex items-center">
                        <span class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center text-white text-sm mr-3">📈</span>
                        Évolution des Votes
                    </h2>
                    <span class="text-sm text-dinor-gray-500">7 derniers jours</span>
                </div>
                <div class="relative h-64">
                    <canvas id="votesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Google Analytics Dashboard moderne -->
        <div class="card-dinor mb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-dinor-brown flex items-center">
                    <span class="w-8 h-8 bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg flex items-center justify-center text-white text-sm mr-3">📊</span>
                    Google Analytics en temps réel
                </h2>
            </div>

            @if(config('services.google_analytics.tracking_id'))
                <div class="bg-gradient-to-r from-green-50 to-green-100 p-6 rounded-xl border border-green-200 mb-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center text-white mr-4">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-green-800 font-semibold">
                                Tracking ID: {{ config('services.google_analytics.tracking_id') }}
                            </p>
                            <p class="text-green-700 text-sm mt-1">
                                Les données Google Analytics sont collectées automatiquement pour tous les événements.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Événements trackés -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border border-green-200">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center text-white mr-3">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-green-800">Événements Votes</h4>
                        </div>
                        <p class="text-sm text-green-700">Chaque clic sur "Voter" est tracké avec l'ID du candidat</p>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white mr-3">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-blue-800">Inscriptions Candidats</h4>
                        </div>
                        <p class="text-sm text-blue-700">Les soumissions de formulaires sont trackées</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl border border-purple-200">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center text-white mr-3">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-purple-800">Connexions OAuth</h4>
                        </div>
                        <p class="text-sm text-purple-700">Google/Facebook logins sont suivis</p>
                    </div>
                </div>

                <!-- Instructions pour voir les données -->
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 p-6 rounded-xl border border-yellow-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-yellow-800 mb-2">📈 Accéder aux données complètes</h4>
                            <p class="text-yellow-700 mb-3">
                                Pour voir les métriques détaillées, accédez à votre dashboard Google Analytics :
                            </p>
                        </div>
                        <a
                            href="https://analytics.google.com"
                            target="_blank"
                            class="bg-yellow-600 text-white px-6 py-3 rounded-xl hover:bg-yellow-700 transition-colors font-medium flex items-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Ouvrir Google Analytics
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-gradient-to-r from-red-50 to-red-100 p-6 rounded-xl border border-red-200">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center text-white mr-4">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-red-800 mb-2">⚠️ Google Analytics non configuré</h4>
                            <p class="text-red-700 mb-3">
                                Ajoutez <code class="bg-red-200 px-2 py-1 rounded">GOOGLE_ANALYTICS_TRACKING_ID</code> dans votre fichier <code class="bg-red-200 px-2 py-1 rounded">.env</code> pour activer le suivi.
                            </p>
                            <div class="bg-red-200 p-3 rounded-lg text-sm font-mono text-red-800">
                                GOOGLE_ANALYTICS_TRACKING_ID=GA_MEASUREMENT_ID
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions rapides modernes -->
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('contest.home') }}" class="btn-dinor px-8 py-4 text-lg group">
                <span class="flex items-center">
                    🏠 Retour au concours
                    <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </span>
            </a>

            @auth
                @if(auth()->user()->email === 'jeanluc@bigfiveabidjan.com')
                    <a href="{{ url('/admin') }}" class="bg-gradient-to-r from-dinor-brown to-dinor-brown-dark text-white px-8 py-4 rounded-xl font-bold hover:from-dinor-red-vintage hover:to-dinor-brown transition-all duration-300 text-lg group">
                        <span class="flex items-center">
                            ⚙️ Panel Admin Filament
                            <svg class="ml-2 w-5 h-5 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </span>
                    </a>
                    <a href="{{ url('/admin/whatsapp-test') }}" class="bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-4 rounded-xl font-bold hover:from-green-700 hover:to-green-800 transition-all duration-300 text-lg group">
                        <span class="flex items-center">
                            📱 Test WhatsApp
                            <svg class="ml-2 w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </span>
                    </a>
                @endif
            @endauth
        </div>
    </div>
</div>

<!-- Chart.js pour les graphiques -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique des votes moderne
const votesCtx = document.getElementById('votesChart').getContext('2d');
const votesData = @json($votesChart);
const votesLabels = Object.keys(votesData);
const votesValues = Object.values(votesData);

new Chart(votesCtx, {
    type: 'line',
    data: {
        labels: votesLabels,
        datasets: [{
            label: 'Votes par jour',
            data: votesValues,
            borderColor: '#FF8C00',
            backgroundColor: 'rgba(255, 140, 0, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#FF8C00',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }]
    },
    options: {
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
                    color: 'rgba(0,0,0,0.1)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Tracker les vues du dashboard
if (typeof gtag !== 'undefined') {
    gtag('event', 'page_view', {
        event_category: 'dashboard',
        event_label: 'admin_dashboard'
    });
}
</script>
@endsection
