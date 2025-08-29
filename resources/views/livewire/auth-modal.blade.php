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


                <!-- Terms -->
                <p class="mt-6 text-xs text-center text-gray-500">
                    En vous {{ $mode === 'login' ? 'connectant' : 'inscrivant' }}, vous acceptez de participer au concours selon les règles établies.
                </p>
            </div>
        </div>
    </div>
</div>