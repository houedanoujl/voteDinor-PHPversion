@auth
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="flex items-center space-x-2 text-dinor-brown hover:text-dinor-orange transition-colors">
        <div class="w-8 h-8 bg-dinor-orange rounded-full flex items-center justify-center text-white font-bold">
            {{ substr(auth()->user()->name, 0, 1) }}
        </div>
        <span class="font-medium">{{ auth()->user()->name }}</span>
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
        </svg>
    </button>

    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-dinor-brown hover:bg-dinor-beige transition-colors">
            📊 Dashboard
        </a>
        
        @if(auth()->user()->email === 'jeanluc@bigfiveabidjan.com')
            <a href="{{ url('/admin') }}" class="block px-4 py-2 text-dinor-brown hover:bg-dinor-beige transition-colors">
                ⚙️ Admin Panel
            </a>
        @endif
        
        <hr class="my-1">
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 transition-colors">
                🚪 Déconnexion
            </button>
        </form>
    </div>
</div>
@else
<div class="flex space-x-4">
    <a href="{{ route('login') }}" class="bg-gray-900 text-white px-4 py-2 rounded hover:bg-gray-800 transition-colors">
        Connexion
    </a>
    <a href="{{ route('register') }}" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 transition-colors">
        Inscription
    </a>
</div>
@endauth