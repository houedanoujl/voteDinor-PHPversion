@props(['wireModel' => 'photo', 'maxSize' => 5, 'required' => true])

<div x-data="robustMobileUpload()" class="space-y-4" x-init="init()">
    <!-- Méthode 1: Drag & Drop moderne avec preview -->
    <div x-show="method === 'modern'" x-transition>
        <div class="relative group">
            <input 
                type="file" 
                x-ref="modernInput"
                wire:model="{{ $wireModel }}"
                accept="image/*,.heic,.heif,.webp"
                capture="environment"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                @change="handleModernUpload($event)"
            >
            
            <div 
                class="border-2 border-dashed rounded-xl p-6 text-center transition-all"
                :class="preview ? 'border-green-400 bg-green-50' : 'border-gray-300 group-hover:border-orange-400 group-hover:bg-orange-50'"
            >
                <!-- Preview existant -->
                <div x-show="preview" class="space-y-4">
                    <img :src="preview" class="mx-auto max-h-48 rounded-lg shadow-lg">
                    <div class="text-sm text-green-700">
                        <p class="font-medium" x-text="fileName"></p>
                        <p x-text="fileSize"></p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="clearPhoto()" 
                                class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm hover:bg-red-200">
                            🗑️ Supprimer
                        </button>
                        <button type="button" @click="$refs.modernInput.click()" 
                                class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg text-sm hover:bg-orange-200">
                            🔄 Changer
                        </button>
                    </div>
                </div>
                
                <!-- Interface upload -->
                <div x-show="!preview" class="space-y-3">
                    <div class="text-4xl">📸</div>
                    <div>
                        <p class="text-lg font-medium text-gray-700">
                            Ajoutez votre photo
                        </p>
                        <p class="text-sm text-gray-500">
                            Cliquez ou glissez une image ici
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Méthode 2: Upload simple (fallback) -->
    <div x-show="method === 'simple'" x-transition>
        <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-6">
            <div class="text-center space-y-4">
                <div class="text-3xl">📁</div>
                <div>
                    <p class="font-medium text-gray-700 mb-2">Mode compatibilité activé</p>
                    <input 
                        type="file" 
                        x-ref="simpleInput"
                        wire:model="{{ $wireModel }}"
                        accept="image/*"
                        capture="environment"
                        class="w-full p-3 border border-gray-300 rounded-lg bg-white"
                        @change="handleSimpleUpload($event)"
                    >
                </div>
                
                <!-- Preview simple -->
                <div x-show="preview" class="space-y-3">
                    <img :src="preview" class="mx-auto max-h-32 rounded border">
                    <p class="text-sm text-gray-600" x-text="fileName"></p>
                    <button type="button" @click="clearPhoto()" 
                            class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Méthode 3: Caméra directe -->
    <div x-show="method === 'camera'" x-transition>
        <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
            <div class="text-center space-y-4">
                <div class="text-4xl">📷</div>
                <p class="font-medium text-blue-700">Prendre une photo directement</p>
                
                <!-- Video preview pour caméra -->
                <video x-ref="video" class="mx-auto max-w-full h-48 bg-black rounded-lg hidden"></video>
                <canvas x-ref="canvas" class="hidden"></canvas>
                
                <!-- Photo capturée -->
                <div x-show="capturedPhoto" class="space-y-3">
                    <img :src="capturedPhoto" class="mx-auto max-h-48 rounded-lg">
                    <div class="flex gap-2 justify-center">
                        <button type="button" @click="retakePhoto()" 
                                class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm">
                            🔄 Reprendre
                        </button>
                        <button type="button" @click="acceptPhoto()" 
                                class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm">
                            ✅ Valider
                        </button>
                    </div>
                </div>
                
                <!-- Contrôles caméra -->
                <div x-show="!capturedPhoto" class="space-y-2">
                    <button type="button" @click="startCamera()" 
                            class="w-full py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        📷 Activer la caméra
                    </button>
                    <button type="button" @click="capturePhoto()" x-show="cameraActive"
                            class="w-full py-3 bg-green-500 text-white rounded-lg hover:bg-green-600">
                        📸 Prendre la photo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sélecteur de méthode -->
    <div class="flex gap-2 justify-center">
        <button type="button" @click="setMethod('modern')" 
                :class="method === 'modern' ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-700'"
                class="px-3 py-2 rounded-lg text-sm transition-colors">
            📁 Fichier
        </button>
        <button type="button" @click="setMethod('simple')" 
                :class="method === 'simple' ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-700'"
                class="px-3 py-2 rounded-lg text-sm transition-colors">
            📱 Simple
        </button>
        <button type="button" @click="setMethod('camera')" 
                :class="method === 'camera' ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-700'"
                class="px-3 py-2 rounded-lg text-sm transition-colors">
            📷 Caméra
        </button>
    </div>

    <!-- Messages d'erreur -->
    <div x-show="error" x-transition class="p-3 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-sm text-red-800" x-text="error"></p>
    </div>

    <!-- Instructions contextuelles -->
    <div class="text-xs text-gray-500 bg-gray-50 p-3 rounded-lg">
        <div x-show="method === 'modern'">
            <p><strong>📁 Mode fichier:</strong> Glissez-déposez ou cliquez pour sélectionner depuis votre galerie/appareil photo.</p>
        </div>
        <div x-show="method === 'simple'">
            <p><strong>📱 Mode simple:</strong> Compatible avec tous les appareils. Sélectionnez un fichier image.</p>
        </div>
        <div x-show="method === 'camera'">
            <p><strong>📷 Mode caméra:</strong> Prenez une photo directement avec votre caméra (nécessite autorisation).</p>
        </div>
        <p class="mt-1"><strong>Formats:</strong> JPG, PNG, WebP, HEIC • <strong>Taille max:</strong> {{ $maxSize }}MB</p>
    </div>
