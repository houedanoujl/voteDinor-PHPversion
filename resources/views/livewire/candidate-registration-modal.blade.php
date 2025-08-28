<div>
    <!-- Bouton pour ouvrir le modal -->
    <button
        wire:click="openModal"
        class="btn-dinor px-8 py-4 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 group"
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
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay avec blur -->
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

                <!-- Modal panel moderne -->
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit.prevent="submit" class="bg-white">
                        <!-- Header -->
                        <div class="bg-gradient-dinor px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-2xl font-bold text-white" id="modal-title">
                                    🎯 Inscription au Concours Photo DINOR
                                </h3>
                                <button wire:click="closeModal" class="text-white hover:text-dinor-cream transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="px-6 py-6">
                            <!-- Photo Upload moderne -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-dinor-brown mb-3">
                                    📸 Photo du plat *
                                </label>

                                <div class="border-2 border-dashed border-dinor-gray-300 rounded-xl p-8 text-center hover:border-dinor-orange transition-colors cursor-pointer relative overflow-hidden">
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
                                                <p class="text-lg font-medium text-dinor-brown mb-2">Ajoutez votre photo</p>
                                                <p class="text-sm text-dinor-gray-500">Cliquez ou glissez-déposez votre image</p>
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

                                <div wire:loading wire:target="photo" class="text-dinor-orange text-sm mt-2 flex items-center">
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
                                    <label class="block text-sm font-semibold text-dinor-brown mb-2">
                                        👤 Prénom *
                                    </label>
                                    <input
                                        type="text"
                                        wire:model="prenom"
                                        class="input-modern w-full"
                                        placeholder="Votre prénom"
                                    >
                                    @error('prenom')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Nom -->
                                <div>
                                    <label class="block text-sm font-semibold text-dinor-brown mb-2">
                                        📝 Nom *
                                    </label>
                                    <input
                                        type="text"
                                        wire:model="nom"
                                        class="input-modern w-full"
                                        placeholder="Votre nom"
                                    >
                                    @error('nom')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-semibold text-dinor-brown mb-2">
                                        📧 Email
                                    </label>
                                    <input
                                        type="email"
                                        wire:model="email"
                                        class="input-modern w-full"
                                        placeholder="votre@email.com"
                                    >
                                    @error('email')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- WhatsApp -->
                                <div>
                                    <label class="block text-sm font-semibold text-dinor-brown mb-2">
                                        📱 WhatsApp *
                                    </label>
                                    <input
                                        type="tel"
                                        wire:model="whatsapp"
                                        class="input-modern w-full"
                                        placeholder="+225XXXXXXXX"
                                    >
                                    @error('whatsapp')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mt-6">
                                <label class="block text-sm font-semibold text-dinor-brown mb-2">
                                    📖 Description de votre plat
                                </label>
                                <textarea
                                    wire:model="description"
                                    rows="4"
                                    class="input-modern w-full resize-none"
                                    placeholder="Décrivez votre plat, les ingrédients utilisés, l'inspiration..."
                                ></textarea>
                                @error('description')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Informations -->
                            <div class="mt-6 p-4 bg-dinor-gray-50 rounded-xl">
                                <h4 class="font-semibold text-dinor-brown mb-2">ℹ️ Informations importantes</h4>
                                <ul class="text-sm text-dinor-gray-600 space-y-1">
                                    <li>• Votre photo sera visible par tous les visiteurs</li>
                                    <li>• Vous recevrez une notification WhatsApp lors de l'approbation</li>
                                    <li>• Les votes sont limités à 1 par candidat par jour</li>
                                    <li>• Le concours se termine le 31 décembre 2024</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-dinor-gray-50 px-6 py-4 flex flex-col sm:flex-row gap-3 justify-end">
                            <button
                                type="button"
                                wire:click="closeModal"
                                class="px-6 py-3 text-dinor-gray-600 hover:text-dinor-brown transition-colors font-medium"
                            >
                                Annuler
                            </button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="btn-dinor px-8 py-3 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span wire:loading.remove wire:target="submit">
                                    🚀 Soumettre ma candidature
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

<script>
document.addEventListener('livewire:init', function () {
    Livewire.on('track-registration', (event) => {
        if (typeof trackRegistration !== 'undefined') {
            trackRegistration(event.candidateName);
        }
    });
});
</script>
