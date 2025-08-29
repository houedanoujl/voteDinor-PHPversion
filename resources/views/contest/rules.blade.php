@extends('layouts.app')

@section('title', 'Règles du Concours - DINOR')
@section('description', 'Règles officielles du concours photo DINOR')

@section('content')
<div class="min-h-screen bg-dinor-cream py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-retro font-bold text-dinor-brown mb-4">
                📋 Règles du Concours DINOR
            </h1>
            <p class="text-dinor-olive text-lg">
                Règles officielles pour participer au concours photo vintage
            </p>
        </div>

        <!-- Règles principales -->
        <div class="bg-white rounded-lg border border-gray-200 p-8 mb-8">
            <h2 class="text-2xl font-bold text-dinor-brown mb-6">🏆 Objectif du Concours</h2>
            <p class="text-gray-700 mb-6">
                Le concours photo DINOR vise à célébrer la créativité et l'art photographique dans un style vintage.
                Les participants peuvent soumettre leurs meilleures photos et voter pour leurs candidats préférés.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Participation -->
                <div>
                    <h3 class="text-xl font-semibold text-dinor-brown mb-4">📸 Participation</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">✓</span>
                            <span>Inscription gratuite et obligatoire</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">✓</span>
                            <span>Une photo par participant maximum</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">✓</span>
                            <span>Photos originales et personnelles uniquement</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">✓</span>
                            <span>Format accepté : JPG, PNG, WebP</span>
                        </li>
                    </ul>
                </div>

                <!-- Système de vote -->
                <div>
                    <h3 class="text-xl font-semibold text-dinor-brown mb-4">🗳️ Système de Vote</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">✓</span>
                            <span>1 vote par candidat par jour</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">✓</span>
                            <span>Vote possible pour tous les candidats</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">✓</span>
                            <span>Classement en temps réel</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">✓</span>
                            <span>Votes vérifiés et sécurisés</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Règles de contenu -->
        <div class="bg-white rounded-lg border border-gray-200 p-8 mb-8">
            <h2 class="text-2xl font-bold text-dinor-brown mb-6">📝 Règles de Contenu</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Contenu autorisé -->
                <div>
                    <h3 class="text-xl font-semibold text-green-600 mb-4">✅ Contenu Autorisé</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li>• Photos originales prises par le participant</li>
                        <li>• Style vintage et rétro</li>
                        <li>• Portraits, paysages, nature morte</li>
                        <li>• Photos artistiques et créatives</li>
                        <li>• Contenu approprié pour tous publics</li>
                    </ul>
                </div>

                <!-- Contenu interdit -->
                <div>
                    <h3 class="text-xl font-semibold text-red-600 mb-4">❌ Contenu Interdit</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li>• Photos volées ou copiées</li>
                        <li>• Contenu offensant ou inapproprié</li>
                        <li>• Photos de personnes sans consentement</li>
                        <li>• Contenu commercial ou publicitaire</li>
                        <li>• Images générées par IA</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- ⚠️ SECTION FRAUDE - TRÈS IMPORTANTE -->
        <div class="bg-red-50 border-2 border-red-200 rounded-lg p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h2 class="text-2xl font-bold text-red-800">🚨 RÈGLES ANTI-FRAUDE</h2>
                </div>
            </div>

            <div class="space-y-4 text-red-800">
                <div class="bg-red-100 border border-red-300 rounded-lg p-4">
                    <h3 class="text-lg font-semibold mb-2">⚠️ FRAUDE = DISQUALIFICATION IMMÉDIATE</h3>
                    <p class="text-sm">
                        <strong>Toute tentative de fraude entraînera la disqualification immédiate et l'annulation de tous les lots.</strong>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold mb-2">🔍 Détection de Fraude</h4>
                        <ul class="text-sm space-y-1">
                            <li>• Votes multiples depuis la même IP</li>
                            <li>• Création de comptes multiples</li>
                            <li>• Utilisation de bots ou scripts</li>
                            <li>• Photos volées ou copiées</li>
                            <li>• Manipulation du système de vote</li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-semibold mb-2">⚖️ Sanctions</h4>
                        <ul class="text-sm space-y-1">
                            <li>• Disqualification immédiate</li>
                            <li>• Annulation de tous les lots</li>
                            <li>• Bannissement permanent</li>
                            <li>• Signalement aux autorités si nécessaire</li>
                            <li>• Publication de la fraude</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white border border-red-300 rounded-lg p-4">
                    <p class="text-sm font-medium text-red-900">
                        <strong>ATTENTION :</strong> En participant à ce concours, vous acceptez que toute fraude détectée entraînera la perte immédiate de tous vos droits et lots, sans possibilité de recours.
                    </p>
                </div>
            </div>
        </div>

        <!-- Prix et récompenses -->
        <div class="bg-white rounded-lg border border-gray-200 p-8 mb-8">
            <h2 class="text-2xl font-bold text-dinor-brown mb-6">🏆 Prix et Récompenses</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-6 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="text-3xl mb-2">🥇</div>
                    <h3 class="text-lg font-semibold text-dinor-brown mb-2">1er Prix</h3>
                    <p class="text-sm text-gray-600">Lot principal + Certificat</p>
                </div>

                <div class="text-center p-6 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="text-3xl mb-2">🥈</div>
                    <h3 class="text-lg font-semibold text-dinor-brown mb-2">2ème Prix</h3>
                    <p class="text-sm text-gray-600">Lot secondaire + Certificat</p>
                </div>

                <div class="text-center p-6 bg-orange-50 rounded-lg border border-orange-200">
                    <div class="text-3xl mb-2">🥉</div>
                    <h3 class="text-lg font-semibold text-dinor-brown mb-2">3ème Prix</h3>
                    <p class="text-sm text-gray-600">Lot tertiaire + Certificat</p>
                </div>
            </div>

            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800">
                    <strong>Note :</strong> Les lots exacts seront annoncés avant la fin du concours.
                    Tous les participants recevront un certificat de participation.
                </p>
            </div>
        </div>

        <!-- Calendrier -->
        <div class="bg-white rounded-lg border border-gray-200 p-8 mb-8">
            <h2 class="text-2xl font-bold text-dinor-brown mb-6">📅 Calendrier du Concours</h2>

            <div class="space-y-4">
                <div class="flex items-center p-4 bg-green-50 rounded-lg">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white font-bold">1</div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-green-800">Phase d'inscription</h3>
                        <p class="text-sm text-green-600">Ouverture des inscriptions et soumissions de photos</p>
                    </div>
                </div>

                <div class="flex items-center p-4 bg-blue-50 rounded-lg">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">2</div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-blue-800">Phase de vote</h3>
                        <p class="text-sm text-blue-600">Vote public pour les candidats approuvés</p>
                    </div>
                </div>

                <div class="flex items-center p-4 bg-purple-50 rounded-lg">
                    <div class="flex-shrink-0 w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold">3</div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-purple-800">Annonce des résultats</h3>
                        <p class="text-sm text-purple-600">Proclamation des gagnants et remise des prix</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact et support -->
        <div class="bg-white rounded-lg border border-gray-200 p-8">
            <h2 class="text-2xl font-bold text-dinor-brown mb-6">📞 Contact et Support</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-dinor-brown mb-3">❓ Questions ?</h3>
                    <p class="text-gray-700 mb-3">
                        Si vous avez des questions sur les règles du concours, n'hésitez pas à nous contacter.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• WhatsApp : +225 0123456789</li>
                        <li>• Email : contact@dinor-concours.com</li>
                        <li>• Support en ligne disponible</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-dinor-brown mb-3">📋 Modifications</h3>
                    <p class="text-gray-700 mb-3">
                        L'organisation se réserve le droit de modifier ces règles à tout moment.
                    </p>
                    <p class="text-sm text-gray-600">
                        Les participants seront informés de toute modification par WhatsApp et sur le site.
                    </p>
                </div>
            </div>
        </div>

        <!-- Bouton retour -->
        <div class="text-center mt-8">
            <a href="{{ route('contest.home') }}"
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-dinor-brown hover:bg-dinor-olive focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-dinor-orange transition-all duration-200">
                ← Retour au concours
            </a>
        </div>
    </div>
</div>
@endsection
