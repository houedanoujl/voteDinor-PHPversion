<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Rediriger vers le fournisseur OAuth
     */
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Gérer le callback OAuth
     */
    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
            
            // Trouver ou créer l'utilisateur
            $user = $this->findOrCreateUser($socialUser, $provider);
            
            // Connecter l'utilisateur
            Auth::login($user, true);
            
            Log::info("Connexion sociale réussie", [
                'provider' => $provider,
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            // Rediriger avec message de succès
            return redirect()->intended('/')
                ->with('success', 'Connexion réussie avec ' . ucfirst($provider) . ' !');

        } catch (\Exception $e) {
            Log::error("Erreur connexion sociale {$provider}: " . $e->getMessage());
            
            return redirect()->route('login')
                ->with('error', 'Erreur lors de la connexion avec ' . ucfirst($provider) . '. Veuillez réessayer.');
        }
    }

    /**
     * Trouver ou créer un utilisateur basé sur les données sociales
     */
    private function findOrCreateUser($socialUser, string $provider): User
    {
        // 1. Chercher un utilisateur existant avec cet email
        if ($socialUser->getEmail()) {
            $user = User::where('email', $socialUser->getEmail())->first();
            
            if ($user) {
                // Mettre à jour les infos du fournisseur social
                $this->updateSocialInfo($user, $socialUser, $provider);
                return $user;
            }
        }

        // 2. Chercher par provider_id (si l'utilisateur existe déjà avec ce fournisseur)
        $user = User::where($provider . '_id', $socialUser->getId())->first();
        if ($user) {
            return $user;
        }

        // 3. Créer un nouvel utilisateur
        return $this->createUserFromSocial($socialUser, $provider);
    }

    /**
     * Créer un nouvel utilisateur à partir des données sociales
     */
    private function createUserFromSocial($socialUser, string $provider): User
    {
        $userData = [
            'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'Utilisateur',
            'email' => $socialUser->getEmail(),
            'avatar' => $socialUser->getAvatar(),
            'email_verified_at' => now(),
            $provider . '_id' => $socialUser->getId(),
        ];

        // Ne pas créer sans email
        if (!$socialUser->getEmail()) {
            throw new \Exception("Email requis pour créer un compte");
        }

        return User::create($userData);
    }

    /**
     * Mettre à jour les informations sociales d'un utilisateur existant
     */
    private function updateSocialInfo(User $user, $socialUser, string $provider): void
    {
        $updateData = [
            $provider . '_id' => $socialUser->getId()
        ];

        // Mettre à jour l'avatar si pas encore défini
        if (!$user->avatar && $socialUser->getAvatar()) {
            $updateData['avatar'] = $socialUser->getAvatar();
        }

        // Vérifier l'email si pas encore vérifié
        if (!$user->email_verified_at && $socialUser->getEmail()) {
            $updateData['email_verified_at'] = now();
        }

        $user->update($updateData);
    }

    /**
     * Valider le fournisseur OAuth
     */
    private function validateProvider(string $provider): void
    {
        $allowedProviders = ['google', 'facebook'];
        
        if (!in_array($provider, $allowedProviders)) {
            abort(404, 'Fournisseur non supporté');
        }
    }

    /**
     * Déconnecter l'utilisateur
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Vous êtes déconnecté.');
    }
}