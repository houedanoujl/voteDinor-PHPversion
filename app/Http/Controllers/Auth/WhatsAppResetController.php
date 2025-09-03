<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WhatsAppResetController extends Controller
{
    public function show()
    {
        return view('auth.passwords.whatsapp');
    }

    public function send(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string', // email ou whatsapp
        ]);

        $identifier = trim($request->input('identifier'));

        $user = User::query()
            ->when(filter_var($identifier, FILTER_VALIDATE_EMAIL), fn ($q) => $q->where('email', $identifier))
            ->when(!filter_var($identifier, FILTER_VALIDATE_EMAIL), fn ($q) => $q->orWhere('whatsapp', 'like', "%{$identifier}%"))
            ->first();

        if (!$user) {
            return back()->withErrors(['identifier' => "Utilisateur introuvable (email ou WhatsApp)."])->withInput();
        }

        if (empty($user->whatsapp)) {
            return back()->withErrors(['identifier' => "Aucun numéro WhatsApp associé à ce compte."])->withInput();
        }

        $newPasswordPlain = Str::password(12);
        $user->password = Hash::make($newPasswordPlain);
        $user->setRememberToken(Str::random(60));
        $user->save();

        $message = "🔐 Réinitialisation de mot de passe\n\n" .
            "Bonjour {$user->name},\n" .
            "Voici votre nouveau mot de passe: {$newPasswordPlain}\n\n" .
            "Par sécurité, changez-le après connexion dans votre profil.";

        $whatsApp = new WhatsAppService();
        try {
            $resp = $whatsApp->sendMessage($user->whatsapp, $message);
            if (!($resp['success'] ?? false)) {
                return back()->withErrors(['identifier' => 'Envoi WhatsApp échoué, réessayez plus tard.'])->withInput();
            }
        } catch (\Throwable $e) {
            return back()->withErrors(['identifier' => 'Erreur WhatsApp: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('login')->with('status', 'Un nouveau mot de passe a été envoyé par WhatsApp.');
    }
}


