@extends('layouts.app')

@section('title', 'Créer un compte votant')
@section('description', 'Créez votre compte votant pour participer au concours photo DINOR')

@section('content')
<div class="min-h-screen bg-dinor-cream">
    <!-- Hero Section -->
    <section class="relative py-20 px-4">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 tracking-tight">
                    Créer un compte
                    <span class="block text-orange-600">Votant</span>
                </h1>
                <p class="text-xl text-gray-600 mb-8">
                    Votez pour vos candidats préférés et suivez le classement en temps réel !
                </p>
            </div>

            <!-- Formulaire d'inscription votant -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                @livewire('voter-registration-form')
            </div>

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
                        <p class="text-sm text-gray-600">
                            Vous voulez participer ?
                            <a href="{{ route('register') }}" class="font-medium text-orange-600 hover:text-orange-700">
                                Poster votre photo
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