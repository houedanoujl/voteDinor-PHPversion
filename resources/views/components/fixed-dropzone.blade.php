@props(['wireModel' => 'photo', 'maxSize' => 5])

<div x-data="fixedDropzone()" class="w-full">
    <!-- Input file avec wire:model natif -->
    <input 
        type="file" 
        x-ref="fileInput"
        wire:model="{{ $wireModel }}"
        accept="image/*,.heic,.heif"
        capture="environment"
        class="hidden"
        @change="handleFileChange($event)"
    >
    
    <!-- Zone de drop stylisée -->
    <div 
        @click="$refs.fileInput.click()"
        @dragover.prevent="dragover = true"
        @dragleave="dragover = false"
        @drop.prevent="handleDrop($event)"
        class="relative border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-all duration-200"
        :class="dragover ? 'border-orange-400 bg-orange-50' : (hasFile ? 'border-green-400 bg-green-50' : 'border-gray-300 hover:border-orange-300 hover:bg-orange-50')"
    >
        <!-- Preview ou état initial -->
        <div x-show="!hasFile" class="space-y-4">
            <div class="text-6xl">📸</div>
            <div>
                <h3 class="text-lg font-semibold text-gray-700">
                    Ajoutez votre photo
                </h3>
                <p class="text-gray-500 mt-2">
                    Cliquez ici ou glissez votre image
                </p>
                <p class="text-sm text-gray-400 mt-1">
                    JPG, PNG, WebP, HEIC • Max {{ $maxSize }}MB
                </p>
            </div>
        </div>
        
        <!-- Fichier sélectionné -->
        <div x-show="hasFile" class="space-y-4">
            <!-- Preview image si possible -->
            <div x-show="preview">
                <img :src="preview" class="mx-auto max-h-48 rounded-lg shadow-md">
            </div>
            
            <!-- Info fichier HEIC ou autre -->
            <div class="text-sm" :class="isHeic ? 'text-blue-700' : 'text-green-700'">
                <p class="font-medium" x-text="fileName"></p>
                <p x-text="fileSize"></p>
                <p x-show="isHeic" class="text-xs mt-1">📱 Fichier HEIC (preview non disponible)</p>
            </div>
            
            <button 
                type="button"
                @click.stop="clearFile()"
                class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-sm"
            >
                🗑️ Changer de photo
            </button>
        </div>
        
        <!-- Indicateur de drag -->
        <div x-show="dragover" class="absolute inset-0 bg-orange-100 bg-opacity-75 flex items-center justify-center rounded-lg">
            <div class="text-2xl text-orange-600">📥 Déposez votre photo ici</div>
        </div>
    </div>
    
    <!-- Messages d'erreur -->
    <div x-show="error" x-transition class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-sm text-red-800" x-text="error"></p>
    </div>
    
    <!-- Instructions -->
    <div class="mt-3 text-xs text-gray-500 bg-gray-50 p-3 rounded-lg">
        <p><strong>📱 Mobile :</strong> Cliquez pour accéder à votre galerie ou appareil photo</p>
        <p><strong>💻 Desktop :</strong> Glissez-déposez directement votre image</p>
    </div>
</div>

<script>
function fixedDropzone() {
    return {
        hasFile: false,
        preview: null,
        fileName: null,
        fileSize: null,
        error: null,
        dragover: false,
        isHeic: false,
        
        // Quand l'input change (géré par Livewire)
        handleFileChange(event) {
            const file = event.target.files[0];
            console.log('📁 Fichier détecté par Livewire:', file);
            
            if (file) {
                this.processFileInfo(file);
            } else {
                this.resetState();
            }
        },
        
        // Gestion du drag & drop
        handleDrop(event) {
            this.dragover = false;
            const files = event.dataTransfer.files;
            
            if (files.length > 0) {
                const file = files[0];
                console.log('📥 Fichier glissé:', file);
                
                // Validation rapide côté client
                if (!this.validateFile(file)) {
                    return;
                }
                
                // Assigner le fichier à l'input pour que Livewire le traite
                try {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    this.$refs.fileInput.files = dt.files;
                    
                    // Déclencher l'événement pour Livewire
                    this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                } catch (e) {
                    console.error('Erreur DataTransfer:', e);
                    this.error = 'Erreur lors du glisser-déposer. Utilisez le clic.';
                }
            }
        },
        
        // Traiter les informations du fichier pour l'affichage
        processFileInfo(file) {
            this.error = null;
            this.hasFile = true;
            this.fileName = file.name;
            this.fileSize = this.formatBytes(file.size);
            this.isHeic = this.isHeicFile(file);
            
            // Créer preview si possible
            if (!this.isHeic) {
                this.createPreview(file);
            } else {
                this.preview = null;
            }
        },
        
        // Validation côté client
        validateFile(file) {
            this.error = null;
            
            // Taille
            const maxBytes = {{ $maxSize }} * 1024 * 1024;
            if (file.size > maxBytes) {
                this.error = `Fichier trop volumineux: ${this.formatBytes(file.size)}. Maximum: {{ $maxSize }}MB`;
                return false;
            }
            
            // Type
            const allowedTypes = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 
                'image/webp', 'image/heic', 'image/heif'
            ];
            
            const isValidType = allowedTypes.includes(file.type.toLowerCase()) ||
                               file.name.toLowerCase().match(/\.(jpg|jpeg|png|gif|webp|heic|heif)$/);
            
            if (!isValidType) {
                this.error = 'Format non supporté. Utilisez JPG, PNG, WebP ou HEIC.';
                return false;
            }
            
            return true;
        },
        
        // Créer preview
        createPreview(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.preview = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        
        // Vérifier si c'est un HEIC
        isHeicFile(file) {
            return ['image/heic', 'image/heif'].includes(file.type.toLowerCase()) ||
                   file.name.toLowerCase().match(/\.(heic|heif)$/);
        },
        
        // Effacer le fichier
        clearFile() {
            this.$refs.fileInput.value = '';
            this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            this.resetState();
        },
        
        // Réinitialiser l'état
        resetState() {
            this.hasFile = false;
            this.preview = null;
            this.fileName = null;
            this.fileSize = null;
            this.error = null;
            this.isHeic = false;
        },
        
        // Formatage taille
        formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
        }
    }
}
</script>