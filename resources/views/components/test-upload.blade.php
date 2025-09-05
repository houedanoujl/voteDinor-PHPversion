@props(['wireModel' => 'photo'])

<div x-data="photoUpload()">
    <!-- Input file standard avec style de dropzone -->
    <div class="relative">
        <input 
            type="file" 
            wire:model="{{ $wireModel }}"
            accept=".jpg,.jpeg,.png"
            capture="environment"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
            @change="handleFileChange($event)"
        >
        
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-orange-400 hover:bg-orange-50 transition-all">
            <div class="text-6xl mb-4">📸</div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">
                Ajoutez votre photo
            </h3>
            <p class="text-gray-500">
                Cliquez ici pour sélectionner une photo
            </p>
            <p class="text-sm text-gray-400 mt-2">
                <strong>Formats acceptés :</strong> JPG, PNG uniquement • Max 5MB
            </p>
        </div>
    </div>
    
    <!-- Instructions pour iPhone -->
    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-2">
            <div class="text-blue-600 text-lg">📱</div>
            <div class="text-sm text-blue-800">
                <p><strong>iPhone :</strong> 
                <button @click="showIphoneHelp = true" class="text-blue-600 underline font-medium">
                    Comment prendre une photo JPG ?
                </button>
                </p>
                <p class="mt-1"><strong>Android/PC :</strong> Sélectionnez directement vos photos JPG ou PNG</p>
            </div>
        </div>
    </div>

    <!-- Message d'erreur format -->
    <div x-show="formatError" x-transition class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-start gap-2">
            <div class="text-red-600 text-lg">⚠️</div>
            <div class="text-sm text-red-800">
                <p><strong>Format non supporté !</strong></p>
                <p x-text="errorMessage"></p>
                <button @click="showIphoneHelp = true" class="mt-2 text-red-600 underline font-medium">
                    → Voir les instructions iPhone
                </button>
            </div>
        </div>
    </div>
    
    <!-- Popup d'instructions iPhone -->
    <div x-show="showIphoneHelp" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showIphoneHelp = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-md mx-4 p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-bold text-gray-900">📱 Instructions iPhone</h3>
                <button @click="showIphoneHelp = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            
            <div class="space-y-4 text-sm text-gray-700">
                <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                    <p class="font-medium text-yellow-800">⚠️ Votre iPhone utilise le format HEIC par défaut</p>
                </div>
                
                <div class="space-y-3">
                    <h4 class="font-semibold text-gray-900">📋 Étapes à suivre :</h4>
                    
                    <div class="space-y-2">
                        <div class="flex gap-3">
                            <span class="bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">1</span>
                            <p>Allez dans <strong>Réglages → Appareil photo</strong></p>
                        </div>
                        
                        <div class="flex gap-3">
                            <span class="bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">2</span>
                            <p>Trouvez <strong>"Formats"</strong></p>
                        </div>
                        
                        <div class="flex gap-3">
                            <span class="bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">3</span>
                            <p>Sélectionnez <strong>"Le plus compatible"</strong> au lieu de "Haute efficacité"</p>
                        </div>
                        
                        <div class="flex gap-3">
                            <span class="bg-green-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">✓</span>
                            <p><strong>Vos photos seront maintenant en JPG</strong></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                    <p class="text-green-800"><strong>💡 Alternative :</strong> Utilisez une photo existante de votre galerie qui est déjà en JPG</p>
                </div>
            </div>
            
            <div class="mt-6 flex gap-3">
                <button @click="showIphoneHelp = false" class="flex-1 bg-orange-500 text-white py-2 px-4 rounded-lg font-medium hover:bg-orange-600">
                    J'ai compris
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function photoUpload() {
    return {
        showIphoneHelp: false,
        formatError: false,
        errorMessage: '',
        
        handleFileChange(event) {
            const file = event.target.files[0];
            
            if (file) {
                console.log('📁 Fichier sélectionné:', file.name, 'Type:', file.type);
                
                // Vérifier le format
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                const fileExtension = file.name.toLowerCase().split('.').pop();
                const allowedExtensions = ['jpg', 'jpeg', 'png'];
                
                if (!allowedTypes.includes(file.type.toLowerCase()) && !allowedExtensions.includes(fileExtension)) {
                    this.formatError = true;
                    
                    if (file.type === 'image/heic' || file.type === 'image/heif' || fileExtension === 'heic' || fileExtension === 'heif') {
                        this.errorMessage = "Fichier HEIC détecté. Changez les réglages de votre iPhone pour utiliser JPG.";
                    } else if (file.type === 'image/webp' || fileExtension === 'webp') {
                        this.errorMessage = "Format WebP non supporté. Utilisez JPG ou PNG.";
                    } else {
                        this.errorMessage = `Format "${fileExtension.toUpperCase()}" non supporté. Utilisez JPG ou PNG uniquement.`;
                    }
                    
                    // Vider l'input
                    event.target.value = '';
                    return;
                }
                
                // Format OK
                this.formatError = false;
                this.errorMessage = '';
            }
        }
    }
}
</script>