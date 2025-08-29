<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegistrationConfirmationController extends Controller
{
    public function show()
    {
        $email = session('email');
        $password = session('password');
        $whatsappSent = session('whatsapp_sent', false);

        if (!$email || !$password) {
            return redirect()->route('home');
        }

        return view('auth.registration-confirmation', compact('email', 'password', 'whatsappSent'));
    }
}
