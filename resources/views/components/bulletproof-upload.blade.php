@props(['wireModel' => 'photo'])

<div>
    <!-- Upload Livewire NATIF - Le plus simple et fiable -->
    <div 
        x-data="{ uploading: false, progress: 0 }"
        x-on:livewire-upload-start="uploading = true"
        x-on:livewire-upload-finish="uploading = false"
        x-on:livewire-upload-error="uploading = false"
        x-on:livewire-upload-progress="progress = $event.detail.progress"
    >
        <!-- Zone d'upload stylée -->
        <label for="photo-upload" class="block cursor-pointer">
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-orange-400 hover:bg-orange-50 transition-all">
                
                <!-- État normal -->
                <div x-show="!uploading" class="space-y-4">
                    <div class="text-6xl">📁</div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">
                            Sélectionnez votre photo
                        </h3>
                        <p class="text-gray-500 mt-2">
                            Cliquez ici pour choisir depuis votre galerie
                        </p>
                        <p class="text-sm text-gray-400 mt-1">
                            JPG, PNG uniquement • Max 5MB
                        </p>
                    </div>
                </div>

                <!-- État upload en cours -->
                <div x-show="uploading" class="space-y-4">
                    <div class="text-6xl animate-pulse">⏳</div>
                    <div>
                        <h3 class="text-lg font-semibold text-orange-600">
                            Upload en cours...
                        </h3>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                            <div 
                                class="bg-orange-500 h-2 rounded-full transition-all duration-300" 
                                :style="`width: ${progress}%`"
                            ></div>
                        </div>
                        <p class="text-sm text-gray-600 mt-1" x-text="`${progress}%`"></p>
                    </div>
                </div>
            </div>
        </label>

        <!-- Input file NATIF Livewire -->
        <input 
            type="file" 
            id="photo-upload"
            wire:model="{{ $wireModel }}"
            accept=".jpg,.jpeg,.png"
            class="hidden"
            onchange="console.log('🔥 FICHIER SELECTIONNE:', this.files[0]); console.log('🔥 INPUT VALUE:', this.value);"
        >
    </div>

    <!-- Le composant ne peut pas accéder à $photo directement -->

    <!-- Messages d'erreur Livewire -->
    @error('photo')
        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-2">
                <div class="text-red-600 text-lg">⚠️</div>
                <div class="text-sm text-red-800">
                    <p><strong>Erreur :</strong> {{ $message }}</p>
                </div>
            </div>
        </div>
    @enderror

    <!-- Instructions -->
    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-2">
            <div class="text-blue-600 text-lg">💡</div>
            <div class="text-sm text-blue-800">
                <p><strong>📱 iPhone :</strong> Allez dans Réglages → Appareil photo → Formats → "Le plus compatible" pour avoir des photos JPG</p>
                <p class="mt-1"><strong>📱 Android :</strong> Vos photos sont déjà en JPG par défaut</p>
            </div>
        </div>
    </div>
</div>