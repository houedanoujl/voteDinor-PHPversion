@extends('layouts.app')

@section('title', 'Photo envoyée avec succès !')
@section('description', 'Votre photo a été envoyée et est en cours d\'examen pour le concours DINOR')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg w-full space-y-8">
        
        <!-- Animation de succès -->
        <div class="text-center">
            <div class="mx-auto h-24 w-24 bg-green-100 rounded-full flex items-center justify-center mb-6 animate-pulse">
                <svg class="h-12 w-12 text-green-600 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Photo envoyée ! 🎉
            </h1>
            <p class="text-xl text-gray-600 mb-8">
                Merci {{ session('candidate_data.name', 'pour votre participation') }} !
            </p>
        </div>

        <!-- Carte principale avec photo et détails -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            @if(session('candidate_data.photo'))
            <!-- Aperçu de la photo -->
            <div class="relative h-64 bg-gray-100">
                <img src="{{ Storage::url(session('candidate_data.photo')) }}" 
                     alt="Votre photo soumise" 
                     class="w-full h-full object-cover">
                <div class="absolute top-4 right-4">
                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                        ✓ Reçue
                    </span>
                </div>
            </div>
            @endif
            
            <div class="p-8">
                <!-- Message de confirmation -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-2">
                                En cours d'examen
                            </h3>
                            <p class="text-yellow-700">
                                <strong>Votre photo sera publiée une fois examinée et approuvée.</strong><br>
                                Cette vérification nous permet de nous assurer que toutes les photos respectent les règles du concours.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Informations -->
                <div class="grid grid-cols-1 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <div>
                                <div class="font-medium text-gray-900">Notification WhatsApp</div>
                                <div class="text-sm text-gray-600">{{ session('candidate_data.whatsapp') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Étapes suivantes -->
                <div class="bg-blue-50 rounded-lg p-6 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-4 flex items-center">
                        <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Que se passe-t-il maintenant ?
                    </h3>
                    <div class="space-y-3 text-sm text-blue-800">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-200 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                <span class="text-xs font-bold text-blue-600">1</span>
                            </div>
                            <div>
                                <div class="font-medium">Examen de votre photo</div>
                                <div class="text-blue-700">Notre équipe vérifie que votre photo respecte les règles</div>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-200 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                <span class="text-xs font-bold text-blue-600">2</span>
                            </div>
                            <div>
                                <div class="font-medium">Notification WhatsApp</div>
                                <div class="text-blue-700">Vous recevrez un message dès que votre photo est approuvée</div>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-200 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                <span class="text-xs font-bold text-blue-600">3</span>
                            </div>
                            <div>
                                <div class="font-medium">Publication et votes</div>
                                <div class="text-blue-700">Votre photo sera visible et les votes peuvent commencer !</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('contest.home') }}" 
                       class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 px-4 rounded-lg transition-colors text-center">
                        <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Retour au concours
                    </a>
                    @if(auth()->check())
                    <a href="{{ route('dashboard') }}" 
                       class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-4 rounded-lg transition-colors text-center">
                        <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Mon tableau de bord
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Note en bas -->
        <div class="text-center text-sm text-gray-500">
            <p>Temps d'examen habituel : 2-24 heures</p>
            <p class="mt-1">
                Des questions ? 
                <a href="{{ route('contest.rules') }}" class="text-orange-600 hover:text-orange-700 font-medium">
                    Consultez les règles
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Popup de confirmation automatique -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Afficher un popup de confirmation après quelques secondes
    setTimeout(function() {
        if (typeof window !== 'undefined' && !sessionStorage.getItem('popup_shown')) {
            showConfirmationPopup();
            sessionStorage.setItem('popup_shown', 'true');
        }
    }, 1500);
});

function showConfirmationPopup() {
    const popup = document.createElement('div');
    popup.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4';
    popup.innerHTML = `
        <div class="bg-white rounded-2xl p-8 max-w-sm w-full text-center transform transition-all duration-300 scale-95">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Photo reçue ! 📸</h3>
            <p class="text-gray-600 mb-6">Votre photo sera examinée et publiée une fois approuvée.</p>
            <button onclick="closePopup(this)" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                Compris !
            </button>
        </div>
    `;
    
    document.body.appendChild(popup);
    
    // Animation d'entrée
    setTimeout(() => {
        popup.querySelector('div').classList.remove('scale-95');
        popup.querySelector('div').classList.add('scale-100');
    }, 10);
}

function closePopup(button) {
    const popup = button.closest('.fixed');
    popup.classList.add('opacity-0');
    setTimeout(() => {
        document.body.removeChild(popup);
    }, 300);
}
</script>
@endsection