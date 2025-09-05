@props(['wireModel' => 'photo', 'maxSize' => 5])

<div x-data="mobilePhotoUpload()" class="space-y-4">
    <div class="relative">
        <!-- Input file caché -->
        <input 
            type="file" 
            x-ref="fileInput"
            wire:model="{{ $wireModel }}"
            accept="image/*,.heic,.heif"
            capture="environment"
            class="hidden"
            @change="handleFileSelect($event)"
        >
        
        <!-- Zone de drop/click -->
        <div 
            @click="$refs.fileInput.click()"
            class="w-full p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-orange-400 transition-colors cursor-pointer bg-gray-50"
            :class="{'border-orange-400 bg-orange-50': isDragging}"
            @dragover.prevent="isDragging = true"
            @dragleave="isDragging = false"
            @drop.prevent="handleDrop($event)"
        >
            <div class="text-center">
                <!-- Preview image si disponible -->
                <div x-show="preview" class="mb-4">
                    <img :src="preview" class="mx-auto max-w-full h-48 object-cover rounded-lg shadow-md">
                </div>
                
                <!-- Icône upload si pas de preview -->
                <div x-show="!preview">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">
                        <span class="font-medium text-orange-600">Cliquez pour sélectionner</span>
                        ou glissez votre photo ici
                    </p>
                </div>
                
                <!-- Informations fichier -->
                <div x-show="fileInfo" x-transition class="mt-3 text-xs text-gray-500">
                    <p><strong>📁</strong> <span x-text="fileInfo?.name"></span></p>
                    <p><strong>📏</strong> <span x-text="formatFileSize(fileInfo?.size)"></span></p>
                </div>
                
                <!-- Barre de progression upload -->
                <div x-show="uploading" x-transition class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-500 h-2 rounded-full transition-all duration-300" 
                             :style="`width: ${uploadProgress}%`"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-1" x-text="`Upload: ${uploadProgress}%`"></p>
                </div>
            </div>
        </div>
        
        <!-- Boutons d'action -->
        <div x-show="preview" x-transition class="flex gap-2 mt-3">
            <button 
                type="button"
                @click="removePhoto()"
                class="flex-1 px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm"
            >
                🗑️ Supprimer
            </button>
            <button 
                type="button"
                @click="$refs.fileInput.click()"
                class="flex-1 px-4 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors text-sm"
            >
                🔄 Changer
            </button>
        </div>
    </div>
    
    <!-- Messages d'erreur -->
    <div x-show="error" x-transition class="p-3 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-sm text-red-800" x-text="error"></p>
    </div>
    
    <!-- Tips mobile -->
    <div class="text-xs text-gray-500 bg-blue-50 p-3 rounded-lg">
        <p><strong>📱 Sur mobile:</strong></p>
        <ul class="list-disc list-inside space-y-1 mt-1">
            <li>Appareil photo : sélection directe</li>
            <li>Galerie : choix parmi photos existantes</li>
            <li>Formats: JPG, PNG, WebP, HEIC (5MB max)</li>
        </ul>
    </div>
</div>

<script>
function mobilePhotoUpload() {
    return {
        preview: null,
        fileInfo: null,
        uploading: false,
        uploadProgress: 0,
        error: null,
        isDragging: false,
        
        init() {
            // Écouter les changements Livewire
            this.$wire.on('photoCleared', () => {
                this.removePhoto();
            });
        },
        
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.processFile(file);
            }
        },
        
        handleDrop(event) {
            this.isDragging = false;
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                this.processFile(files[0]);
            }
        },
        
        processFile(file) {
            this.error = null;
            
            // Validation taille (5MB)
            const maxSize = {{ $maxSize }} * 1024 * 1024;
            if (file.size > maxSize) {
                this.error = `Fichier trop volumineux. Taille max: {{ $maxSize }}MB`;
                return;
            }
            
            // Validation type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/heic', 'image/heif'];
            if (!allowedTypes.includes(file.type.toLowerCase())) {
                this.error = 'Format non supporté. Utilisez JPG, PNG, WebP ou HEIC.';
                return;
            }
            
            this.fileInfo = {
                name: file.name,
                size: file.size,
                type: file.type
            };
            
            // Créer preview
            this.createPreview(file);
            
            // Simuler progression upload (optionnel)
            this.simulateUpload();
        },
        
        createPreview(file) {
            // Support HEIC avec fallback
            if (file.type === 'image/heic' || file.type === 'image/heif') {
                // Pour HEIC, afficher nom du fichier au lieu du preview
                this.preview = null;
                return;
            }
            
            const reader = new FileReader();
            reader.onload = (e) => {
                this.preview = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        
        simulateUpload() {
            this.uploading = true;
            this.uploadProgress = 0;
            
            const interval = setInterval(() => {
                this.uploadProgress += Math.random() * 30;
                if (this.uploadProgress >= 100) {
                    this.uploadProgress = 100;
                    this.uploading = false;
                    clearInterval(interval);
                }
            }, 200);
        },
        
        removePhoto() {
            this.preview = null;
            this.fileInfo = null;
            this.error = null;
            this.uploading = false;
            this.uploadProgress = 0;
            this.$refs.fileInput.value = '';
        },
        
        formatFileSize(bytes) {
            if (!bytes) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    }
}
</script>