@extends('layouts.app')

@section('title', 'Inscription')
@section('description', 'Inscrivez-vous pour participer au concours photo DINOR')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-dinor-cream py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h1 class="text-4xl font-retro font-bold text-dinor-brown mb-2">
                🎉 Inscription DINOR
            </h1>
            <p class="text-dinor-olive mb-8">
                Créez votre compte pour participer au concours photo vintage
            </p>
        </div>

        <!-- Formulaire d'inscription simplifié -->
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="prenom" class="block text-sm font-medium text-dinor-brown">
                        Prénom *
                    </label>
                    <input id="prenom" name="prenom" type="text" required
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-dinor-beige placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-dinor-orange focus:border-dinor-orange focus:z-10 @error('prenom') border-red-500 @enderror"
                           placeholder="Votre prénom"
                           value="{{ old('prenom') }}">
                    @error('prenom')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nom" class="block text-sm font-medium text-dinor-brown">
                        Nom *
                    </label>
                    <input id="nom" name="nom" type="text" required
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-dinor-beige placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-dinor-orange focus:border-dinor-orange focus:z-10 @error('nom') border-red-500 @enderror"
                           placeholder="Votre nom"
                           value="{{ old('nom') }}">
                    @error('nom')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-dinor-brown">
                        Numéro WhatsApp *
                    </label>
                    <input id="whatsapp" name="whatsapp" type="text" required
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-dinor-beige placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-dinor-orange focus:border-dinor-orange focus:z-10 @error('whatsapp') border-red-500 @enderror"
                           placeholder="+225 0123456789"
                           value="{{ old('whatsapp') }}">
                    <p class="mt-1 text-xs text-dinor-olive">
                        Format international recommandé : +225 0123456789
                    </p>
                    @error('whatsapp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-dinor-brown hover:bg-dinor-olive focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-dinor-orange transition-all duration-200">
                🎉 Créer mon compte
            </button>
        </form>

        <!-- Informations importantes -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-blue-900 mb-2">📱 Message WhatsApp automatique</h3>
            <p class="text-xs text-blue-800">
                Après votre inscription, vous recevrez automatiquement un message WhatsApp de confirmation avec les instructions pour participer au concours.
            </p>
        </div>

        <div class="text-center">
            <p class="text-sm text-dinor-olive">
                En vous inscrivant, vous acceptez de participer au concours selon les
                <a href="{{ route('contest.rules') }}" class="font-medium text-dinor-orange hover:text-dinor-brown">
                    règles établies
                </a>.
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
