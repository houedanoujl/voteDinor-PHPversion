<?php

namespace App\Livewire;

use App\Models\Candidate;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CandidateRegistrationModal extends Component
{
    use WithFileUploads;
    
    public $showModal = false;
    public $prenom = '';
    public $nom = '';
    public $email = '';
    public $whatsapp = '';
    public $description = '';
    public $photo = null;
    public $tempPhotoUrl = null;
    
    protected $rules = [
        'prenom' => 'required|min:2|max:255',
        'nom' => 'required|min:2|max:255',
        'email' => 'required|email|unique:users,email',
        'whatsapp' => 'required|regex:/^\+225[0-9]{8}$/',
        'description' => 'nullable|max:500',
        'photo' => 'required|image|max:2048',
    ];
    
    protected $messages = [
        'prenom.required' => 'Le prénom est obligatoire.',
        'nom.required' => 'Le nom est obligatoire.',
        'email.required' => 'L\'email est obligatoire.',
        'email.email' => 'L\'email doit être valide.',
        'email.unique' => 'Cet email est déjà utilisé.',
        'whatsapp.required' => 'Le numéro WhatsApp est obligatoire.',
        'whatsapp.regex' => 'Format: +225XXXXXXXX',
        'photo.required' => 'Une photo est obligatoire.',
        'photo.image' => 'Le fichier doit être une image.',
        'photo.max' => 'La photo ne doit pas dépasser 2MB.',
    ];

    public function openModal()
    {
        \Log::info('openModal called in CandidateRegistrationModal - Success!');
        $this->showModal = true;
        $this->dispatch('modal-opened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['prenom', 'nom', 'email', 'whatsapp', 'description', 'photo', 'tempPhotoUrl']);
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
        // Validation conditionnelle selon le statut de connexion
        $rules = [
            'prenom' => 'required|min:2|max:255',
            'nom' => 'required|min:2|max:255',
            'whatsapp' => 'required|regex:/^\+225[0-9]{8}$/',
            'description' => 'nullable|max:500',
            'photo' => 'required|image|max:2048',
        ];

        // Ajouter la validation email seulement si l'utilisateur n'est pas connecté
        if (!auth()->check()) {
            $rules['email'] = 'required|email|unique:users,email';
        }

        $this->validate($rules);

        try {
            // Créer l'utilisateur s'il n'est pas déjà connecté
            $user = null;
            if (!auth()->check()) {
                // Générer un mot de passe temporaire
                $password = Str::random(12);
                
                $user = User::create([
                    'name' => $this->prenom . ' ' . $this->nom,
                    'email' => $this->email,
                    'password' => Hash::make($password),
                    'email_verified_at' => now(), // Auto-vérifier l'email
                ]);

                // Connecter l'utilisateur automatiquement
                Auth::login($user);
            } else {
                $user = auth()->user();
            }

            $candidate = Candidate::create([
                'prenom' => $this->prenom,
                'nom' => $this->nom,
                'whatsapp' => $this->whatsapp,
                'description' => $this->description,
                'status' => 'pending',
                'votes_count' => 0,
                'user_id' => $user->id,
            ]);

            // Ajouter la photo avec Spatie Media Library
            if ($this->photo) {
                $candidate->addMediaFromString(file_get_contents($this->photo->getRealPath()))
                    ->usingName($this->prenom . '_' . $this->nom)
                    ->usingFileName(Str::uuid() . '.' . $this->photo->getClientOriginalExtension())
                    ->toMediaCollection('photos');
            }

            session()->flash('success', '✅ Inscription réussie ! ' . 
                (!auth()->guest() ? 'Votre candidature sera validée sous 24h.' : 
                'Un compte a été créé et vous êtes maintenant connecté. Votre candidature sera validée sous 24h.'));
            
            // Tracker l'inscription avec Google Analytics
            $this->dispatch('track-registration', candidateName: $this->prenom . ' ' . $this->nom);
            
            $this->closeModal();
            
            // Rafraîchir la page pour afficher le nouveau statut de connexion
            $this->dispatch('userRegistered');
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'inscription: ' . $e->getMessage());
            session()->flash('error', '❌ Erreur lors de l\'inscription. Veuillez réessayer.');
        }
    }

    public function render()
    {
        return view('livewire.candidate-registration-modal');
    }
}
