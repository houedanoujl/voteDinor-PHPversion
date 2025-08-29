<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthModal extends Component
{
    public $showModal = false;
    public $mode = 'login'; // 'login' or 'register'

    protected $listeners = ['open-auth-modal' => 'handleOpenModal'];
    
    // Login fields
    public $email = '';
    public $password = '';
    public $remember = false;
    
    // Register fields
    public $name = '';
    public $email_register = '';
    public $password_register = '';
    public $password_confirmation = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
        'name' => 'required|min:2|max:255',
        'email_register' => 'required|email|unique:users,email',
        'password_register' => 'required|min:8|confirmed',
    ];

    protected $messages = [
        'email.required' => 'L\'email est obligatoire.',
        'email.email' => 'L\'email doit être valide.',
        'password.required' => 'Le mot de passe est obligatoire.',
        'name.required' => 'Le nom est obligatoire.',
        'email_register.required' => 'L\'email est obligatoire.',
        'email_register.email' => 'L\'email doit être valide.',
        'email_register.unique' => 'Cet email est déjà utilisé.',
        'password_register.required' => 'Le mot de passe est obligatoire.',
        'password_register.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        'password_register.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
    ];

    public function handleOpenModal($data)
    {
        $this->mode = $data['mode'] ?? 'login';
        $this->showModal = true;
        $this->resetForm();
    }

    public function openModal($mode = 'login')
    {
        $this->mode = $mode;
        $this->showModal = true;
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function switchMode($mode)
    {
        $this->mode = $mode;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->email = '';
        $this->password = '';
        $this->remember = false;
        $this->name = '';
        $this->email_register = '';
        $this->password_register = '';
        $this->password_confirmation = '';
        $this->resetErrorBag();
    }

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            $this->closeModal();
            $this->dispatch('user-logged-in');
            session()->flash('success', 'Connexion réussie !');
        } else {
            $this->addError('email', 'Ces informations d\'identification ne correspondent pas à nos enregistrements.');
        }
    }

    public function register()
    {
        $this->validate([
            'name' => 'required|min:2|max:255',
            'email_register' => 'required|email|unique:users,email',
            'password_register' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email_register,
            'password' => Hash::make($this->password_register),
            'email_verified_at' => now(),
        ]);

        Auth::login($user);
        session()->regenerate();
        $this->closeModal();
        $this->dispatch('user-registered');
        session()->flash('success', 'Inscription réussie ! Bienvenue !');
    }

    public function render()
    {
        return view('livewire.auth-modal');
    }
}
