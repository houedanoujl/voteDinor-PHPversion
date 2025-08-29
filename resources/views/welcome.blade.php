<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>DINOR - Concours Photo</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-gray-900 dark:to-gray-800 min-h-screen">
        <header class="w-full px-6 py-4 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm border-b border-orange-100 dark:border-gray-700">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <h1 class="text-2xl font-bold text-orange-600 dark:text-orange-400">DINOR</h1>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Concours Photo</span>
                </div>

                <nav class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors">
                            🗳️ Voter
                        </a>
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center px-4 py-2 border border-orange-200 hover:border-orange-300 text-orange-700 dark:text-orange-300 rounded-lg font-medium transition-colors">
                            Dashboard
                        </a>
                    @else
                        <button onclick="openVoterModal()"
                                class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors">
                            🗳️ Voter
                        </button>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center px-4 py-2 border border-orange-200 hover:border-orange-300 text-orange-700 dark:text-orange-300 rounded-lg font-medium transition-colors">
                            Connexion
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="flex-1 flex items-center justify-center px-6 py-12">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Hero Section -->
                <div class="mb-12">
                    <h1 class="text-5xl md:text-7xl font-bold text-gray-900 dark:text-white mb-6">
                        Concours Photo
                        <span class="block text-orange-600 dark:text-orange-400">DINOR 2024</span>
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
                        Participez au concours photo officiel DINOR ! Soumettez vos plus belles photos et votez pour vos favoris.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="grid md:grid-cols-2 gap-6 mb-12">
                    <!-- Voter Button -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-orange-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="text-4xl mb-4">🗳️</div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Devenir Votant</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">
                            Rejoignez la communauté et votez pour vos photos préférées. Simple et rapide !
                        </p>
                        <button onclick="openVoterModal()"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                            S'inscrire comme Votant
                        </button>
                    </div>

                    <!-- Candidate Button -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-orange-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="text-4xl mb-4">📸</div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Devenir Candidat</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">
                            Soumettez votre plus belle photo et tentez de remporter le concours !
                        </p>
                        <button onclick="openCandidateModal()"
                                class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                            Participer au Concours
                        </button>
                    </div>
                </div>

                <!-- Features -->
                <div class="grid md:grid-cols-3 gap-6 text-center">
                    <div class="p-6">
                        <div class="text-3xl mb-3">⚡</div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Inscription Rapide</h4>
                        <p class="text-gray-600 dark:text-gray-300 text-sm">Créez votre compte en quelques minutes seulement</p>
                    </div>
                    <div class="p-6">
                        <div class="text-3xl mb-3">🏆</div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Concours Équitable</h4>
                        <p class="text-gray-600 dark:text-gray-300 text-sm">Système de vote transparent et sécurisé</p>
                    </div>
                    <div class="p-6">
                        <div class="text-3xl mb-3">🎁</div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Beaux Prix</h4>
                        <p class="text-gray-600 dark:text-gray-300 text-sm">De nombreux lots à gagner pour les gagnants</p>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modals -->
        @livewire('voter-registration-modal')
        @livewire('candidate-registration-modal')

        @livewireScripts

        <script>
            function openVoterModal() {
                Livewire.dispatch('open-voter-modal');
            }

            function openCandidateModal() {
                Livewire.dispatch('open-candidate-modal');
            }
        </script>
    </body>
</html>
