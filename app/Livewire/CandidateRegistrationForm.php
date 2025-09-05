<?php

namespace App\Livewire;

use App\Models\Candidate;
use App\Models\User;
use App\Services\WhatsAppService;
use App\Services\ImageOptimizationService;
use App\Events\CandidatePhotoUploaded;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
        // JPG et PNG uniquement, taille 5MB
        'photo' => 'required|mimes:jpeg,jpg,png|max:5120',
    ];

    protected $messages = [
        'prenom.required' => 'Le prénom est obligatoire.',
        'nom.required' => 'Le nom est obligatoire.',
        'whatsapp.required' => 'Le numéro WhatsApp est obligatoire.',
        'whatsapp.regex' => 'Format requis: +225 suivi de 10 chiffres',
        'whatsapp.unique' => 'Ce numéro WhatsApp est déjà utilisé.',
        'photo.required' => 'Une photo est obligatoire.',
        'photo.mimes' => 'Formats acceptés: JPG, PNG uniquement.',
        'photo.max' => 'La photo ne doit pas dépasser 5MB.',
    ];

    public function updatedPhoto()
    {
        Log::info('📱 PHOTO UPDATED - Debug Mobile', [
            'photo_present' => $this->photo !== null,
            'photo_class' => $this->photo ? get_class($this->photo) : 'null',
            'user_agent' => request()->header('User-Agent'),
        ]);
        
        if ($this->photo) {
            Log::info('📱 DÉTAILS PHOTO MOBILE', [
                'filename' => $this->photo->getClientOriginalName(),
                'size' => $this->photo->getSize(),
                'mime' => $this->photo->getMimeType(),
                'extension' => $this->photo->getClientOriginalExtension(),
                'is_valid' => $this->photo->isValid(),
                'error' => $this->photo->getError(),
                'path' => $this->photo->getRealPath(),
            ]);
            
            try {
                // Validation immédiate pour voir si le fichier est accepté
                $this->validate([
                    'photo' => 'file|mimes:jpeg,jpg,png|max:5120'
                ]);
                
                Log::info('✅ Photo mobile validée avec succès');
                
                // Générer preview
                $ext = strtolower($this->photo->getClientOriginalExtension() ?? pathinfo($this->photo->getClientOriginalName(), PATHINFO_EXTENSION));
                $previewable = in_array($ext, config('livewire.temporary_file_upload.preview_mimes', []));
                if ($previewable) {
                    $this->tempPhotoUrl = $this->photo->temporaryUrl();
                } else {
                    $this->tempPhotoUrl = null;
                }
                
            } catch (\Throwable $e) {
                Log::error('❌ Erreur validation photo mobile', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $this->tempPhotoUrl = null;
            }
        } else {
            Log::warning('⚠️  Photo est null après update');
        }
    }
    
    protected $listeners = ['photoSelected'];
    
    public function photoSelected($filename)
    {
        Log::info('🎯 Photo sélectionnée via JavaScript', [
            'filename' => $filename,
            'current_photo_state' => $this->photo !== null ? 'present' : 'null'
        ]);
    }

    public function submit()
    {
        $this->isSubmitting = true;

        try {
            Log::info('Début de soumission candidature mobile', [
                'prenom' => $this->prenom,
                'nom' => $this->nom,
                'whatsapp' => $this->whatsapp,
                'photo_present' => $this->photo !== null,
                'photo_type' => $this->photo ? get_class($this->photo) : 'null',
                'user_agent' => request()->header('User-Agent'),
            ]);

            // Debug de la photo uploadée
            if ($this->photo) {
                Log::info('Photo upload details', [
                    'filename' => $this->photo->getClientOriginalName(),
                    'size' => $this->photo->getSize(),
                    'mime' => $this->photo->getMimeType(),
                    'extension' => $this->photo->getClientOriginalExtension(),
                    'is_valid' => $this->photo->isValid(),
                    'path' => $this->photo->getRealPath(),
                ]);
            } else {
                Log::warning('Aucune photo détectée lors de la soumission mobile');
            }

            // Check if applications are open
            $settings = SiteSetting::first();
            if ($settings && !$settings->applications_open) {
                $this->isSubmitting = false;
                session()->flash('error', 'Les candidatures sont actuellement fermées.');
                return;
            }
            
            // Validation dynamique: photo obligatoire si les uploads sont activés
            $dynamicRules = $this->rules;
            $uploadsEnabled = $settings?->uploads_enabled ?? true;
            $photoRule = 'file|mimes:jpeg,jpg,png|max:5120'; // 5MB, JPG/PNG seulement
            
            if ($uploadsEnabled === false) {
                $dynamicRules['photo'] = 'nullable|' . $photoRule;
            } else {
                // Validation stricte JPG/PNG seulement
                $dynamicRules['photo'] = 'required|' . $photoRule;
            }
            
            Log::info('Règles de validation mobile', [
                'rules' => $dynamicRules,
                'uploads_enabled' => $uploadsEnabled
            ]);
            
            $this->validate($dynamicRules);

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
            $photoUrl = null;
            $uploadsEnabled = $settings?->uploads_enabled ?? true;
            if ($uploadsEnabled && $this->photo) {
                // Stocker l'image originale
                $photoPath = $this->photo->store('candidates', 'public');
                $photoUrl = $photoPath ? asset('storage/' . $photoPath) : null;
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
                'status' => 'pending',
                'photo_url' => '',
            ]);

            // Garantir photo_url si photo uploadée
            if ($photoPath) {
                $candidate->update([
                    'photo_url' => $photoPath,
                    'photo_optimization_status' => 'processing'
                ]);

                // Déclencher l'événement d'optimisation (asynchrone)
                CandidatePhotoUploaded::dispatch($candidate, $photoPath);

                Log::info('Événement d\'optimisation déclenché', [
                    'candidate_id' => $candidate->id,
                    'photo_path' => $photoPath
                ]);
            }

            // Envoyer un message WhatsApp de confirmation
            try {
                $whatsappService = new WhatsAppService();
                $message = "🎉 Félicitations {$this->prenom} ! Votre inscription au concours photo DINOR a été reçue. Votre candidature est en cours de validation. Vous recevrez une notification dès l'approbation.";
                $whatsappService->sendMessage($whatsappWithPrefix, $message);

                // Notifier l'admin - LOG OBLIGATOIRE
                Log::info('=== INSCRIPTION CANDIDAT: début notification admin ===', [
                    'candidat' => "{$this->prenom} {$this->nom}",
                    'user_id' => $user->id,
                    'candidate_id' => $candidate->id,
                ]);

                $adminPhone = config('services.whatsapp.admin_phone');
                Log::info('Config admin phone', ['admin_phone' => $adminPhone]);

                if (!empty($adminPhone)) {
                    Log::info('Notif admin inscription: envoi WhatsApp', [
                        'admin_phone' => $adminPhone,
                        'user_id' => $user->id,
                        'candidate_id' => $candidate->id,
                    ]);
                    $userUrl = route('filament.admin.resources.users.edit', $user);
                    $candidateUrl = route('filament.admin.resources.candidates.view', $candidate);
                    $adminMessage = "🔔 Nouvelle inscription CANDIDAT\n\n" .
                        "Nom: {$this->prenom} {$this->nom}\n" .
                        "WhatsApp: {$whatsappWithPrefix}\n" .
                        (isset($photoUrl) && $photoUrl ? "Photo: {$photoUrl}\n" : '') .
                        "ID utilisateur: {$user->id}\n" .
                        "ID candidat: {$candidate->id}\n" .
                        "Statut: pending\n\n" .
                        "▶ Utilisateur: {$userUrl}\n" .
                        "▶ Candidat: {$candidateUrl}";
                    $result = $whatsappService->sendMessage($adminPhone, $adminMessage);
                    Log::info('Notif admin inscription: résultat', $result);
                } else {
                    Log::warning('Admin phone vide - notification non envoyée');
                }

                Log::info('=== INSCRIPTION CANDIDAT: fin notification admin ===');
            } catch (\Exception $e) {
                Log::error('Erreur WhatsApp inscription: ' . $e->getMessage());
            }

            // Réinitialiser le formulaire
            $this->resetForm();

            // Message de succès avec informations détaillées
            session()->flash('success', 'Votre inscription a été envoyée avec succès ! ' .
                ($existingUser ? 'Votre numéro était déjà enregistré, nous avons connecté votre compte. ' : '') .
                'Vous recevrez une notification WhatsApp dès validation.');
            
            // Stocker les données pour la page de confirmation
            session()->flash('candidate_data', [
                'name' => $this->prenom . ' ' . $this->nom,
                'photo' => $photoPath,
                'whatsapp' => $whatsappWithPrefix,
                'existing_user' => (bool) $existingUser
            ]);

            // Redirection vers la page de confirmation candidat
            return redirect()->route('candidate.confirmation');

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
