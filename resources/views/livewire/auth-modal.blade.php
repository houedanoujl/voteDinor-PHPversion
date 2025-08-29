<div x-data="{ showModal: @entangle('showModal') }" 
     @open-auth-modal.window="showModal = true; $wire.set('mode', $event.detail.mode)">
    <!-- Modal Backdrop -->
    <div class="fixed inset-0 z-50 overflow-y-auto" 
         x-show="showModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 wire:click="closeModal()"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">
                        {{ $mode === 'login' ? 'Connexion' : 'Inscription' }}
                    </h3>
                    <button type="button" 
                            wire:click="closeModal()" 
                            class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Mode Toggle -->
                <div class="flex mb-6 bg-gray-100 rounded-lg p-1">
                    <button type="button" 
                            wire:click="switchMode('login')"
                            class="flex-1 py-2 px-4 rounded-md text-sm font-medium transition-colors {{ $mode === 'login' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Connexion
                    </button>
                    <button type="button" 
                            wire:click="switchMode('register')"
                            class="flex-1 py-2 px-4 rounded-md text-sm font-medium transition-colors {{ $mode === 'register' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Inscription
                    </button>
                </div>

                @if ($mode === 'login')
                    <!-- Login Form -->
                    <form wire:submit="login" class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Adresse email
                            </label>
                            <input type="email" 
                                   id="email" 
                                   wire:model="email" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('email') border-red-500 @enderror" 
                                   placeholder="votre@email.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Mot de passe
                            </label>
                            <input type="password" 
                                   id="password" 
                                   wire:model="password" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('password') border-red-500 @enderror" 
                                   placeholder="••••••••">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="remember" 
                                   wire:model="remember" 
                                   class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Se souvenir de moi
                            </label>
                        </div>

                        <button type="submit" 
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                            Se connecter
                        </button>
                    </form>
                @else
                    <!-- Register Form -->
                    <form wire:submit="register" class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nom complet
                            </label>
                            <input type="text" 
                                   id="name" 
                                   wire:model="name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('name') border-red-500 @enderror" 
                                   placeholder="Votre nom complet">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email_register" class="block text-sm font-medium text-gray-700 mb-1">
                                Adresse email
                            </label>
                            <input type="email" 
                                   id="email_register" 
                                   wire:model="email_register" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('email_register') border-red-500 @enderror" 
                                   placeholder="votre@email.com">
                            @error('email_register')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_register" class="block text-sm font-medium text-gray-700 mb-1">
                                Mot de passe
                            </label>
                            <input type="password" 
                                   id="password_register" 
                                   wire:model="password_register" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('password_register') border-red-500 @enderror" 
                                   placeholder="••••••••">
                            @error('password_register')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                Confirmer le mot de passe
                            </label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   wire:model="password_confirmation" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                                   placeholder="••••••••">
                        </div>

                        <button type="submit" 
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                            S'inscrire
                        </button>
                    </form>
                @endif

                <!-- Séparateur -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Ou continuez avec</span>
                    </div>
                </div>

                <!-- Social Login Buttons -->
                <div class="space-y-3">
                    <a href="{{ route('auth.redirect', 'google') }}" 
                       onclick="if(typeof trackLogin !== 'undefined') trackLogin('google');"
                       class="w-full flex justify-center items-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                        <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Continuer avec Google
                    </a>

                    <a href="{{ route('auth.redirect', 'facebook') }}" 
                       onclick="if(typeof trackLogin !== 'undefined') trackLogin('facebook');"
                       class="w-full flex justify-center items-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="#1877F2" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Continuer avec Facebook
                    </a>
                </div>

                <!-- Terms -->
                <p class="mt-6 text-xs text-center text-gray-500">
                    En vous {{ $mode === 'login' ? 'connectant' : 'inscrivant' }}, vous acceptez de participer au concours selon les règles établies.
                </p>
            </div>
        </div>
    </div>
</div>