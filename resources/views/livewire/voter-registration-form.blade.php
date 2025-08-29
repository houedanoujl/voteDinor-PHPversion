<div class="max-w-md mx-auto">
    <form wire:submit.prevent="submit" class="space-y-6">
        <!-- Messages de statut -->
        @if (session()->has('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Titre -->
        <div class="text-center">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">🗳️ Devenir Votant</h3>
            <p class="text-gray-600 text-sm">
                Créez votre compte pour voter pour vos candidats préférés
            </p>
        </div>

        <!-- Champs du formulaire -->
        <div class="space-y-4">
            <!-- Prénom -->
            <div>
                <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">
                    Prénom *
                </label>
                <input
                    type="text"
                    id="prenom"
                    wire:model="prenom"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('prenom') border-red-500 @enderror"
                    placeholder="Votre prénom"
                >
                @error('prenom')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nom -->
            <div>
                <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">
                    Nom *
                </label>
                <input
                    type="text"
                    id="nom"
                    wire:model="nom"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('nom') border-red-500 @enderror"
                    placeholder="Votre nom"
                >
                @error('nom')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- WhatsApp -->
            <div>
                <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1">
                    Numéro WhatsApp *
                </label>
                <input
                    type="text"
                    id="whatsapp"
                    wire:model="whatsapp"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('whatsapp') border-red-500 @enderror"
                    placeholder="+225XXXXXXXXXX"
                >
                <p class="mt-1 text-xs text-gray-500">
                    Format: +225 suivi de 10 chiffres
                </p>
                @error('whatsapp')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Bouton de soumission -->
        <button
            type="submit"
            class="w-full bg-orange-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-orange-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            wire:loading.attr="disabled"
        >
            <div wire:loading wire:target="submit" class="mr-2">
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <span wire:loading.remove wire:target="submit">Créer mon compte votant</span>
            <span wire:loading wire:target="submit">Création en cours...</span>
        </button>

        <!-- Informations -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-900 mb-2 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Compte votant
            </h4>
            <ul class="text-xs text-blue-800 space-y-1">
                <li>• Vous pourrez voter pour vos candidats préférés</li>
                <li>• Vous recevrez vos identifiants par WhatsApp</li>
                <li>• Vous ne pourrez pas uploader de photo</li>
            </ul>
        </div>
    </form>
</div>
