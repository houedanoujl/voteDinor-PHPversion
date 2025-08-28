@extends('layouts.app')

@section('title', 'Mon Dashboard')
@section('description', 'Votre tableau de bord personnel - Vos photos et statistiques')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header personnel -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Bonjour {{ Auth::user()->name }} 👋
            </h1>
            <p class="text-gray-600 mt-2">Voici un aperçu de votre participation au concours</p>
        </div>

        <!-- Statistiques personnelles -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Photos soumises</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $personalStats['photos_submitted'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Photos approuvées</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $personalStats['photos_approved'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Votes reçus</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $personalStats['total_votes_received'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">En attente</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $personalStats['photos_pending'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mes photos et classement -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Mes photos -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Mes photos</h2>
                </div>
                <div class="p-6">
                    @forelse($candidatesWithRanking as $candidate)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-4 last:mb-0">
                            <div class="flex items-center">
                                @if($candidate->getPhotoUrl())
                                    <img src="{{ $candidate->getPhotoUrl() }}" alt="Photo" class="w-16 h-16 object-cover rounded-lg mr-4">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg mr-4 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $candidate->full_name }}</h3>
                                    <div class="flex items-center mt-1">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($candidate->status === 'approved') bg-green-100 text-green-800
                                            @elseif($candidate->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($candidate->status) }}
                                        </span>
                                        @if($candidate->status === 'approved')
                                            <span class="ml-2 text-sm text-gray-500">Classement: #{{ $candidate->ranking_position }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-gray-900">{{ $candidate->votes_count }}</p>
                                <p class="text-sm text-gray-500">votes</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-500">Vous n'avez pas encore soumis de photo</p>
                            <p class="text-sm text-gray-400 mt-1">Retournez à l'accueil pour participer</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="space-y-6">
                <!-- Mes actions -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Actions rapides</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <a href="{{ route('contest.home') }}" class="block w-full bg-blue-600 text-white text-center px-4 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                            Voir tous les candidats
                        </a>
                        <a href="{{ route('contest.ranking') }}" class="block w-full bg-gray-100 text-gray-900 text-center px-4 py-3 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                            Voir le classement complet
                        </a>
                        @if($personalStats['photos_submitted'] > 0)
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <p class="text-sm text-green-600">
                                    Vous avez voté {{ $personalStats['votes_given_today'] }} fois aujourd'hui
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informations -->
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Règles du concours</h2>
                    </div>
                    <div class="p-6 space-y-3 text-sm text-gray-600">
                        <div class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            <p>1 vote par candidat par jour</p>
                        </div>
                        <div class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            <p>Vous pouvez voter pour tous les candidats</p>
                        </div>
                        <div class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            <p>Les photos sont modérées avant publication</p>
                        </div>
                        <div class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            <p>Le classement est mis à jour en temps réel</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection