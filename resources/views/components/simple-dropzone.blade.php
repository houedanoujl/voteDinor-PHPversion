@props(['wireModel' => 'photo', 'maxSize' => 5])

<div
    x-data="simpleDropzone({ serverError: @js($errors->first($wireModel)) })"
    class="w-full"
    x-on:livewire-upload-start="onUploadStart()"
    x-on:livewire-upload-progress="onUploadProgress($event)"
    x-on:livewire-upload-error="onUploadError()"
    x-on:livewire-upload-finish="onUploadFinish()"
>
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
    
    <!-- Zone de drop -->
    <div 
        @click="$refs.fileInput.click()"
        @dragover.prevent="dragover = true"
        @dragleave="dragover = false"
        @drop.prevent="handleDrop($event)"
        class="relative border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-all duration-200"
        :class="{
            'border-orange-400 bg-orange-50': dragover,
            'border-orange-500 bg-orange-50 animate-pulse': uploading,
            'border-green-500 bg-green-50': !uploading && uploadSuccess,
            'border-red-500 bg-red-50': error,
            'border-blue-400 bg-blue-50': !uploading && !uploadSuccess && preview && !error,
            'border-gray-300 hover:border-orange-300 hover:bg-orange-50': !dragover && !preview && !uploading && !uploadSuccess && !error,
        }"
    >
        <!-- Preview de l'image -->
        <div x-show="preview" class="space-y-4">
            <img :src="preview" class="mx-auto max-h-48 rounded-lg shadow-md">
            <div class="text-sm text-green-700">
                <p class="font-medium" x-text="fileName"></p>
                <p x-text="fileSize"></p>
            </div>

            <!-- États visuels -->
            <div class="flex items-center justify-center gap-2">
                <span
                    x-show="!uploading && !uploadSuccess"
                    class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700"
                >Sélectionné</span>
                <span
                    x-show="uploading"
                    class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700"
                >Téléversement… <span class="ml-1" x-text="progress + '%' "></span></span>
                <span
                    x-show="!uploading && uploadSuccess"
                    class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700"
                >✔️ Upload réussi</span>
            </div>

            <button 
                type="button"
                @click.stop="clearFile()"
                class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-sm"
            >
                🗑️ Supprimer
            </button>
            <button
                type="button"
                @click.stop="$refs.fileInput.click()"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm"
            >
                🔄 Changer de photo
            </button>
        </div>
        
        <!-- Interface d'upload -->
        <div x-show="!preview" class="space-y-4">
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
        
        <!-- Fichier HEIC info -->
        <div x-show="isHeic && !preview" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                📱 <strong>Fichier HEIC détecté :</strong> <span x-text="fileName || 'Fichier sélectionné'"></span>
            </p>
            <p class="text-xs text-blue-600 mt-1">
                Les fichiers HEIC ne peuvent pas être prévisualisés mais sont acceptés
            </p>
        </div>

        <!-- Barre de progression -->
        <div x-show="uploading" class="absolute left-0 right-0 bottom-0 h-2 bg-gray-200 rounded-b-lg overflow-hidden">
            <div class="h-full bg-orange-500 transition-all duration-150" :style="{ width: progress + '%' }"></div>
        </div>
    </div>
    
    <!-- Messages d'erreur -->
    <div x-show="error" x-transition class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-sm text-red-800" x-text="error"></p>
    </div>
    
    <!-- Instructions mobile -->
    <div class="mt-3 text-xs text-gray-500 bg-gray-50 p-3 rounded-lg">
        <p><strong>📱 Mobile :</strong> L'appareil photo s'ouvrira automatiquement</p>
        <p><strong>🖥️ Desktop :</strong> Glissez-déposez ou cliquez pour sélectionner</p>
    </div>

    <!-- Conseils iPhone pour HEIC -->
    <div x-show="isIOS" class="mt-2 text-xs text-gray-600 bg-amber-50 border border-amber-200 p-3 rounded-lg">
        <p class="font-medium">📣 Utilisateurs iPhone :</p>
        <p class="mt-1">Si votre photo est au format <strong>.HEIC</strong>, nous l'acceptons, mais certains aperçus peuvent ne pas s'afficher. Pour plus de compatibilité, vous pouvez la prendre en <strong>JPEG</strong> :</p>
        <ul class="list-disc ml-5 mt-1">
            <li>Réglages → Appareil photo → Formats → choisir <strong>Plus compatible</strong></li>
            <li>Ou exporter la photo en JPEG depuis l'app Photos avant l'envoi</li>
        </ul>
    </div>
