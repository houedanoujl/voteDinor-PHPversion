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

    public function destroy(Candidate $candidate, Request $request)
    {
        try {
            // Supprimer les votes associés
            $votesCount = $candidate->votes()->count();
            $candidate->votes()->delete();

            // Supprimer le candidat
            $candidateName = $candidate->prenom . ' ' . $candidate->nom;
            $candidate->delete();

            $message = "Candidat '{$candidateName}' et {$votesCount} votes supprimés avec succès !";

            // Si c'est une requête AJAX, retourner du JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du candidat: ' . $e->getMessage());

            $errorMessage = 'Erreur lors de la suppression du candidat.';

            // Si c'est une requête AJAX, retourner du JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return back()->with('error', $errorMessage);
        }
    }
}
