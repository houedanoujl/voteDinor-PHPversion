<?php

namespace App\Livewire;

use App\Models\Candidate;
use App\Models\User;
use App\Services\WhatsAppService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CandidateRegistrationForm extends Component
{
    use WithFileUploads;

    public $prenom = '';
    public $nom = '';
    public $email = '';
    public $whatsapp = '';
    public $description = '';
    public $photo = null;
    public $tempPhotoUrl = null;
    public $isSubmitting = false;

    protected $rules = [
        'prenom' => 'required|min:2|max:255',
        'nom' => 'required|min:2|max:255',
        'email' => 'required|email|unique:users,email',
        'whatsapp' => 'required|regex:/^\+225[0-9]{8}$/|unique:candidates,whatsapp',
        'description' => 'nullable|string|max:500',
        'photo' => 'required|image|max:3072',
    ];

    protected $messages = [
        'prenom.required' => 'Le prénom est obligatoire.',
        'nom.required' => 'Le nom est obligatoire.',
        'email.required' => 'L\'email est obligatoire.',
        'email.email' => 'L\'email doit être valide.',
        'email.unique' => 'Cet email est déjà utilisé.',
        'whatsapp.required' => 'Le numéro WhatsApp est obligatoire.',
        'whatsapp.regex' => 'Format: +225XXXXXXXX',
        'whatsapp.unique' => 'Ce numéro WhatsApp est déjà utilisé.',
        'description.max' => 'La description ne doit pas dépasser 500 caractères.',
        'photo.required' => 'Une photo est obligatoire.',
        'photo.image' => 'Le fichier doit être une image.',
        'photo.max' => 'La photo ne doit pas dépasser 3MB.',
    ];

    public function updatedPhoto()
    {
        if ($this->photo) {
            $this->tempPhotoUrl = $this->photo->temporaryUrl();
        }
    }

    public function submit()
    {
        $this->isSubmitting = true;

        try {
            $this->validate();

            // Créer un utilisateur
            $user = User::create([
                'name' => $this->prenom . ' ' . $this->nom,
                'email' => $this->email,
                'password' => Hash::make(Str::random(12)), // Mot de passe temporaire
                'email_verified_at' => now(),
            ]);

            // Stocker la photo
            $photoPath = $this->photo->store('candidates', 'public');

            // Créer le candidat
            $candidate = Candidate::create([
                'user_id' => $user->id,
                'prenom' => $this->prenom,
                'nom' => $this->nom,
                'email' => $this->email,
                'whatsapp' => $this->whatsapp,
                'description' => $this->description,
                'photo' => $photoPath,
                'status' => 'pending',
            ]);

            // Connecter automatiquement l'utilisateur
            Auth::login($user);

            // Envoyer un message WhatsApp de confirmation
            try {
                $whatsappService = new WhatsAppService();
                $message = "🎉 Félicitations {$this->prenom} ! Votre inscription au concours photo DINOR a été reçue. Votre candidature est en cours de validation. Vous recevrez une notification dès l'approbation.";
                $whatsappService->sendMessage($this->whatsapp, $message);
            } catch (\Exception $e) {
                Log::error('Erreur WhatsApp inscription: ' . $e->getMessage());
            }

            // Réinitialiser le formulaire
            $this->resetForm();

            // Message de succès
            session()->flash('success', 'Votre inscription a été envoyée avec succès ! Vous recevrez une notification WhatsApp dès validation.');

            // Redirection vers le tableau de bord
            return redirect()->route('dashboard');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->isSubmitting = false;
            throw $e;
        } catch (\Exception $e) {
            $this->isSubmitting = false;
            Log::error('Erreur lors de l\'inscription: ' . $e->getMessage());
            session()->flash('error', 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.');
        }
    }

    private function resetForm()
    {
        $this->prenom = '';
        $this->nom = '';
        $this->email = '';
        $this->whatsapp = '';
        $this->description = '';
        $this->photo = null;
        $this->tempPhotoUrl = null;
        $this->isSubmitting = false;
    }

    public function render()
    {
        return view('livewire.candidate-registration-form');
    }
}