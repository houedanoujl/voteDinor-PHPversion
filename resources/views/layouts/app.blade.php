<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Concours Photo DINOR') }} - @yield('title', 'Cuisine Vintage des Années 60')</title>

    <!-- Meta pour SEO et réseaux sociaux -->
    <meta name="description" content="@yield('description', 'Participez au concours photo vintage DINOR - Cuisine des années 60. Votez pour vos photos préférées !')">
    <meta property="og:title" content="{{ config('app.name') }} - @yield('title', 'Concours Photo Rétro')">
    <meta property="og:description" content="@yield('description', 'Participez au concours photo vintage DINOR')">
    <meta property="og:image" content="{{ asset('images/dinor-logo.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}" async defer></script>

    <!-- Google Analytics -->
    <x-google-analytics :title="$title ?? null" />

    <style>
        :root {
            --dinor-orange: #FF8C00;
            --dinor-orange-light: #FFB84D;
            --dinor-cream: #FFF8DC;
            --dinor-brown: #8B4513;
            --dinor-brown-dark: #2C1810;
            --dinor-beige: #F5DEB3;
            --dinor-red-vintage: #CD853F;
            --dinor-olive: #808000;

            /* Nouvelles couleurs inspirées de cursor.com */
            --dinor-gray-50: #fafafa;
            --dinor-gray-100: #f5f5f5;
            --dinor-gray-200: #e5e5e5;
            --dinor-gray-300: #d4d4d4;
            --dinor-gray-400: #a3a3a3;
            --dinor-gray-500: #737373;
            --dinor-gray-600: #525252;
            --dinor-gray-700: #404040;
            --dinor-gray-800: #262626;
            --dinor-gray-900: #171717;
            --dinor-gray-950: #0a0a0a;
        }

        /* Design moderne inspiré de cursor.com */
        .bg-gradient-dinor {
            background: linear-gradient(135deg, var(--dinor-orange) 0%, var(--dinor-orange-light) 50%, var(--dinor-cream) 100%);
        }

        .bg-gradient-dinor-dark {
            background: linear-gradient(135deg, var(--dinor-brown-dark) 0%, var(--dinor-brown) 100%);
        }

        .btn-dinor {
            background: linear-gradient(45deg, var(--dinor-orange), var(--dinor-orange-light));
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(255,140,0,0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-dinor::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-dinor:hover::before {
            left: 100%;
        }

        .btn-dinor:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255,140,0,0.4);
        }

        .btn-dinor:active {
            transform: translateY(0);
        }

        .card-dinor {
            background: white;
            border: 1px solid var(--dinor-gray-200);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }

        .card-dinor:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        .card-dinor-vintage {
            background: linear-gradient(135deg, var(--dinor-cream), var(--dinor-beige));
            border: 2px solid var(--dinor-red-vintage);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(139,69,19,0.2);
        }

        .font-retro {
            font-family: 'Georgia', serif;
        }

        .font-modern {
            font-family: 'Inter', sans-serif;
        }

        /* Navigation moderne */
        .nav-modern {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--dinor-gray-200);
        }

        .nav-dark {
            background: rgba(10, 10, 10, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--dinor-gray-800);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        .animate-slide-in-right {
            animation: slideInRight 0.6s ease-out;
        }

        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .glass-dark {
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Input moderne */
        .input-modern {
            background: white;
            border: 2px solid var(--dinor-gray-200);
            border-radius: 12px;
            padding: 12px 16px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        .input-modern:focus {
            outline: none;
            border-color: var(--dinor-orange);
            box-shadow: 0 0 0 3px rgba(255, 140, 0, 0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card-dinor {
                padding: 16px;
                border-radius: 12px;
            }
        }
    </style>
</head>

<body class="font-modern antialiased bg-white">
    <div class="min-h-screen">
        <!-- Navigation moderne -->
        <nav class="nav-modern sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('contest.home') }}" class="flex-shrink-0 flex items-center group">
                            <div class="bg-orange-600 p-2 rounded-lg mr-3 group-hover:scale-110 transition-transform">
                                <span class="text-white font-bold text-lg">D</span>
                            </div>
                            <h1 class="text-2xl font-bold text-gray-900 group-hover:text-orange-600 transition-colors">
                                DINOR
                            </h1>
                        </a>
                    </div>

                    <div class="flex items-center space-x-4">
                        @auth
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 text-gray-700 hover:text-orange-600 transition-colors">
                                    <div class="w-8 h-8 bg-orange-600 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    <span class="font-medium">{{ auth()->user()->name }}</span>
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-600 hover:text-gray-800 transition-colors">
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="flex space-x-3" x-data>
                                <button 
                                    @click="$dispatch('open-auth-modal', { mode: 'login' })"
                                    class="bg-white text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors border border-gray-200">
                                    Connexion
                                </button>
                                <button 
                                    @click="$dispatch('open-auth-modal', { mode: 'register' })"
                                    class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-700 transition-colors">
                                    Inscription
                                </button>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Contenu principal -->
        <main>
            @yield('content')
        </main>

        <!-- Auth Modal Component -->
        @if (!auth()->check())
            @livewire('auth-modal')
        @endif

        <!-- Footer moderne -->
        <footer class="bg-gradient-dinor-dark text-white py-12 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center md:text-left">
                        <h3 class="text-2xl font-bold mb-4">DINOR</h3>
                        <p class="text-dinor-beige">Redécouvrez les saveurs authentiques des années 60</p>
                    </div>
                    <div class="text-center">
                        <h4 class="text-lg font-semibold mb-4">Concours Photo</h4>
                        <p class="text-dinor-beige">Partagez vos créations culinaires vintage</p>
                    </div>
                    <div class="text-center md:text-right">
                        <h4 class="text-lg font-semibold mb-4">Contact</h4>
                        <p class="text-dinor-beige">© {{ date('Y') }} DINOR - Flashback Gourmand</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts


    @stack('scripts')
</body>
</html>
