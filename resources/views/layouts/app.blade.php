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
                            <div class="flex space-x-3">
                                <a href="{{ route('auth.redirect', 'google') }}" class="bg-white text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors border border-gray-200 flex items-center">
                                    <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24">
                                        <path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                    Google
                                </a>
                                <a href="{{ route('auth.redirect', 'facebook') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    Facebook
                                </a>
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
