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
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <!-- reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}" async defer></script>
    
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
        }
        
        .bg-gradient-dinor {
            background: linear-gradient(135deg, var(--dinor-orange) 0%, var(--dinor-orange-light) 100%);
        }
        
        .btn-dinor {
            background: linear-gradient(45deg, var(--dinor-orange), var(--dinor-orange-light));
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255,140,0,0.3);
        }
        
        .btn-dinor:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255,140,0,0.4);
        }
        
        .card-dinor {
            background: linear-gradient(135deg, var(--dinor-cream), var(--dinor-beige));
            border: 2px solid var(--dinor-red-vintage);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(139,69,19,0.2);
        }
        
        .font-retro {
            font-family: 'Georgia', serif;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-dinor-brown shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                            <h1 class="text-2xl font-retro font-bold text-dinor-cream">DINOR</h1>
                        </a>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        @auth
                            <div class="relative">
                                <button onclick="toggleUserMenu()" class="flex items-center text-dinor-cream hover:text-dinor-beige">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="w-8 h-8 rounded-full mr-2">
                                    @endif
                                    <span>{{ auth()->user()->name }}</span>
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                    @if(auth()->user()->is_admin)
                                        <a href="/admin" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Administration</a>
                                    @endif
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Se déconnecter</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="flex space-x-2">
                                <a href="{{ route('auth.redirect', 'google') }}" class="bg-white text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 flex items-center">
                                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                                        <path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                    Google
                                </a>
                                <a href="{{ route('auth.redirect', 'facebook') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
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

        <!-- Footer -->
        <footer class="bg-dinor-brown text-dinor-cream py-8 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h3 class="text-xl font-retro font-bold mb-2">DINOR - Cuisine Vintage</h3>
                    <p class="text-dinor-beige mb-4">Redécouvrez les saveurs authentiques des années 60</p>
                    <div class="text-sm text-dinor-beige">
                        © {{ date('Y') }} Concours Photo DINOR - Flashback Gourmand
                    </div>
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts
    
    <script>
        function toggleUserMenu() {
            document.getElementById('userMenu').classList.toggle('hidden');
        }
        
        // Fermer le menu si on clique ailleurs
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('userMenu');
            const button = event.target.closest('button');
            if (!button || button.getAttribute('onclick') !== 'toggleUserMenu()') {
                userMenu.classList.add('hidden');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>