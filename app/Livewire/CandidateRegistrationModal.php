<?php

namespace App\Livewire;

use App\Models\Candidate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class CandidateRegistrationModal extends Component
{
    use WithFileUploads;
    
    public $showModal = false;
    public $prenom = '';
    public $nom = '';
    public $whatsapp = '';
    public $description = '';
    public $photo = null;
    public $tempPhotoUrl = null;
    
    protected $rules = [
        'prenom' => 'required|min:2|max:255',
        'nom' => 'required|min:2|max:255', 
        'whatsapp' => 'required|regex:/^\+225[0-9]{8}$/',
        'description' => 'nullable|max:500',
        'photo' => 'required|image|max:2048',
    ];
    
    protected $messages = [
        'prenom.required' => 'Le prénom est obligatoire.',
        'nom.required' => 'Le nom est obligatoire.',
        'whatsapp.required' => 'Le numéro WhatsApp est obligatoire.',
        'whatsapp.regex' => 'Format: +225XXXXXXXX',
        'photo.required' => 'Une photo est obligatoire.',
        'photo.image' => 'Le fichier doit être une image.',
        'photo.max' => 'La photo ne doit pas dépasser 2MB.',
    ];

    public function openModal()
    {
        \Log::info('openModal called in CandidateRegistrationModal');
        $this->showModal = true;
        $this->dispatch('modal-opened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['prenom', 'nom', 'whatsapp', 'description', 'photo', 'tempPhotoUrl']);
        $this->resetErrorBag();
    }

    public function updatedPhoto()
    {
        $this->validate(['photo' => 'image|max:2048']);
        
        if ($this->photo) {
            $this->tempPhotoUrl = $this->photo->temporaryUrl();
        }
    }

    public function submit()
    {
        $this->validate();

        try {
            $candidate = Candidate::create([
                'prenom' => $this->prenom,
                'nom' => $this->nom,
                'whatsapp' => $this->whatsapp,
                'description' => $this->description,
                'status' => 'pending',
                'votes_count' => 0,
                'user_id' => auth()->id(),
            ]);

            // Ajouter la photo avec Spatie Media Library
            if ($this->photo) {
                $candidate->addMediaFromString(file_get_contents($this->photo->getRealPath()))
                    ->usingName($this->prenom . '_' . $this->nom)
                    ->usingFileName(Str::uuid() . '.' . $this->photo->getClientOriginalExtension())
                    ->toMediaCollection('photos');
            }

            session()->flash('success', '✅ Inscription réussie ! Votre candidature sera validée sous 24h.');
            
            // Tracker l'inscription avec Google Analytics
            $this->dispatch('track-registration', candidateName: $this->prenom . ' ' . $this->nom);
            
            $this->closeModal();
            
            // Rafraîchir la galerie si présente
            $this->dispatch('candidateRegistered');
            
        } catch (\Exception $e) {
            session()->flash('error', '❌ Erreur lors de l\'inscription. Veuillez réessayer.');
        }
    }

    public function render()
    {
        return view('livewire.candidate-registration-modal');
    }
}
