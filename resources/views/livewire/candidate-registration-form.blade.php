<div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
    <!-- En-tête du formulaire -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">🎯 Participer au concours</h2>
        <p class="text-gray-600">Remplissez le formulaire ci-dessous pour vous inscrire</p>
    </div>

    <!-- Messages de succès/erreur -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Formulaire -->
    <form wire:submit.prevent="submit" class="space-y-6">
        <!-- Photo Upload Amélioré -->
        @php($settings = \App\Models\SiteSetting::first())
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                📸 Votre photo de concours 
                @if(($settings?->uploads_enabled ?? true) === false)
                    <span class="text-xs text-gray-500 font-normal">(temporairement désactivé)</span>
                @endif
            </label>

            @if(($settings?->uploads_enabled ?? true) !== false)
                <x-ultra-simple-upload wire-model="photo" />
            @else
                <div class="p-4 bg-gray-100 border border-gray-300 rounded-lg text-center">
                    <p class="text-gray-600">📴 Upload temporairement désactivé</p>
                </div>
            @endif

            @error('photo')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Informations personnelles -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Prénom -->
            <div>
                <label for="prenom" class="block text-sm font-semibold text-gray-700 mb-2">
                    👤 Prénom *
                </label>
                <input type="text" id="prenom" wire:model="prenom"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                       placeholder="Votre prénom">
                @error('prenom')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nom -->
            <div>
                <label for="nom" class="block text-sm font-semibold text-gray-700 mb-2">
                    👤 Nom *
                </label>
                <input type="text" id="nom" wire:model="nom"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                       placeholder="Votre nom">
                @error('nom')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Email supprimé -->

        <!-- WhatsApp -->
        <div>
            <label for="whatsapp" class="block text-sm font-semibold text-gray-700 mb-2">
                📱 WhatsApp *
            </label>
            <input type="text" id="whatsapp" wire:model="whatsapp"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                   placeholder="+225XXXXXXXXXX">
            <p class="mt-1 text-xs text-gray-500">Format: +225 suivi de 10 chiffres</p>
            @error('whatsapp')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>



        <!-- Bouton de soumission -->
        <div class="pt-4">
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold py-4 px-6 rounded-lg hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">

                <span wire:loading.remove>
                    🚀 Participer au concours
                </span>

                <span wire:loading class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Inscription en cours...
                </span>
            </button>
        </div>

        <!-- Note légale -->
        <div class="text-center pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-500">
                En vous inscrivant, vous acceptez que vos informations soient utilisées pour le concours et que vous puissiez recevoir des notifications WhatsApp.
            </p>
        </div>
    </form>
</div>
