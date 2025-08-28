@extends('layouts.app')

@section('title', 'Inscription')
@section('description', 'Inscrivez-vous pour participer au concours photo DINOR')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-dinor-cream py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h1 class="text-4xl font-retro font-bold text-dinor-brown mb-2">
                =� Inscription DINOR
            </h1>
            <p class="text-dinor-olive mb-8">
                Cr�ez votre compte pour participer au concours photo vintage
            </p>
        </div>

        <!-- Formulaire d'inscription -->
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-dinor-brown">
                        Nom complet
                    </label>
                    <input id="name" name="name" type="text" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-dinor-beige placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-dinor-orange focus:border-dinor-orange focus:z-10 @error('name') border-red-500 @enderror" 
                           placeholder="Votre nom complet"
                           value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-dinor-brown">
                        Adresse email
                    </label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-dinor-beige placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-dinor-orange focus:border-dinor-orange focus:z-10 @error('email') border-red-500 @enderror" 
                           placeholder="votre@email.com"
                           value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-dinor-brown">
                        Mot de passe
                    </label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-dinor-beige placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-dinor-orange focus:border-dinor-orange focus:z-10 @error('password') border-red-500 @enderror" 
                           placeholder="""""""""">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-dinor-brown">
                        Confirmer le mot de passe
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-dinor-beige placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-dinor-orange focus:border-dinor-orange focus:z-10" 
                           placeholder="""""""""">
                </div>
            </div>

            <button type="submit" 
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-dinor-brown hover:bg-dinor-olive focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-dinor-orange transition-all duration-200">
                Cr�er mon compte
            </button>
        </form>
        
        <!-- S�parateur -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-dinor-beige"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-dinor-cream text-dinor-olive">Ou continuez avec</span>
            </div>
        </div>

        <!-- Authentification sociale -->
        <div class="space-y-4">
            <a href="{{ route('auth.redirect', 'google') }}" 
               onclick="if(typeof trackLogin !== 'undefined') trackLogin('google');"
               class="group relative w-full flex justify-center items-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Continuer avec Google
            </a>

            <a href="{{ route('auth.redirect', 'facebook') }}" 
               onclick="if(typeof trackLogin !== 'undefined') trackLogin('facebook');"
               class="group relative w-full flex justify-center items-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Continuer avec Facebook
            </a>
        </div>

        <div class="text-center">
            <p class="text-sm text-dinor-olive">
                En vous inscrivant, vous acceptez de participer au concours selon les r�gles �tablies.
            </p>
            
            <div class="mt-4 pt-4 border-t border-dinor-beige space-y-2">
                <p class="text-sm" style="color: var(--dinor-brown)">
                    Déjà un compte ? 
                    <a href="{{ route('login') }}" class="font-medium" style="color: var(--dinor-orange)" onmouseover="this.style.color='var(--dinor-brown)'" onmouseout="this.style.color='var(--dinor-orange)'">
                        Connectez-vous
                    </a>
                </p>
                <a href="{{ route('contest.home') }}" class="font-medium" style="color: var(--dinor-brown)" onmouseover="this.style.color='var(--dinor-orange)'" onmouseout="this.style.color='var(--dinor-brown)'">
                    ← Retour au concours
                </a>
            </div>
        </div>
    </div>
</div>
@endsection