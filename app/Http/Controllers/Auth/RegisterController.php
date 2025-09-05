<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\WhatsAppService;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }
    
    public function showVoterRegistrationForm()
    {
        return view('auth.register-voter');
    }

    public function register(Request $request)
    {
        // Rediriger vers la page d'inscription avec le formulaire Livewire complet
        return redirect()->route('register')->with('info', 'Veuillez utiliser le formulaire d\'inscription complet avec photo.');
    }
}
