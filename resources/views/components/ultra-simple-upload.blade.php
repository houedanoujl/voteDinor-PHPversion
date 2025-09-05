@props(['wireModel' => 'photo'])

<div>
    <!-- Zone d'upload stylée SANS Alpine -->
    <label for="photo-upload" class="block cursor-pointer">
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-orange-400 hover:bg-orange-50 transition-all">
            <div class="text-6xl mb-4">📁</div>
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
    </label>

    <!-- Input file NATIF Livewire pur -->
    <input 
        type="file" 
        id="photo-upload"
        wire:model="{{ $wireModel }}"
        accept=".jpg,.jpeg,.png"
        class="hidden"
        onchange="
            console.log('🔥 FICHIER SELECTIONNE:', this.files[0]); 
            console.log('🔥 NOM:', this.files[0]?.name);
            console.log('🔥 TAILLE:', this.files[0]?.size);
            console.log('🔥 TYPE:', this.files[0]?.type);
        "
    >

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

    <!-- Instructions simples -->
    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-2">
            <div class="text-blue-600 text-lg">💡</div>
            <div class="text-sm text-blue-800">
                <p><strong>Format requis :</strong> JPG ou PNG uniquement (Max 5MB)</p>
            </div>
        </div>
    </div>
</div>