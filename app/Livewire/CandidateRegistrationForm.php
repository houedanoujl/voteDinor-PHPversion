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
use App\Models\SiteSetting;

class CandidateRegistrationForm extends Component
{
    use WithFileUploads;

    public $prenom = '';
    public $nom = '';
    public $email = '';
    public $whatsapp = '';
    public $photo = null;
    public $tempPhotoUrl = null;
    public $isSubmitting = false;

    protected $rules = [
        'prenom' => 'required|min:2|max:255',
        'nom' => 'required|min:2|max:255',
        'whatsapp' => 'required|regex:/^\+225[0-9]{10}$/|unique:candidates,whatsapp',
        'photo' => 'nullable|image|max:3072',
    ];

    protected $messages = [
        'prenom.required' => 'Le prénom est obligatoire.',
        'nom.required' => 'Le nom est obligatoire.',
        'whatsapp.required' => 'Le numéro WhatsApp est obligatoire.',
        'whatsapp.regex' => 'Format requis: +225 suivi de 10 chiffres',
        'whatsapp.unique' => 'Ce numéro WhatsApp est déjà utilisé.',
        // 'photo.required' => 'Une photo est obligatoire.',
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
            // Check if applications are open
            $settings = SiteSetting::first();
            if ($settings && !$settings->applications_open) {
                $this->isSubmitting = false;
                session()->flash('error', 'Les candidatures sont actuellement fermées.');
                return;
            }
            $this->validate();

            // Si un utilisateur est connecté, interdire la création si une candidature existe déjà
            if (auth()->check() && auth()->user()->candidate) {
                $this->isSubmitting = false;
                session()->flash('error', '❌ Vous avez déjà soumis une photo. Une seule participation est autorisée.');
                return;
            }

            // Déjà au format +225XXXXXXXXXX
            $whatsappWithPrefix = $this->whatsapp;

            // Réutiliser un utilisateur existant par WhatsApp ou en créer un
            $existingUser = User::where('whatsapp', $whatsappWithPrefix)->first();

            if ($existingUser) {
                $user = $existingUser;
                // S'assurer que le type devient candidate
                if ($user->type !== 'candidate') {
                    $user->update(['type' => 'candidate']);
                }
                // Mettre à jour le nom si vide
                if (empty($user->prenom) || empty($user->nom)) {
                    $user->update([
                        'prenom' => $this->prenom,
                        'nom' => $this->nom,
                        'name' => $this->prenom . ' ' . $this->nom,
                    ]);
                }
                // Connecter l'utilisateur existant
                Auth::login($user);
            } else {
                $user = User::create([
                    'name' => $this->prenom . ' ' . $this->nom,
                    'prenom' => $this->prenom,
                    'nom' => $this->nom,
                    'email' => (string) Str::uuid().'@dinor.local',
                    'whatsapp' => $whatsappWithPrefix,
                    'password' => Hash::make(Str::random(12)), // Mot de passe temporaire
                    'email_verified_at' => now(),
                    'type' => 'candidate',
                    'role' => 'user',
                ]);
                // Connecter automatiquement le nouvel utilisateur
                Auth::login($user);
            }

            // Stocker la photo si l'upload est activé
            $photoPath = null;
            $uploadsEnabled = $settings?->uploads_enabled ?? true;
            if ($uploadsEnabled) {
                if ($this->photo) {
                    $photoPath = $this->photo->store('candidates', 'public');
                }
            }

            // whatsapp déjà défini

            // Empêcher de créer une deuxième candidature pour le même utilisateur
            if ($user->candidate) {
                $this->isSubmitting = false;
                session()->flash('error', '❌ Vous avez déjà soumis une photo. Une seule participation est autorisée.');
                return;
            }

            // Créer le candidat
            $candidate = Candidate::create([
                'user_id' => $user->id,
                'prenom' => $this->prenom,
                'nom' => $this->nom,
                'email' => null,
                'whatsapp' => $whatsappWithPrefix,
                'photo_url' => $photoPath,
                'status' => 'pending',
            ]);

            // Envoyer un message WhatsApp de confirmation
            try {
                $whatsappService = new WhatsAppService();
                $message = "🎉 Félicitations {$this->prenom} ! Votre inscription au concours photo DINOR a été reçue. Votre candidature est en cours de validation. Vous recevrez une notification dès l'approbation.";
                $whatsappService->sendMessage($whatsappWithPrefix, $message);

                // Notifier l'admin
                $adminPhone = config('services.whatsapp.admin_phone');
                if (!empty($adminPhone)) {
                    $adminMessage = "🔔 Nouvelle inscription CANDIDAT\n\n" .
                        "Nom: {$this->prenom} {$this->nom}\n" .
                        "WhatsApp: {$whatsappWithPrefix}\n" .
                        "ID utilisateur: {$user->id}\n" .
                        "ID candidat: {$candidate->id}\n" .
                        "Statut: pending\n\n" .
                        "Filament: " . url('/admin') . "\n" .
                        "Valider depuis le panneau admin.";
                    $whatsappService->sendMessage($adminPhone, $adminMessage);
                }
            } catch (\Exception $e) {
                Log::error('Erreur WhatsApp inscription: ' . $e->getMessage());
            }

            // Réinitialiser le formulaire
            $this->resetForm();

            // Message de succès
            session()->flash('success', 'Votre inscription a été envoyée avec succès ! ' .
                ($existingUser ? 'Votre numéro était déjà enregistré, nous avons connecté votre compte. ' : '') .
                'Vous recevrez une notification WhatsApp dès validation.');

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
        $this->photo = null;
        $this->tempPhotoUrl = null;
        $this->isSubmitting = false;
    }

    public function render()
    {
        return view('livewire.candidate-registration-form');
    }
}
