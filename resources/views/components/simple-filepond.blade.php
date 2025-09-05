@props(['wireModel' => 'photo'])

<div>
    <!-- FilePond CSS -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    
    <!-- Input file direct pour Livewire - comme celui qui marchait -->
    <input 
        type="file" 
        wire:model="{{ $wireModel }}"
        accept=".jpg,.jpeg,.png"
        id="filepond-input"
        onchange="console.log('📁 Fichier sélectionné directement:', this.files[0]?.name)"
    >
    
    <!-- Instructions -->
    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-2">
            <div class="text-blue-600 text-lg">📁</div>
            <div class="text-sm text-blue-800">
                <p><strong>Sélectionnez une photo depuis votre galerie :</strong> JPG ou PNG uniquement (Max 5MB)</p>
                <p class="mt-1"><strong>iPhone :</strong> Changez vos réglages pour photos JPG</p>
            </div>
        </div>
    </div>
</div>

<!-- FilePond JS -->
<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Attendre que l'input soit dans le DOM
    const input = document.getElementById('filepond-input');
    if (input) {
        // Transformer l'input en FilePond après que Livewire l'ait initialisé
        setTimeout(() => {
            const pond = FilePond.create(input, {
                acceptedFileTypes: ['image/jpeg', 'image/jpg', 'image/png'],
                maxFileSize: '5MB',
                labelIdle: `Glissez votre photo ici ou <span class="filepond--label-action">Parcourir</span>`,
                labelFileTypeNotAllowed: 'Fichier de type non valide',
                fileValidateTypeLabelExpectedTypes: 'Formats acceptés : JPG, PNG',
                credits: false,
                allowMultiple: false,
                allowDrop: true,
                allowBrowse: true,
                allowPaste: false,
                allowReplace: true,
                allowRevert: false,
                allowProcess: false,
                allowRemove: true,
                instantUpload: false,
            });
            
            console.log('📁 FilePond initialisé sur input Livewire direct');
        }, 100);
    }
});
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