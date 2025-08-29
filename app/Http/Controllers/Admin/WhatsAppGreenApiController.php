<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Services\GreenApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppGreenApiController extends Controller
{
    protected GreenApiService $greenApiService;

    public function __construct(GreenApiService $greenApiService)
    {
        $this->greenApiService = $greenApiService;
    }

    /**
     * Envoie un message WhatsApp à un candidat
     */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'candidate_id' => 'required|exists:candidates,id',
                'message' => 'string|nullable',
                'message_type' => 'string|in:custom,welcome,notification'
            ]);

            $candidate = Candidate::findOrFail($request->candidate_id);

            // Générer le message selon le type
            $message = $this->generateMessage($candidate, $request->message, $request->message_type);

            // Envoyer le message via Green API
            $result = $this->greenApiService->sendMessage(
                $candidate->whatsapp,
                $message
            );

            if ($result['success']) {
                Log::info('Message WhatsApp envoyé avec succès', [
                    'candidate_id' => $candidate->id,
                    'phone' => $candidate->whatsapp,
                    'message_type' => $request->message_type
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Message envoyé avec succès !',
                    'data' => $result['data']
                ]);
            } else {
                Log::error('Erreur lors de l\'envoi du message WhatsApp', [
                    'candidate_id' => $candidate->id,
                    'error' => $result['message']
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Exception lors de l\'envoi WhatsApp', [
                'candidate_id' => $request->candidate_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur technique lors de l\'envoi du message'
            ], 500);
        }
    }

    /**
     * Envoie un message de notification de statut
     */
    public function sendStatusNotification(Request $request)
    {
        try {
            $request->validate([
                'candidate_id' => 'required|exists:candidates,id',
                'status' => 'required|in:approved,rejected,pending'
            ]);

            $candidate = Candidate::findOrFail($request->candidate_id);

            // Générer le message de notification de statut
            $message = $this->greenApiService->generateNotificationMessage(
                $candidate->prenom,
                $candidate->nom,
                $request->status
            );

            // Envoyer le message
            $result = $this->greenApiService->sendMessage(
                $candidate->whatsapp,
                $message
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification de statut envoyée avec succès !',
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Exception lors de l\'envoi de notification de statut', [
                'candidate_id' => $request->candidate_id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur technique lors de l\'envoi de la notification'
            ], 500);
        }
    }

    /**
     * Vérifie le statut de la connexion Green API
     */
    public function checkStatus()
    {
        try {
            $result = $this->greenApiService->getStateInstance();

            return response()->json([
                'success' => $result['success'],
                'data' => $result['data'] ?? null,
                'message' => $result['message'] ?? 'Statut récupéré avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification du statut'
            ], 500);
        }
    }

    /**
     * Génère le message selon le type demandé
     */
    private function generateMessage(Candidate $candidate, ?string $customMessage, ?string $messageType): string
    {
        if ($customMessage && $messageType === 'custom') {
            return $customMessage;
        }

        switch ($messageType) {
            case 'welcome':
                return $this->greenApiService->generateWelcomeMessage(
                    $candidate->prenom,
                    $candidate->nom
                );

            case 'notification':
                return $this->greenApiService->generateNotificationMessage(
                    $candidate->prenom,
                    $candidate->nom,
                    $candidate->status
                );

            default:
                return $this->greenApiService->generateNotificationMessage(
                    $candidate->prenom,
                    $candidate->nom,
                    $candidate->status
                );
        }
    }

    /**
     * Envoie un message groupé à plusieurs candidats
     */
    public function sendBulkMessage(Request $request)
    {
        try {
            $request->validate([
                'candidate_ids' => 'required|array',
                'candidate_ids.*' => 'exists:candidates,id',
                'message' => 'required|string',
                'message_type' => 'string|in:custom,welcome,notification'
            ]);

            $candidates = Candidate::whereIn('id', $request->candidate_ids)->get();
            $results = [];
            $successCount = 0;
            $errorCount = 0;

            foreach ($candidates as $candidate) {
                try {
                    $message = $this->generateMessage($candidate, $request->message, $request->message_type);

                    $result = $this->greenApiService->sendMessage(
                        $candidate->whatsapp,
                        $message
                    );

                    $results[] = [
                        'candidate_id' => $candidate->id,
                        'name' => $candidate->prenom . ' ' . $candidate->nom,
                        'phone' => $candidate->whatsapp,
                        'success' => $result['success'],
                        'message' => $result['message']
                    ];

                    if ($result['success']) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }

                    // Délai entre les envois pour éviter le spam
                    sleep(1);

                } catch (\Exception $e) {
                    $results[] = [
                        'candidate_id' => $candidate->id,
                        'name' => $candidate->prenom . ' ' . $candidate->nom,
                        'phone' => $candidate->whatsapp,
                        'success' => false,
                        'message' => 'Erreur: ' . $e->getMessage()
                    ];
                    $errorCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Envoi terminé: {$successCount} succès, {$errorCount} erreurs",
                'data' => [
                    'total' => count($candidates),
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'results' => $results
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Exception lors de l\'envoi groupé WhatsApp', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur technique lors de l\'envoi groupé'
            ], 500);
        }
    }
}
