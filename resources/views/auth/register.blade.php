@extends('layouts.app')

@section('title', 'Inscription au concours')
@section('description', 'Inscrivez-vous pour participer au concours photo DINOR')

@section('content')
<div class="min-h-screen">
    <!-- Hero Section simplifié -->
    <section class="relative bg-white py-20 px-4">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6 tracking-tight">
                    Rejoignez le concours
                    <span class="block text-orange-600">DINOR</span>
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                    Partagez votre passion culinaire et tentez de gagner de superbes prix !
                </p>
            </div>

            @guest
                <!-- Formulaire pour les invités -->
                @livewire('candidate-registration-form')
            @else
                <!-- Formulaire pour les utilisateurs connectés -->
                @if(auth()->user()->candidate)
                    <div class="text-center bg-green-50 border border-green-200 rounded-xl p-8">
                        <div class="mb-4">
                            <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Vous participez déjà ! 🎉</h3>
                        <p class="text-gray-600 mb-6">
                            Votre candidature a été soumise avec succès.
                            @if(auth()->user()->candidate->status === 'pending')
                                Elle est actuellement en cours de validation.
                            @elseif(auth()->user()->candidate->status === 'approved')
                                Elle a été approuvée et vous pouvez recevoir des votes !
                            @else
                                Elle a été rejetée. Contactez-nous pour plus d'informations.
                            @endif
                        </p>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors">
                            Voir mon tableau de bord
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                @else
                    <!-- L'utilisateur est connecté mais n'a pas encore participé -->
                    @livewire('candidate-registration-form')
                @endif
            @endguest

            <!-- Navigation -->
            <div class="text-center mt-12">
                <div class="space-y-4">
                    <p class="text-sm text-gray-600">
                        En vous inscrivant, vous acceptez de participer au concours selon les
                        <a href="{{ route('contest.rules') }}" class="font-medium text-orange-600 hover:text-orange-700">
                            règles établies
                        </a>.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            Déjà un compte ?
                            <a href="{{ route('login') }}" class="font-medium text-orange-600 hover:text-orange-700">
                                Connectez-vous
                            </a>
                        </p>
                        <a href="{{ route('contest.home') }}" class="font-medium text-gray-600 hover:text-orange-600">
                            ← Retour au concours
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
