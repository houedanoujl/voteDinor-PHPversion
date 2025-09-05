@props(['wireModel' => 'photo'])

<div x-data="filepond()">
    <!-- FilePond CSS -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    
    <!-- Input file pour Livewire (pas de capture=camera) -->
    <input 
        type="file" 
        x-ref="hiddenInput"
        wire:model="{{ $wireModel }}"
        accept=".jpg,.jpeg,.png"
        style="display: none;"
    >
    
    <!-- FilePond input -->
    <input 
        type="file" 
        x-ref="filepondInput"
        accept=".jpg,.jpeg,.png"
        data-max-file-size="5MB"
    >
    
    <!-- Instructions -->
    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-2">
            <div class="text-blue-600 text-lg">📁</div>
            <div class="text-sm text-blue-800">
                <p><strong>Sélectionnez une photo depuis votre galerie :</strong> JPG ou PNG uniquement (Max 5MB)</p>
                <p class="mt-1"><strong>iPhone :</strong> Changez vos réglages pour photos JPG (Réglages → Appareil photo → Formats → Le plus compatible)</p>
            </div>
        </div>
    </div>
</div>

<!-- FilePond JS -->
<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>

<script>
function filepond() {
    return {
        pond: null,
        
        init() {
            // Créer l'instance FilePond
            this.pond = FilePond.create(this.$refs.filepondInput, {
                acceptedFileTypes: ['image/jpeg', 'image/jpg', 'image/png'],
                maxFileSize: '5MB',
                labelIdle: `Glissez votre photo ici ou <span class="filepond--label-action">Parcourir</span>`,
                labelInvalidField: 'Le fichier contient des données invalides',
                labelFileWaitingForSize: 'En attente de taille',
                labelFileSizeNotAvailable: 'Taille non disponible',
                labelFileLoading: 'Chargement',
                labelFileLoadError: 'Erreur lors du chargement',
                labelFileProcessing: 'Traitement',
                labelFileProcessingComplete: 'Traitement terminé',
                labelFileProcessingAborted: 'Traitement annulé',
                labelFileProcessingError: 'Erreur lors du traitement',
                labelFileProcessingRevertError: 'Erreur lors de l\'annulation',
                labelFileRemoveError: 'Erreur lors de la suppression',
                labelTapToCancel: 'appuyer pour annuler',
                labelTapToRetry: 'appuyer pour réessayer',
                labelTapToUndo: 'appuyer pour annuler',
                labelButtonRemoveItem: 'Supprimer',
                labelButtonAbortItemLoad: 'Annuler',
                labelButtonRetryItemLoad: 'Réessayer',
                labelButtonAbortItemProcessing: 'Annuler',
                labelButtonUndoItemProcessing: 'Annuler',
                labelButtonRetryItemProcessing: 'Réessayer',
                labelButtonProcessItem: 'Traiter',
                labelMaxFileSizeExceeded: 'Le fichier est trop volumineux',
                labelMaxFileSize: 'La taille maximale est de {filesize}',
                labelMaxTotalFileSizeExceeded: 'Taille totale maximale dépassée',
                labelMaxTotalFileSize: 'La taille totale maximale est de {filesize}',
                labelFileTypeNotAllowed: 'Fichier de type non valide',
                fileValidateTypeLabelExpectedTypes: 'Formats acceptés : JPG, PNG',
                credits: false,
                allowMultiple: false,
                allowDrop: false, // Pas de drag & drop, juste parcourir
                allowBrowse: true,
                allowPaste: false,
                allowReplace: true,
                allowRevert: false,
                allowProcess: false,
                allowRemove: true,
                instantUpload: false,
                
                onaddfile: (error, file) => {
                    if (error) {
                        console.error('FilePond erreur:', error);
                        return;
                    }
                    
                    console.log('📁 Fichier ajouté à FilePond:', file.filename);
                    console.log('📁 Objet File:', file.file);
                    
                    // Transférer le fichier vers l'input Livewire avec plusieurs tentatives
                    try {
                        const dt = new DataTransfer();
                        dt.items.add(file.file);
                        this.$refs.hiddenInput.files = dt.files;
                        
                        console.log('📁 Fichier transféré à input hidden:', this.$refs.hiddenInput.files[0]);
                        
                        // Déclencher plusieurs événements pour s'assurer que Livewire détecte
                        this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                        this.$refs.hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                        
                        // Force Livewire à traiter immédiatement
                        this.$nextTick(() => {
                            if (window.Livewire) {
                                console.log('📁 Forçage mise à jour Livewire');
                            }
                        });
                        
                    } catch (e) {
                        console.error('Erreur transfert fichier:', e);
                        
                        // Fallback: créer un nouvel input avec le fichier
                        const newInput = document.createElement('input');
                        newInput.type = 'file';
                        newInput.style.display = 'none';
                        const dt2 = new DataTransfer();
                        dt2.items.add(file.file);
                        newInput.files = dt2.files;
                        
                        // Remplacer l'ancien input
                        this.$refs.hiddenInput.parentNode.replaceChild(newInput, this.$refs.hiddenInput);
                        this.$refs.hiddenInput = newInput;
                        
                        // Ajouter wire:model
                        newInput.setAttribute('wire:model', '{{ $wireModel }}');
                        newInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                },
                
                onremovefile: () => {
                    console.log('📁 Fichier supprimé de FilePond');
                    
                    // Vider l'input Livewire
                    this.$refs.hiddenInput.value = '';
                    this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        },
        
        destroy() {
            if (this.pond) {
                this.pond.destroy();
            }
        }
    }
}
</script>

<style>
/* Customisation FilePond */
.filepond--root {
    margin: 0;
}

.filepond--drop-label {
    color: #374151;
    font-size: 16px;
}

.filepond--label-action {
    color: #f97316;
    text-decoration: underline;
}

.filepond--panel-root {
    background-color: #f9fafb;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
}

.filepond--panel-root:hover {
    border-color: #f97316;
    background-color: #fff7ed;
}

.filepond--item-panel {
    background-color: #f3f4f6;
    border-radius: 6px;
}

.filepond--file-action-button {
    color: #ef4444;
}
</style>