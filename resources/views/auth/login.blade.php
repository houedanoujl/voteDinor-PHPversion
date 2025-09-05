@extends('layouts.app')

@section('title', 'Connexion')
@section('description', 'Connectez-vous pour participer au concours photo DINOR')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-dinor-cream py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Connexion</h1>
            <p class="text-gray-600 mb-8">
                Connectez-vous pour voter et participer au concours photo
            </p>
        </div>

        <!-- Connexion WhatsApp uniquement -->
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="identifier" class="block text-sm font-medium text-gray-700">
                        Téléphone WhatsApp
                    </label>
                    <input id="identifier" name="identifier" type="text" required
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-orange-500 focus:border-orange-500 focus:z-10 @error('identifier') border-red-500 @enderror"
                           placeholder="+225XXXXXXXXXX"
                           value="{{ old('identifier') }}">
                    @error('identifier')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Mot de passe
                    </label>
                    <input id="password" name="password" type="password" required
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-orange-500 focus:border-orange-500 focus:z-10 @error('password') border-red-500 @enderror"
                           placeholder="••••••••">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('password.whatsapp') }}" class="text-sm text-orange-600 hover:text-orange-700">Mot de passe oublié (WhatsApp)</a>
            </div>

            <button type="submit"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200">
                Se connecter
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-sm text-dinor-olive">
                En vous connectant, vous acceptez de participer au concours selon les règles établies.
            </p>

            <div class="mt-6">
                <p class="text-sm text-gray-700 mb-3">Pas encore de compte ?</p>
                <div class="space-y-3">
                    <a href="{{ route('register.voter') }}"
                       class="w-full py-2.5 px-4 rounded-lg border border-dinor-beige text-dinor-brown hover:bg-dinor-beige/40 transition block text-center">
                        Créer un compte votant
                    </a>
                    <a href="{{ route('register') }}"
                       class="w-full py-2.5 px-4 rounded-lg bg-dinor-orange text-white hover:bg-dinor-orange/90 transition block text-center">
                        Poster ma photo
                    </a>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-dinor-beige">
                <a href="{{ route('contest.home') }}" class="text-dinor-brown hover:text-dinor-orange font-medium">
                    ← Retour au concours
                </a>
            </div>
        </div>
    </div>
</div>


@endsection