</div>

<script>
function robustMobileUpload() {
    return {
        method: 'modern',
        preview: null,
        fileName: null,
        fileSize: null,
        error: null,
        cameraActive: false,
        capturedPhoto: null,
        stream: null,
        
        init() {
            // Auto-détection du meilleur mode selon l'appareil
            this.detectBestMethod();
        },
        
        detectBestMethod() {
            const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
            
            if (isIOS) {
                // iOS a des problèmes avec drag&drop, utiliser simple par défaut
                this.method = 'simple';
            } else if (isMobile) {
                // Android mobile, modern devrait fonctionner
                this.method = 'modern';
            } else {
                // Desktop, utiliser modern
                this.method = 'modern';
            }
        },
        
        setMethod(newMethod) {
            this.method = newMethod;
            this.clearPhoto();
            if (newMethod !== 'camera') {
                this.stopCamera();
            }
        },
        
        handleModernUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.processFile(file);
            }
        },
        
        handleSimpleUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.processFile(file);
            }
        },
        
        processFile(file) {
            this.error = null;
            
            // Validation taille
            const maxBytes = {{ $maxSize }} * 1024 * 1024;
            if (file.size > maxBytes) {
                this.error = `Fichier trop volumineux (${this.formatBytes(file.size)}). Taille maximum: {{ $maxSize }}MB`;
                return;
            }
            
            // Validation type
            const allowedTypes = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 
                'image/webp', 'image/heic', 'image/heif'
            ];
            
            if (!allowedTypes.some(type => type.toLowerCase() === file.type.toLowerCase())) {
                this.error = 'Format non supporté. Utilisez JPG, PNG, WebP ou HEIC.';
                return;
            }
            
            this.fileName = file.name;
            this.fileSize = this.formatBytes(file.size);
            
            // Créer preview (sauf HEIC qui ne peut pas être prévisualisé)
            if (!['image/heic', 'image/heif'].includes(file.type.toLowerCase())) {
                this.createPreview(file);
            } else {
                this.preview = null; // HEIC ne peut pas être prévisualisé dans le navigateur
            }
        },
        
        createPreview(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.preview = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        
        clearPhoto() {
            this.preview = null;
            this.fileName = null;
            this.fileSize = null;
            this.error = null;
            this.capturedPhoto = null;
            
            // Vider les inputs
            if (this.$refs.modernInput) this.$refs.modernInput.value = '';
            if (this.$refs.simpleInput) this.$refs.simpleInput.value = '';
        },
        
        // Fonctions caméra
        async startCamera() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'environment', // Caméra arrière par défaut
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    } 
                });
                
                this.$refs.video.srcObject = this.stream;
                this.$refs.video.classList.remove('hidden');
                this.$refs.video.play();
                this.cameraActive = true;
                this.error = null;
            } catch (err) {
                this.error = 'Impossible d\'accéder à la caméra. Vérifiez les autorisations.';
                console.error('Erreur caméra:', err);
            }
        },
        
        capturePhoto() {
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            const ctx = canvas.getContext('2d');
            
            // Définir la taille du canvas
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            // Capturer l'image
            ctx.drawImage(video, 0, 0);
            
            // Convertir en image
            this.capturedPhoto = canvas.toDataURL('image/jpeg', 0.8);
        },
        
        retakePhoto() {
            this.capturedPhoto = null;
        },
        
        acceptPhoto() {
            // Convertir dataURL en File
            this.dataURLToFile(this.capturedPhoto, 'camera-photo.jpg')
                .then(file => {
                    this.processFile(file);
                    this.stopCamera();
                });
        },
        
        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
            if (this.$refs.video) {
                this.$refs.video.classList.add('hidden');
            }
            this.cameraActive = false;
            this.capturedPhoto = null;
        },
        
        // Utilitaires
        formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        dataURLToFile(dataURL, filename) {
            return fetch(dataURL)
                .then(res => res.blob())
                .then(blob => new File([blob], filename, { type: blob.type }));
        }
    }
}
</script>