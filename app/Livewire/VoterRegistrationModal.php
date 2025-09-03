<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\WhatsAppService;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\UserRegisteredEvent;

class VoterRegistrationModal extends Component
{
    public $showModal = false;
    public $prenom = '';
    public $nom = '';
    public $whatsapp = '';
    public $isSubmitting = false;

    protected $listeners = ['open-voter-modal' => 'openModal'];

    protected $rules = [
        'prenom' => 'required|min:2|max:255',
        'nom' => 'required|min:2|max:255',
        'whatsapp' => 'required|regex:/^\+225[0-9]{10}$/|unique:users,whatsapp',
    ];

    protected $messages = [
        'prenom.required' => 'Le prénom est obligatoire.',
        'nom.required' => 'Le nom est obligatoire.',
        'whatsapp.required' => 'Le numéro WhatsApp est obligatoire.',
        'whatsapp.regex' => 'Format requis: +225 suivi de 10 chiffres',
        'whatsapp.unique' => 'Ce numéro WhatsApp est déjà utilisé.',
    ];

    public function openModal()
    {
        $this->showModal = true;
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function submit()
    {
        $this->isSubmitting = true;

        try {
            $this->validate();

            // Plus d'email: on stocke un placeholder interne
            $email = (string) Str::uuid().'@dinor.local';

            // Générer un mot de passe aléatoire
            $password = Str::random(12);

            $whatsappWithPrefix = $this->whatsapp;

            // Créer l'utilisateur votant
            $user = User::create([
                'name' => $this->prenom . ' ' . $this->nom,
                'prenom' => $this->prenom,
                'nom' => $this->nom,
                'email' => $email,
                'whatsapp' => $whatsappWithPrefix,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'type' => 'voter',
                'role' => 'user',
            ]);

            // Connecter automatiquement l'utilisateur
            Auth::login($user);

            // Envoyer un message WhatsApp de bienvenue
            try {
                $whatsappService = new WhatsAppService();
                $dashboardUrl = url('/dashboard');
                $message = "🎉 Bienvenue {$this->prenom} sur le concours photo DINOR !\n\n";
                $message .= "Votre compte VOTANT a été créé avec succès.\n";
                $message .= "Vous pouvez maintenant voter pour vos candidats préférés.\n\n";
                $message .= "🔗 Accédez à votre dashboard : {$dashboardUrl}\n\n";
                $message .= "🔑 Mot de passe : {$password}\n\n";
                $message .= "Bon vote ! 🗳️";

                $whatsappService->sendMessage($whatsappWithPrefix, $message);
            } catch (\Exception $e) {
                Log::error('Erreur WhatsApp votant: ' . $e->getMessage());
            }

            // Réinitialiser le formulaire
            $this->resetForm();

            // Message de succès
            session()->flash('success', 'Votre compte votant a été créé avec succès ! Vous pouvez maintenant voter.');

            // Notifier l'admin (événement global)
            event(new UserRegisteredEvent($user, 'voter_modal'));

            // Fermer la modal et rediriger
            $this->closeModal();
            return redirect()->route('dashboard');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->isSubmitting = false;
            throw $e;
        } catch (\Exception $e) {
            $this->isSubmitting = false;
            Log::error('Erreur lors de la création du compte votant: ' . $e->getMessage());
            session()->flash('error', 'Une erreur est survenue lors de la création du compte. Veuillez réessayer.');
        }
    }

    private function resetForm()
    {
        $this->prenom = '';
        $this->nom = '';
        $this->whatsapp = '';
        $this->isSubmitting = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.voter-registration-modal');
    }
}
