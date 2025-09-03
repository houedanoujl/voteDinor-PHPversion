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

        // Normaliser WhatsApp comme dans LoginController
        $normalizedWhatsapp = null;
        $digits = preg_replace('/[^0-9]/', '', $identifier) ?? '';
        if (strlen($digits) === 10) {
            $normalizedWhatsapp = '+225' . $digits;
        } elseif (strpos($digits, '225') === 0 && strlen($digits) === 13) {
            $normalizedWhatsapp = '+' . $digits;
        } elseif (str_starts_with($identifier, '+225') && strlen($digits) === 13) {
            $normalizedWhatsapp = $identifier;
        }

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $identifier)->first();
        } elseif ($normalizedWhatsapp) {
            $user = User::where('whatsapp', $normalizedWhatsapp)->first();
            // Fallback très limité: si non trouvé, tenter recherche exacte sur l'entrée brute
            if (!$user) {
                $user = User::where('whatsapp', $identifier)->first();
            }
        } else {
            $user = null;
        }

        if (!$user) {
            return back()->withErrors(['identifier' => "Utilisateur introuvable (email ou WhatsApp)."])->withInput();
        }

        if (empty($user->whatsapp)) {
            return back()->withErrors(['identifier' => "Aucun numéro WhatsApp associé à ce compte."])->withInput();
        }

        // Mot de passe simple: 8 caractères (minuscules/chiffres)
        $newPasswordPlain = strtolower(Str::random(8));
        // L'attribut cast "hashed" gère le hash automatiquement
        $user->password = $newPasswordPlain;
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


