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

    public function register(Request $request)
    {
        $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'whatsapp' => 'required|string|regex:/^\+225[0-9]{8}$/',
        ], [
            'whatsapp.regex' => 'Le numéro WhatsApp doit être au format +225XXXXXXXX',
        ]);

        try {
            // Générer un email unique basé sur le prénom et nom
            $baseEmail = Str::slug($request->prenom . '.' . $request->nom) . '@dinor-concours.com';
            $email = $baseEmail;
            $counter = 1;
            
            while (User::where('email', $email)->exists()) {
                $email = Str::slug($request->prenom . '.' . $request->nom) . $counter . '@dinor-concours.com';
                $counter++;
            }

            // Générer un mot de passe aléatoire
            $password = Str::random(12);

            // Créer l'utilisateur
            $user = User::create([
                'name' => $request->prenom . ' ' . $request->nom,
                'prenom' => $request->prenom,
                'nom' => $request->nom,
                'email' => $email,
                'whatsapp' => $request->whatsapp,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);

            // Envoyer le message WhatsApp de bienvenue
            try {
                $whatsappService = new WhatsAppService();
                $message = "🎉 Bienvenue sur le concours photo DINOR !\n\n";
                $message .= "Votre compte a été créé avec succès.\n";
                $message .= "Vous pouvez maintenant voter pour vos candidats préférés ou soumettre votre propre photo.\n\n";
                $message .= "Connectez-vous avec votre email : {$email}\n";
                $message .= "Mot de passe temporaire : {$password}\n\n";
                $message .= "Bonne chance !";
                
                $whatsappService->sendMessage($request->whatsapp, $message);
            } catch (\Exception $e) {
                \Log::error('Erreur WhatsApp bienvenue: ' . $e->getMessage());
            }

            // Envoyer notification à l'administrateur
            try {
                $whatsappService = new WhatsAppService();
                $adminMessage = "🆕 Nouvelle inscription au concours DINOR !\n\n";
                $adminMessage .= "Nom : {$request->prenom} {$request->nom}\n";
                $adminMessage .= "WhatsApp : {$request->whatsapp}\n";
                $adminMessage .= "Email : {$email}\n";
                $adminMessage .= "Date : " . now()->format('d/m/Y H:i');
                
                $whatsappService->sendMessage('+2250545029721', $adminMessage);
            } catch (\Exception $e) {
                \Log::error('Erreur WhatsApp admin: ' . $e->getMessage());
            }

            // Connecter l'utilisateur automatiquement
            auth()->login($user);

            // Rediriger vers la page de confirmation
            return redirect()->route('registration.confirmation')->with([
                'email' => $email,
                'password' => $password,
                'whatsapp_sent' => true
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'inscription: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.']);
        }
    }
}
