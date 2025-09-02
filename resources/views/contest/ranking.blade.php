@extends('layouts.app')

@section('title', 'Classement - Concours Photo DINOR')
@section('description', 'Découvrez le classement en temps réel du concours photo   DINOR')

@section('content')
<div class="min-h-screen bg-dinor-cream py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" style="color: var(--secondary);">
                Classement DINOR
            </h1>
            <p class="text-xl text-dinor-olive mb-6">
                Découvrez les candidats les plus populaires du concours photo  
            </p>
            <a href="{{ route('contest.home') }}" class="btn-dinor inline-block">
                ← Retour au concours
            </a>
        </div>

        <!-- Podium -->
        @if($candidates->count() >= 3)
        <div class="mb-16">
            <h2 class="text-2xl font-bold text-dinor-brown text-center mb-8">Le Podium</h2>
            <div class="flex justify-center items-end space-x-4">
                <!-- 2ème place -->
                @if(isset($candidates[1]))
                <div class="bg-white rounded-xl shadow-lg p-6 text-center transform rotate-2">
                    <div class="text-4xl mb-2 font-bold" style="color: var(--muted);">2</div>
                    <div class="w-24 h-24 rounded-full mx-auto mb-4 overflow-hidden">
                        <img src="{{ $candidates[1]->getPhotoUrl() ?: '/images/placeholder-avatar.svg' }}" 
                             alt="{{ $candidates[1]->full_name }}" 
                             class="w-full h-full object-cover">
                    </div>
                    <h3 class="font-bold text-dinor-brown">{{ $candidates[1]->full_name }}</h3>
                    <p class="text-dinor-orange font-bold text-xl">{{ $candidates[1]->votes_count }} votes</p>
                </div>
                @endif

                <!-- 1ère place -->
                @if(isset($candidates[0]))
                <div class="bg-gradient-to-b from-yellow-100 to-yellow-200 rounded-xl shadow-xl p-8 text-center transform -rotate-1 scale-110">
                    <div class="text-5xl mb-4 font-bold" style="color: var(--accent);">1</div>
                    <div class="w-32 h-32 rounded-full mx-auto mb-4 overflow-hidden border-4 border-yellow-400">
                        <img src="{{ $candidates[0]->getPhotoUrl() ?: '/images/placeholder-avatar.svg' }}" 
                             alt="{{ $candidates[0]->full_name }}" 
                             class="w-full h-full object-cover">
                    </div>
                    <h3 class="font-bold text-dinor-brown text-xl">{{ $candidates[0]->full_name }}</h3>
                    <p class="text-dinor-orange font-bold text-2xl">{{ $candidates[0]->votes_count }} votes</p>
                </div>
                @endif

                <!-- 3ème place -->
                @if(isset($candidates[2]))
                <div class="bg-white rounded-xl shadow-lg p-6 text-center transform -rotate-2">
                    <div class="text-4xl mb-2 font-bold" style="color: var(--secondary);">3</div>
                    <div class="w-24 h-24 rounded-full mx-auto mb-4 overflow-hidden">
                        <img src="{{ $candidates[2]->getPhotoUrl() ?: '/images/placeholder-avatar.svg' }}" 
                             alt="{{ $candidates[2]->full_name }}" 
                             class="w-full h-full object-cover">
                    </div>
                    <h3 class="font-bold text-dinor-brown">{{ $candidates[2]->full_name }}</h3>
                    <p class="text-dinor-orange font-bold text-xl">{{ $candidates[2]->votes_count }} votes</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Classement complet -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-dinor-brown text-white px-6 py-4">
                <h2 class="text-xl font-bold">Classement complet</h2>
            </div>
            
            <div class="divide-y divide-gray-200">
                @forelse($candidates as $index => $candidate)
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center space-x-4">
                        <!-- Position -->
                        <div class="flex-shrink-0 w-8 text-center">
                            @if($index == 0)
                                <span class="text-2xl font-bold" style="color: var(--accent);">1</span>
                            @elseif($index == 1)
                                <span class="text-2xl font-bold" style="color: var(--muted);">2</span>
                            @elseif($index == 2)
                                <span class="text-2xl font-bold" style="color: var(--secondary);">3</span>
                            @else
                                <span class="text-lg font-bold" style="color: var(--primary);">{{ $index + 1 }}</span>
                            @endif
                        </div>
                        
                        <!-- Photo -->
                        <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0">
                            <img src="{{ $candidate->getPhotoUrl() ?: '/images/placeholder-avatar.svg' }}" 
                                 alt="{{ $candidate->full_name }}" 
                                 class="w-full h-full object-cover">
                        </div>
                        
                        <!-- Info -->
                        <div>
                            <h3 class="font-bold text-dinor-brown">{{ $candidate->full_name }}</h3>
                            @if($candidate->description)
                                <p class="text-sm text-gray-600 truncate max-w-xs">{{ $candidate->description }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Votes -->
                    <div class="text-right">
                        <p class="text-2xl font-bold text-dinor-orange">{{ $candidate->votes_count }}</p>
                        <p class="text-sm text-gray-500">votes</p>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    Aucun candidat approuvé pour le moment
                </div>
                @endforelse
            </div>
        </div>

        <!-- CTA -->
        <div class="text-center mt-12">
            <a href="{{ route('contest.home') }}#gallery" class="btn-dinor bg-dinor-olive hover:bg-dinor-brown inline-block">
                Voter maintenant
            </a>
        </div>
    </div>
</div>
@endsection