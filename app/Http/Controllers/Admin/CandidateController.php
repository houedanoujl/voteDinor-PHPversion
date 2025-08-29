<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CandidateController extends Controller
{
    public function approve(Candidate $candidate)
    {
        $candidate->update(['status' => 'approved']);

        // Envoyer le message WhatsApp
        try {
            $whatsappService = new WhatsAppService();
            $message = "🎉 Félicitations ! Votre candidature pour le concours photo DINOR a été approuvée. Vous pouvez maintenant recevoir des votes. Bonne chance !";
            $whatsappService->sendMessage($candidate->whatsapp, $message);
        } catch (\Exception $e) {
            Log::error('Erreur WhatsApp: ' . $e->getMessage());
        }

        return back()->with('success', 'Candidat approuvé avec succès !');
    }

    public function reject(Candidate $candidate)
    {
        $candidate->update(['status' => 'rejected']);

        return back()->with('success', 'Candidat rejeté avec succès !');
    }

    public function destroy(Candidate $candidate)
    {
        try {
            // Supprimer les votes associés
            $candidate->votes()->delete();

            // Supprimer le candidat
            $candidate->delete();

            return back()->with('success', 'Candidat supprimé avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du candidat: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression du candidat.');
        }
    }
}