</div>

<script>
function simpleDropzone(options = {}) {
    return {
        preview: null,
        fileName: null,
        fileSize: null,
        error: options.serverError || null,
        dragover: false,
        isHeic: false,
        isIOS: /iPhone|iPad|iPod/i.test(navigator.userAgent),
        uploading: false,
        progress: 0,
        uploadSuccess: false,
        
        handleFileSelect(event) {
            const file = event.target.files[0];
            console.log('🔧 File selected:', file);
            if (file) {
                this.uploadSuccess = false;
                this.progress = 0;
                this.processFile(file);
                // S'assurer que Livewire détecte le changement
                this.$nextTick(() => {
                    this.$refs.fileInput.dispatchEvent(new Event('input', { bubbles: true }));
                });
            }
        },
        
        handleDrop(event) {
            this.dragover = false;
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                // Assigner le fichier déposé à l'input caché pour que Livewire le prenne en compte
                try {
                    const dt = new DataTransfer();
                    dt.items.add(files[0]);
                    this.$refs.fileInput.files = dt.files;
                    // Déclencher l'événement change et input pour Livewire
                    this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                    this.$refs.fileInput.dispatchEvent(new Event('input', { bubbles: true }));
                } catch (e) {
                    // Fallback: si DataTransfer n'est pas supporté, traiter directement pour l'aperçu
                    this.processFile(files[0]);
                }
            }
        },
        
        processFile(file) {
            this.error = null;
            this.isHeic = false;
            
            console.log('🔧 Processing file:', {
                name: file.name,
                size: file.size,
                type: file.type
            });
            
            // Validation taille
            const maxBytes = {{ $maxSize }} * 1024 * 1024;
            if (file.size > maxBytes) {
                this.error = `Fichier trop volumineux: ${this.formatBytes(file.size)}. Maximum: {{ $maxSize }}MB`;
                return;
            }
            
            // Types acceptés
            const allowedTypes = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 
                'image/webp', 'image/heic', 'image/heif'
            ];
            
            if (!allowedTypes.includes(file.type.toLowerCase())) {
                this.error = 'Format non supporté. Utilisez JPG, PNG, WebP ou HEIC.';
                return;
            }
            
            this.fileName = file.name;
            this.fileSize = this.formatBytes(file.size);
            
            // Vérifier si c'est un HEIC
            if (['image/heic', 'image/heif'].includes(file.type.toLowerCase()) || 
                file.name.toLowerCase().match(/\.(heic|heif)$/)) {
                this.isHeic = true;
                this.preview = null;
            } else {
                // Créer preview pour les autres formats
                this.createPreview(file);
            }
        },
        
        createPreview(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.preview = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        
        clearFile() {
            this.preview = null;
            this.fileName = null;
            this.fileSize = null;
            this.error = null;
            this.isHeic = false;
            this.uploadSuccess = false;
            this.progress = 0;
            this.$refs.fileInput.value = '';
            
            // Déclencher un événement pour Livewire
            this.$refs.fileInput.dispatchEvent(new Event('change'));
        },
        
        // Gestion des événements Livewire d'upload
        onUploadStart() {
            this.uploading = true;
            this.uploadSuccess = false;
            this.progress = 0;
        },
        onUploadProgress(e) {
            if (e && e.detail && typeof e.detail.progress === 'number') {
                this.progress = e.detail.progress;
            }
        },
        onUploadError() {
            this.uploading = false;
        },
        onUploadFinish() {
            this.uploading = false;
            this.uploadSuccess = true;
            this.progress = 100;
        },
        
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