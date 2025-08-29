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
        \Log::info('🟢 CandidateController - Début destroy', [
            'candidate_id' => $candidate->id,
            'candidate_name' => $candidate->prenom . ' ' . $candidate->nom,
            'request_method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'expects_json' => $request->expectsJson(),
            'headers' => $request->headers->all(),
            'url' => $request->fullUrl()
        ]);

        try {
            // Compter les votes associés
            $votesCount = $candidate->votes()->count();
            \Log::info('🟢 Votes trouvés pour suppression', [
                'candidate_id' => $candidate->id,
                'votes_count' => $votesCount
            ]);

            // Supprimer les votes associés
            $deletedVotes = $candidate->votes()->delete();
            \Log::info('🟢 Votes supprimés', [
                'deleted_votes' => $deletedVotes,
                'expected_votes' => $votesCount
            ]);

            // Supprimer le candidat
            $candidateName = $candidate->prenom . ' ' . $candidate->nom;
            $candidateId = $candidate->id;
            $deleted = $candidate->delete();
            \Log::info('🟢 Candidat supprimé', [
                'candidate_id' => $candidateId,
                'candidate_name' => $candidateName,
                'deleted' => $deleted
            ]);

            $message = "Candidat '{$candidateName}' et {$votesCount} votes supprimés avec succès !";

            // Si c'est une requête AJAX, retourner du JSON
            if ($request->expectsJson() || $request->ajax()) {
                $response = [
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'candidate_id' => $candidateId,
                        'candidate_name' => $candidateName,
                        'votes_deleted' => $votesCount
                    ]
                ];
                \Log::info('🟢 Réponse JSON de succès', $response);
                return response()->json($response);
            }

            \Log::info('🟢 Redirection avec message de succès');
            return back()->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('🔴 Erreur lors de la suppression du candidat', [
                'candidate_id' => $candidate->id ?? 'N/A',
                'candidate_name' => ($candidate->prenom ?? '') . ' ' . ($candidate->nom ?? ''),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            $errorMessage = 'Erreur lors de la suppression du candidat: ' . $e->getMessage();

            // Si c'est une requête AJAX, retourner du JSON
            if ($request->expectsJson() || $request->ajax()) {
                $response = [
                    'success' => false,
                    'message' => $errorMessage,
                    'error_details' => [
                        'exception_message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]
                ];
                \Log::error('🔴 Réponse JSON d\'erreur', $response);
                return response()->json($response, 500);
            }

            \Log::error('🔴 Redirection avec message d\'erreur');
            return back()->with('error', $errorMessage);
        }
    }
}
