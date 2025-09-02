<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'password' => 'required',
        ]);

        // Connexion WhatsApp uniquement
        $identifier = $request->input('identifier');
        $credentials = ['password' => $request->input('password')];

        // normaliser 10 chiffres -> +225XXXXXXXXXX
        $digits = preg_replace('/[^0-9]/', '', $identifier);
        if (strlen($digits) === 10) {
            $identifier = '+225' . $digits;
        } elseif (strpos($digits, '225') === 0 && strlen($digits) === 13) {
            $identifier = '+' . $digits;
        }
        $credentials['whatsapp'] = $identifier;

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        throw ValidationException::withMessages([
            'identifier' => 'Les identifiants fournis sont incorrects.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
