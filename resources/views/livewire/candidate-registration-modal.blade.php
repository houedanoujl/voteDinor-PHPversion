<div>
    <!-- Bouton pour ouvrir le modal -->
    <button 
        wire:click="openModal" 
        class="btn-dinor px-6 py-3 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300"
    >
        🎯 Participer au concours
    </button>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="submit" class="bg-dinor-cream">
                        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full">
                                    <h3 class="text-2xl leading-6 font-retro text-dinor-brown mb-4" id="modal-title">
                                        🎯 Inscription au Concours Photo DINOR
                                    </h3>
                                    
                                    <!-- Photo Upload -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-dinor-brown mb-2">
                                            Photo du plat *
                                        </label>
                                        
                                        <div class="border-2 border-dashed border-dinor-beige rounded-lg p-4 text-center">
                                            @if($tempPhotoUrl)
                                                <img src="{{ $tempPhotoUrl }}" alt="Aperçu" class="max-h-32 mx-auto rounded-lg mb-2">
                                                <p class="text-sm text-dinor-olive">Cliquez pour changer</p>
                                            @else
                                                <div class="text-4xl mb-2">📸</div>
                                                <p class="text-sm text-dinor-brown">Cliquez pour ajouter votre photo</p>
                                            @endif
                                            
                                            <input 
                                                type="file" 
                                                wire:model="photo" 
                                                accept="image/*" 
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                            >
                                        </div>
                                        @error('photo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        
                                        <div wire:loading wire:target="photo" class="text-dinor-olive text-xs mt-1">
                                            Chargement de la photo...
                                        </div>
                                    </div>

                                    <!-- Prénom -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-dinor-brown mb-2">
                                            Prénom *
                                        </label>
                                        <input 
                                            type="text" 
                                            wire:model="prenom" 
                                            class="input-dinor w-full"
                                            placeholder="Votre prénom"
                                        >
                                        @error('prenom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Nom -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-dinor-brown mb-2">
                                            Nom *
                                        </label>
                                        <input 
                                            type="text" 
                                            wire:model="nom" 
                                            class="input-dinor w-full"
                                            placeholder="Votre nom"
                                        >
                                        @error('nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- WhatsApp -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-dinor-brown mb-2">
                                            WhatsApp *
                                        </label>
                                        <input 
                                            type="tel" 
                                            wire:model="whatsapp" 
                                            class="input-dinor w-full"
                                            placeholder="+225XXXXXXXX"
                                        >
                                        @error('whatsapp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-dinor-brown mb-2">
                                            Description (optionnelle)
                                        </label>
                                        <textarea 
                                            wire:model="description" 
                                            rows="3"
                                            class="input-dinor w-full resize-none"
                                            placeholder="Parlez-nous de votre plat, votre inspiration..."
                                        ></textarea>
                                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="bg-dinor-beige px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button 
                                type="submit" 
                                class="w-full inline-flex justify-center btn-dinor px-4 py-2 text-base font-medium text-white sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove>S'inscrire</span>
                                <span wire:loading>Inscription...</span>
                            </button>
                            
                            <button 
                                type="button" 
                                wire:click="closeModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-dinor-brown bg-white px-4 py-2 text-base font-medium text-dinor-brown hover:bg-dinor-cream sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Annuler
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
