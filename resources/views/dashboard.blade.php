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

        <!-- Statistiques générales du concours -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg border border-blue-200 p-6 text-white">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-400 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-blue-100">Total candidats</p>
                        <p class="text-2xl font-bold">{{ $contestStats['total_candidates'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg border border-green-200 p-6 text-white">
                <div class="flex items-center">
                    <div class="p-3 bg-green-400 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-green-100">Candidats approuvés</p>
                        <p class="text-2xl font-bold">{{ $contestStats['approved_candidates'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg border border-purple-200 p-6 text-white">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-400 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-purple-100">Total votes</p>
                        <p class="text-2xl font-bold">{{ $contestStats['total_votes'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg border border-orange-200 p-6 text-white">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-400 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-orange-100">Votes aujourd'hui</p>
                        <p class="text-2xl font-bold">{{ $contestStats['votes_today'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Classement général et mes photos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Classement général -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">🏆 Classement général</h2>
                    <a href="{{ route('contest.ranking') }}" class="text-sm text-blue-600 hover:text-blue-800">Voir tout</a>
                </div>
                <div class="p-6">
                    @forelse($topCandidates as $candidate)
                        <div class="flex items-center justify-between p-4 {{ $loop->first ? 'bg-yellow-50 border border-yellow-200' : 'bg-gray-50' }} rounded-lg mb-3 last:mb-0">
                            <div class="flex items-center">
                                <!-- Position avec médaille pour le top 3 -->
                                <div class="w-8 h-8 rounded-full flex items-center justify-center mr-4 font-bold text-sm
                                    @if($loop->first) bg-yellow-400 text-yellow-900
                                    @elseif($loop->iteration === 2) bg-gray-300 text-gray-700
                                    @elseif($loop->iteration === 3) bg-orange-400 text-orange-900
                                    @else bg-gray-200 text-gray-600 @endif">
                                    @if($loop->first) 🥇
                                    @elseif($loop->iteration === 2) 🥈
                                    @elseif($loop->iteration === 3) 🥉
                                    @else {{ $candidate->position }}
                                    @endif
                                </div>

                                @if($candidate->getPhotoUrl())
                                    <img src="{{ $candidate->getPhotoUrl() }}" alt="Photo" class="w-12 h-12 object-cover rounded-lg mr-4">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg mr-4 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $candidate->full_name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $candidate->email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-gray-900">{{ $candidate->votes_count }}</p>
                                <p class="text-xs text-gray-500">votes</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <p class="text-gray-500">Aucun candidat approuvé pour le moment</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Mes photos -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">📸 Mes photos</h2>
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
