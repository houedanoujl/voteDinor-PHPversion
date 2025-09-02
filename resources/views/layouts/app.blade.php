<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Concours Photo DINOR') }} - @yield('title', 'Cuisine Vintage des Années 60')</title>

    <!-- Meta pour SEO et réseaux sociaux -->
    <meta name="description" content="@yield('description', 'Participez au concours photo vintage DINOR - Cuisine des années 60. Votez pour vos photos préférées !')">

    @hasSection('og_meta')
        @yield('og_meta')
    @else
        <meta property="og:title" content="{{ config('app.name') }} - @yield('title', 'Concours Photo Rétro')">
        <meta property="og:description" content="@yield('description', 'Participez au concours photo vintage DINOR')">
        <meta property="og:image" content="{{ asset('images/dinor-logo.png') }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Concours Photo DINOR">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ config('app.name') }} - @yield('title', 'Concours Photo Rétro')">
        <meta name="twitter:description" content="@yield('description', 'Participez au concours photo vintage DINOR')">
        <meta name="twitter:image" content="{{ asset('images/dinor-logo.png') }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Alpine.js non nécessaire: Livewire v3 l'inclut déjà. Éviter les doublons. -->

    <!-- reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}" async defer></script>

    <!-- Google Analytics -->
    <x-google-analytics :title="$title ?? null" />

    <style>
        :root {
            /* Palette festive fournie */
            --dark-goldenrod: #9B7D25;
            --bole: #7B433D;
            --black: #000000;
            --red-cmyk: #E3231C;
            --lion: #AF9556;

            /* Déclinaisons utilitaires */
            --primary: var(--dark-goldenrod);
            --secondary: var(--bole);
            --accent: var(--red-cmyk);
            --muted: var(--lion);
            --bg-dark: #0a0a0a;
            --bg-light: #faf7f2;

            /* Grays */
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

        /* Arrière-plans festifs */
        .bg-gradient-dinor {
            background: linear-gradient(135deg, var(--primary) 0%, var(--muted) 35%, #fff 100%);
        }

        .bg-gradient-dinor-dark {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--black) 100%);
        }

        .btn-dinor {
            background: linear-gradient(145deg, var(--primary), var(--muted));
            color: white;
            border: 2px solid var(--muted);
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
            background: linear-gradient(145deg, var(--muted), var(--primary));
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
            background: linear-gradient(135deg, #fffef8, var(--bg-light));
            border: 2px solid var(--muted);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
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
            border-bottom: 2px solid var(--muted);
        }

        .nav-dark {
            background: rgba(10, 10, 10, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--dinor-gray-800);
        }

        /* Ruban festif haut de page (grill/guirlandes) */
        .festive-topbar {
            height: 6px;
            background-image:
                repeating-linear-gradient(45deg,
                    var(--accent) 0 12px,
                    var(--muted) 12px 24px,
                    var(--secondary) 24px 36px
                );
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

        /* Motif grille (grillade) utilisable en fond */
        .bg-grill {
            background-color: #111;
            background-image:
                linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px),
                linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        /* Texture bois pour l'ambiance barbecue */
        .bg-wood {
            background: linear-gradient(90deg, 
                var(--secondary) 0%, 
                #8B5A3C 15%, 
                var(--secondary) 30%, 
                #6B3A2D 45%, 
                var(--secondary) 60%, 
                #8B5A3C 75%, 
                var(--secondary) 100%);
            background-size: 120px 100%;
        }

        /* Effet fumée animée */
        .smoke {
            position: relative;
            overflow: hidden;
        }
        .smoke::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -10%;
            width: 120%;
            height: 200%;
            background: radial-gradient(ellipse at center,
                rgba(255,255,255,0.03) 0%,
                rgba(255,255,255,0.01) 40%,
                transparent 70%);
            animation: smokeFloat 8s ease-in-out infinite;
            pointer-events: none;
        }
        @keyframes smokeFloat {
            0%, 100% { transform: translateY(0) rotate(0deg) scale(1); opacity: 0.3; }
            25% { transform: translateY(-20px) rotate(1deg) scale(1.05); opacity: 0.5; }
            50% { transform: translateY(-10px) rotate(-1deg) scale(0.98); opacity: 0.4; }
            75% { transform: translateY(-30px) rotate(0.5deg) scale(1.02); opacity: 0.6; }
        }

        /* Variantes de boutons */
        .btn-dinor-secondary {
            background: linear-gradient(145deg, var(--secondary), var(--bole));
            border-color: var(--secondary);
        }
        .btn-dinor-secondary:hover {
            background: linear-gradient(145deg, var(--bole), var(--secondary));
        }

        .btn-dinor-accent {
            background: linear-gradient(145deg, var(--accent), #C41E3A);
            border-color: var(--accent);
        }
        .btn-dinor-accent:hover {
            background: linear-gradient(145deg, #C41E3A, var(--accent));
        }

        .btn-dinor-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        .btn-dinor-outline:hover {
            background: var(--primary);
            color: white;
        }

        /* Cards cohérentes */
        .card-dinor-clean {
            background: white;
            border: 2px solid var(--muted);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }
        .card-dinor-clean:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        /* Écran de chargement */
        .loading-overlay {
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse at top, var(--black), #1a1a1a 70%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 300ms ease, visibility 300ms ease;
        }
        .loading-overlay.hidden {
            opacity: 0;
            visibility: hidden;
        }
        .loader-ring {
            width: 64px;
            height: 64px;
            border: 4px solid rgba(255,255,255,0.1);
            border-top-color: var(--accent);
            border-right-color: var(--muted);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .loading-icon {
            font-size: 24px;
            margin-left: 12px;
            opacity: 0.8;
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
    <!-- Bandeau festif -->
    <div class="festive-topbar"></div>
    <!-- Écran de chargement -->
    <div id="loadingOverlay" class="loading-overlay">
        <div style="display:flex;align-items:center;">
            <div class="loader-ring"></div>
            <div class="loading-icon">DINOR</div>
        </div>
    </div>
    <div class="min-h-screen">
        <!-- Navigation moderne -->
        <nav class="nav-modern sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('contest.home') }}" class="flex-shrink-0 flex items-center group">
                            <img src="{{ asset('images/dinor-logo.png') }}" alt="DINOR" class="w-10 h-10">
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
                            <div class="flex space-x-3">
                                <a href="{{ route('login') }}"
                                   class="bg-white text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors border border-gray-200">
                                    Connexion
                                </a>
                                <!-- <a href="{{ route('register') }}"
                                   class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-700 transition-colors">
                                    Inscription
                                </a> -->
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
