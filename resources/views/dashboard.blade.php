@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('description', 'Tableau de bord administrateur - Statistiques du concours photo DINOR')

@section('content')
<div class="min-h-screen bg-dinor-cream py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-retro font-bold text-dinor-brown mb-2">
                📊 Dashboard Admin
            </h1>
            <p class="text-dinor-olive">
                Tableau de bord du concours photo DINOR - Données en temps réel
            </p>
        </div>

        <!-- Stats principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="card-dinor bg-white p-6">
                <div class="flex items-center">
                    <div class="text-3xl mr-4">👥</div>
                    <div>
                        <h3 class="text-2xl font-bold text-dinor-brown">{{ $stats['total_candidates'] }}</h3>
                        <p class="text-dinor-olive">Candidats total</p>
                        <small class="text-sm text-gray-600">
                            {{ $stats['approved_candidates'] }} approuvés, {{ $stats['pending_candidates'] }} en attente
                        </small>
                    </div>
                </div>
            </div>

            <div class="card-dinor bg-white p-6">
                <div class="flex items-center">
                    <div class="text-3xl mr-4">❤️</div>
                    <div>
                        <h3 class="text-2xl font-bold text-dinor-brown">{{ $stats['total_votes'] }}</h3>
                        <p class="text-dinor-olive">Votes total</p>
                        <small class="text-sm text-gray-600">
                            {{ $stats['votes_today'] }} aujourd'hui
                        </small>
                    </div>
                </div>
            </div>

            <div class="card-dinor bg-white p-6">
                <div class="flex items-center">
                    <div class="text-3xl mr-4">🔐</div>
                    <div>
                        <h3 class="text-2xl font-bold text-dinor-brown">{{ $stats['total_users'] }}</h3>
                        <p class="text-dinor-olive">Utilisateurs inscrits</p>
                        <small class="text-sm text-gray-600">
                            Via Google/Facebook OAuth
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top candidats et graphiques -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top 5 Candidats -->
            <div class="card-dinor bg-white p-6">
                <h2 class="text-xl font-bold text-dinor-brown mb-4 flex items-center">
                    🏆 Top 5 Candidats
                </h2>
                <div class="space-y-3">
                    @forelse($topCandidates as $candidate)
                        <div class="flex items-center justify-between p-3 bg-dinor-beige rounded-lg">
                            <div class="flex items-center">
                                <img 
                                    src="{{ $candidate->getPhotoUrl() ?: '/images/placeholder-avatar.svg' }}" 
                                    alt="{{ $candidate->prenom }}"
                                    class="w-12 h-12 rounded-full mr-3 object-cover"
                                >
                                <div>
                                    <h4 class="font-semibold text-dinor-brown">
                                        {{ $candidate->prenom }} {{ substr($candidate->nom, 0, 1) }}.
                                    </h4>
                                    <p class="text-sm text-dinor-olive">{{ $candidate->votes_count }} votes</p>
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
                        <p class="text-gray-500 text-center py-8">Aucun candidat pour le moment</p>
                    @endforelse
                </div>
            </div>

            <!-- Graphique des votes -->
            <div class="card-dinor bg-white p-6">
                <h2 class="text-xl font-bold text-dinor-brown mb-4 flex items-center">
                    📈 Votes (7 derniers jours)
                </h2>
                <canvas id="votesChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Google Analytics Dashboard -->
        <div class="mt-8">
            <div class="card-dinor bg-white p-6">
                <h2 class="text-xl font-bold text-dinor-brown mb-4 flex items-center">
                    📊 Google Analytics en temps réel
                </h2>
                
                @if(config('services.google_analytics.tracking_id'))
                    <div class="bg-dinor-beige p-4 rounded-lg mb-4">
                        <p class="text-dinor-brown">
                            <strong>Tracking ID:</strong> {{ config('services.google_analytics.tracking_id') }}
                        </p>
                        <p class="text-sm text-dinor-olive mt-1">
                            Les données Google Analytics sont collectées automatiquement pour tous les événements.
                        </p>
                    </div>

                    <!-- Événements trackés -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <h4 class="font-semibold text-green-800 mb-2">✅ Événements Votes</h4>
                            <p class="text-sm text-green-600">Chaque clic sur "Voter" est tracké avec l'ID du candidat</p>
                        </div>
                        
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <h4 class="font-semibold text-blue-800 mb-2">📝 Inscriptions Candidats</h4>
                            <p class="text-sm text-blue-600">Les soumissions de formulaires sont trackées</p>
                        </div>
                        
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <h4 class="font-semibold text-purple-800 mb-2">🔐 Connexions OAuth</h4>
                            <p class="text-sm text-purple-600">Google/Facebook logins sont suivis</p>
                        </div>
                    </div>

                    <!-- Instructions pour voir les données -->
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h4 class="font-semibold text-yellow-800 mb-2">📈 Accéder aux données complètes</h4>
                        <p class="text-sm text-yellow-700 mb-3">
                            Pour voir les métriques détaillées, accédez à votre dashboard Google Analytics :
                        </p>
                        <a 
                            href="https://analytics.google.com" 
                            target="_blank"
                            class="inline-block bg-yellow-800 text-white px-4 py-2 rounded hover:bg-yellow-700 transition-colors"
                        >
                            🔗 Ouvrir Google Analytics
                        </a>
                    </div>
                @else
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                        <h4 class="font-semibold text-red-800 mb-2">⚠️ Google Analytics non configuré</h4>
                        <p class="text-sm text-red-600 mb-3">
                            Ajoutez <code>GOOGLE_ANALYTICS_TRACKING_ID</code> dans votre fichier <code>.env</code> pour activer le suivi.
                        </p>
                        <div class="bg-red-100 p-3 rounded text-sm font-mono">
                            GOOGLE_ANALYTICS_TRACKING_ID=GA_MEASUREMENT_ID
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="mt-8 flex flex-wrap gap-4">
            <a href="{{ route('contest.home') }}" class="btn-dinor px-6 py-3">
                🏠 Retour au concours
            </a>
            
            @auth
                @if(auth()->user()->email === 'jeanluc@bigfiveabidjan.com')
                    <a href="{{ url('/admin') }}" class="bg-dinor-brown text-white px-6 py-3 rounded-lg font-bold hover:bg-dinor-red-vintage transition-colors">
                        ⚙️ Panel Admin Filament
                    </a>
                    <a href="{{ url('/admin/whatsapp-test') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-green-700 transition-colors">
                        📱 Test WhatsApp
                    </a>
                @endif
            @endauth
        </div>
    </div>
</div>

<!-- Chart.js pour les graphiques -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique des votes
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
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
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