<div>
    <!-- Bouton pour ouvrir le modal -->
    <button
        wire:click="openModal"
        wire:loading.attr="disabled"
        class="bg-orange-600 text-white px-8 py-4 font-bold rounded-lg shadow-lg hover:shadow-xl hover:bg-orange-700 transition-all duration-300 group disabled:opacity-50"
    >
        <span class="flex items-center">
            🎯 Participer au concours
            <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
            </svg>
        </span>
    </button>

    <!-- Modal moderne -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Background overlay avec blur -->
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <!-- Modal panel moderne -->
                <div class="inline-block w-full max-w-2xl bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all relative z-10">
                    <form wire:submit.prevent="submit" class="bg-white">
                        <!-- Header -->
                        <div class="bg-gray-900 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-2xl font-bold text-white" id="modal-title">
                                    🎯 Inscription au Concours Photo DINOR
                                </h3>
                                <button wire:click="closeModal" class="text-white hover:text-gray-200 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="px-6 py-6">
                            <!-- Photo Upload moderne -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    📸 Photo du plat *
                                </label>

                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-orange-500 transition-colors cursor-pointer relative overflow-hidden">
                                    @if($tempPhotoUrl)
                                        <div class="relative">
                                            <img src="{{ $tempPhotoUrl }}" alt="Aperçu" class="max-h-48 mx-auto rounded-lg mb-4 shadow-lg">
                                            <div class="absolute inset-0 bg-black/20 rounded-lg opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <span class="text-white font-medium">Cliquez pour changer</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="space-y-4">
                                            <div class="text-6xl">📸</div>
                                            <div>
                                                <p class="text-lg font-medium text-gray-700 mb-2">Ajoutez votre photo</p>
                                                <p class="text-sm text-gray-500">Cliquez ou glissez-déposez votre image</p>
                                                <p class="text-xs text-gray-400 mt-1">Taille maximum: 3MB</p>
                                            </div>
                                        </div>
                                    @endif

                                    <input
                                        type="file"
                                        wire:model="photo"
                                        accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    >
                                </div>
                                @error('photo')
                                    <span class="text-red-500 text-sm mt-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror

                                <div wire:loading wire:target="photo" class="text-orange-600 text-sm mt-2 flex items-center">
                                    <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Chargement de la photo...
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Prénom -->
                                <div>
                                                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Prénom *
                                </label>
                                    <input
                                        type="text"
                                        wire:model="prenom"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="Votre prénom"
                                    >
                                    @error('prenom')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Nom -->
                                <div>
                                                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Nom *
                                </label>
                                    <input
                                        type="text"
                                        wire:model="nom"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="Votre nom"
                                    >
                                    @error('nom')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Email -->
                                @guest
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        ✉️ Email *
                                    </label>
                                    <input
                                        type="email"
                                        wire:model="email"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="votre@email.com"
                                    >
                                    @error('email')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                    <p class="text-xs text-gray-500 mt-1">Un compte sera créé automatiquement avec cet email</p>
                                </div>
                                @endguest

                                <!-- WhatsApp -->
                                <div>
                                                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    WhatsApp *
                                </label>
                                    <input
                                        type="tel"
                                        wire:model="whatsapp"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="+225XXXXXXXX"
                                    >
                                    @error('whatsapp')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>



                            <!-- Informations -->
                            <div class="mt-6 p-4 bg-gray-50 rounded-xl">
                                <h4 class="font-semibold text-gray-700 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Informations importantes
                            </h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• Votre photo sera visible par tous les visiteurs</li>
                                    <li>• Vous recevrez une notification WhatsApp lors de l'approbation</li>
                                    <li>• Les votes sont limités à 1 par candidat par jour</li>
                                    <li>• Le concours se termine le 31 décembre 2024</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row gap-3 justify-end">
                            <button
                                type="button"
                                wire:click="closeModal"
                                class="px-6 py-3 text-gray-600 hover:text-gray-800 transition-colors font-medium"
                            >
                                Annuler
                            </button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-3 rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <span wire:loading.remove wire:target="submit">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Soumettre ma candidature
                                </span>
                                <span wire:loading wire:target="submit" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Envoi en cours...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('livewire:init', function () {
    Livewire.on('track-registration', (event) => {
        if (typeof trackRegistration !== 'undefined') {
            trackRegistration(event.candidateName);
        }
    });

    Livewire.on('userRegistered', () => {
        // Rafraîchir la page pour montrer que l'utilisateur est maintenant connecté
        setTimeout(() => {
            window.location.reload();
        }, 2000); // Délai pour laisser le temps de voir le message de succès
    });
});
</script>
@endpush
