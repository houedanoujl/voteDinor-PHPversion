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

class CandidateRegistrationModal extends Component
{
    use WithFileUploads;

    public $showModal = false;
    public $prenom = '';
    public $nom = '';
    public $email = '';
    public $whatsapp = '';

    public $photo = null;
    public $tempPhotoUrl = null;

    protected $listeners = ['open-candidate-modal' => 'openModal'];

    protected $rules = [
        'prenom' => 'required|min:2|max:255',
        'nom' => 'required|min:2|max:255',
        'email' => 'required|email|unique:users,email',
        'whatsapp' => 'required|regex:/^\+225[0-9]{8}$/|unique:candidates,whatsapp',
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
        'photo.required' => 'Une photo est obligatoire.',
        'photo.image' => 'Le fichier doit être une image.',
        'photo.max' => 'La photo ne doit pas dépasser 3MB.',
        'photo.max_file_size' => 'La photo est trop grande. Maximum autorisé: 3MB.'
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
        $this->reset(['prenom', 'nom', 'email', 'whatsapp', 'photo', 'tempPhotoUrl']);
        $this->resetErrorBag();
    }

    public function updatedPhoto()
    {
        // Vérifier la taille du fichier avant la validation
        if ($this->photo && $this->photo->getSize() > 3072000) { // 3MB en bytes
            $this->addError('photo', 'La photo est trop grande. Maximum autorisé: 3MB.');
            $this->photo = null;
            $this->tempPhotoUrl = null;
            return;
        }

        $this->validate(['photo' => 'image|max:3072']);

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
            'whatsapp' => 'required|regex:/^\+225[0-9]{8}$/|unique:candidates,whatsapp',
            'photo' => 'required|image|max:3072',
        ];

        // Ajouter la validation email seulement si l'utilisateur n'est pas connecté
        if (!auth()->check()) {
            $rules['email'] = 'required|email|unique:users,email';
        }

        $this->validate($rules);

        try {
            // Créer l'utilisateur s'il n'est pas déjà connecté
            $user = null;
            $password = null;
            $isNewUser = false;

            if (!auth()->check()) {
                // Générer un mot de passe temporaire
                $password = Str::random(12);
                $isNewUser = true;

                $user = User::create([
                    'name' => $this->prenom . ' ' . $this->nom,
                    'prenom' => $this->prenom,
                    'nom' => $this->nom,
                    'email' => $this->email,
                    'password' => Hash::make($password),
                    'email_verified_at' => now(), // Auto-vérifier l'email
                    'type' => 'candidate',
                    'role' => 'user',
                ]);

                // Connecter l'utilisateur automatiquement
                Auth::login($user);
            } else {
                $user = auth()->user();
                // Mettre à jour le type d'utilisateur vers candidat s'il ne l'est pas déjà
                if ($user->type !== 'candidate') {
                    $user->update(['type' => 'candidate']);
                }
            }

            $candidate = Candidate::create([
                'prenom' => $this->prenom,
                'nom' => $this->nom,
                'whatsapp' => $this->whatsapp,
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

            // Envoyer les identifiants WhatsApp si c'est un nouvel utilisateur
            if ($isNewUser && $password) {
                try {
                    $whatsappService = new WhatsAppService();
                    $dashboardUrl = route('dashboard');

                    $message = "🎯 Bienvenue sur DINOR Concours Photo !\n\n";
                    $message .= "Votre inscription a été effectuée avec succès.\n\n";
                    $message .= "📧 Email: {$this->email}\n";
                    $message .= "🔑 Mot de passe: {$password}\n\n";
                    $message .= "🔗 Accédez à votre tableau de bord :\n{$dashboardUrl}\n\n";
                    $message .= "⚠️ Conservez ces informations en lieu sûr.\n";
                    $message .= "Vous pourrez changer votre mot de passe dans les paramètres.\n\n";
                    $message .= "Votre candidature sera validée sous 24h.\n\n";
                    $message .= "Merci de votre participation !\n";
                    $message .= "L'équipe DINOR";

                    $result = $whatsappService->sendMessage($this->whatsapp, $message);

                    if (!$result['success']) {
                        \Log::warning('Échec envoi WhatsApp identifiants', [
                            'user_id' => $user->id,
                            'whatsapp' => $this->whatsapp,
                            'response' => $result
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Erreur envoi identifiants WhatsApp: ' . $e->getMessage(), [
                        'user_id' => $user->id,
                        'whatsapp' => $this->whatsapp
                    ]);
                }
            }

            session()->flash('success', '✅ Inscription réussie ! ' .
                ($isNewUser ? 'Un compte a été créé et vos identifiants ont été envoyés par WhatsApp. Votre candidature sera validée sous 24h.' :
                'Votre candidature sera validée sous 24h.'));

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
