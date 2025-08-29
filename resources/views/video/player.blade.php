@extends('layouts.app')

@section('title', 'Lecteur Vidéo - Streaming')
@section('description', 'Lecteur vidéo avec streaming multi-format')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                🎥 Lecteur Vidéo
            </h1>
            <p class="text-gray-600 mt-2">Streaming vidéo avec support multi-format</p>
        </div>

        <!-- Informations de la vidéo -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations de la vidéo</h2>
            <div id="video-info" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600" id="file-size">-</div>
                    <div class="text-sm text-gray-600">Taille</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600" id="mime-type">-</div>
                    <div class="text-sm text-gray-600">Format</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600" id="last-modified">-</div>
                    <div class="text-sm text-gray-600">Modifié le</div>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-lg">
                    <div class="text-2xl font-bold text-orange-600" id="stream-status">-</div>
                    <div class="text-sm text-gray-600">Statut</div>
                </div>
            </div>
        </div>

        <!-- Lecteur vidéo principal -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Lecteur Principal</h2>
            <div class="aspect-video bg-black rounded-lg overflow-hidden">
                <video
                    id="main-player"
                    class="w-full h-full"
                    controls
                    preload="metadata"
                    poster="{{ asset('images/video-poster.jpg') }}"
                    crossorigin="anonymous">
                    <source src="{{ route('video.stream', 'video.mp4') }}" type="video/mp4">
                    Votre navigateur ne supporte pas la lecture vidéo.
                </video>
            </div>
        </div>

        <!-- Options de streaming -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Streaming standard -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📺 Streaming Standard</h3>
                <p class="text-gray-600 mb-4">Lecture directe avec support des requêtes de plage pour un streaming fluide.</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <span class="text-sm font-medium">Format:</span>
                        <span class="text-sm text-gray-600">MP4</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <span class="text-sm font-medium">Support Range:</span>
                        <span class="text-sm text-green-600">✓ Oui</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <span class="text-sm font-medium">Cache:</span>
                        <span class="text-sm text-gray-600">1 heure</span>
                    </div>
                </div>
                <button onclick="loadStandardStream()" class="w-full mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Charger le streaming standard
                </button>
            </div>

            <!-- Streaming HLS -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🔄 Streaming HLS</h3>
                <p class="text-gray-600 mb-4">HTTP Live Streaming pour une meilleure adaptation à la bande passante.</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <span class="text-sm font-medium">Format:</span>
                        <span class="text-sm text-gray-600">HLS</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <span class="text-sm font-medium">Adaptation:</span>
                        <span class="text-sm text-green-600">✓ Oui</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <span class="text-sm font-medium">Cache:</span>
                        <span class="text-sm text-gray-600">Désactivé</span>
                    </div>
                </div>
                <button onclick="loadHLSStream()" class="w-full mt-4 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    Charger le streaming HLS
                </button>
            </div>
        </div>

        <!-- Statistiques de streaming -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">📊 Statistiques de streaming</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600" id="buffering-time">0s</div>
                    <div class="text-sm text-gray-600">Temps de buffering</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600" id="playback-quality">-</div>
                    <div class="text-sm text-gray-600">Qualité de lecture</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600" id="network-speed">-</div>
                    <div class="text-sm text-gray-600">Vitesse réseau</div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 mt-8">
            <h3 class="text-lg font-semibold text-blue-900 mb-4">📋 Instructions d'utilisation</h3>
            <div class="space-y-2 text-sm text-blue-800">
                <p>• <strong>Ajoutez votre fichier vidéo :</strong> Placez votre fichier `video.mp4` dans le dossier `public/videos/`</p>
                <p>• <strong>Streaming standard :</strong> Idéal pour les vidéos courtes et moyennes</p>
                <p>• <strong>Streaming HLS :</strong> Recommandé pour les vidéos longues et les connexions lentes</p>
                <p>• <strong>Support mobile :</strong> Les deux formats sont compatibles avec les appareils mobiles</p>
                <p>• <strong>Performance :</strong> Le streaming utilise des requêtes de plage pour optimiser la bande passante</p>
            </div>
        </div>
    </div>
</div>

<script>
// Charger les informations de la vidéo au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadVideoInfo();
    setupVideoPlayer();
});

// Charger les informations de la vidéo
function loadVideoInfo() {
    fetch('{{ route("video.info", "video.mp4") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('file-size').textContent = data.size_formatted;
            document.getElementById('mime-type').textContent = data.mime_type;
            document.getElementById('last-modified').textContent = new Date(data.last_modified).toLocaleDateString();
            document.getElementById('stream-status').textContent = 'Disponible';
        })
        .catch(error => {
            console.error('Erreur lors du chargement des informations:', error);
            document.getElementById('stream-status').textContent = 'Erreur';
        });
}

// Configuration du lecteur vidéo
function setupVideoPlayer() {
    const video = document.getElementById('main-player');
    let startTime = Date.now();

    video.addEventListener('loadstart', function() {
        startTime = Date.now();
    });

    video.addEventListener('canplay', function() {
        const bufferingTime = ((Date.now() - startTime) / 1000).toFixed(1);
        document.getElementById('buffering-time').textContent = bufferingTime + 's';
    });

    video.addEventListener('progress', function() {
        if (video.buffered.length > 0) {
            const bufferedEnd = video.buffered.end(video.buffered.length - 1);
            const duration = video.duration;
            const quality = ((bufferedEnd / duration) * 100).toFixed(0);
            document.getElementById('playback-quality').textContent = quality + '%';
        }
    });

    // Simuler la vitesse réseau
    setInterval(() => {
        const speeds = ['Lente', 'Normale', 'Rapide'];
        const randomSpeed = speeds[Math.floor(Math.random() * speeds.length)];
        document.getElementById('network-speed').textContent = randomSpeed;
    }, 5000);
}

// Charger le streaming standard
function loadStandardStream() {
    const video = document.getElementById('main-player');
    video.src = '{{ route("video.stream", "video.mp4") }}';
    video.load();
    video.play();
}

// Charger le streaming HLS
function loadHLSStream() {
    const video = document.getElementById('main-player');
    video.src = '{{ route("video.hls", "video.mp4") }}';
    video.load();
    video.play();
}
</script>
@endsection
