<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function sendMessage(Request $request)
    {
        \Log::info('🟢 WhatsApp Controller - Début sendMessage', [
            'request_data' => $request->all(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl()
        ]);

        try {
            $validation = $request->validate([
                'candidate_id' => 'required|exists:candidates,id',
                'message_type' => 'sometimes|string',
                'message' => 'sometimes|string|max:1000',
            ]);

            \Log::info('🟢 Validation réussie', $validation);

            $candidate = Candidate::findOrFail($request->candidate_id);
            \Log::info('🟢 Candidat trouvé', [
                'candidate_id' => $candidate->id,
                'candidate_name' => $candidate->prenom . ' ' . $candidate->nom,
                'whatsapp' => $candidate->whatsapp
            ]);

            $whatsappService = new WhatsAppService();
            
            // Préparer le message
            $message = $request->message;
            if (!$message && $request->message_type === 'notification') {
                $message = "🎉 Félicitations {$candidate->prenom} ! Votre candidature au concours photo DINOR a été approuvée. Vous pouvez maintenant recevoir des votes !";
            }

            \Log::info('🟢 Message préparé', ['message' => $message]);
            
            $result = $whatsappService->sendMessage($candidate->whatsapp, $message);
            \Log::info('🟢 Résultat du service WhatsApp', $result);
            
            if ($result['success']) {
                $response = [
                    'success' => true,
                    'message' => "Message envoyé avec succès à {$candidate->prenom} {$candidate->nom}"
                ];
                \Log::info('🟢 Réponse de succès', $response);
                return response()->json($response);
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Impossible d\'envoyer le message WhatsApp. ' . ($result['message'] ?? 'Erreur inconnue')
                ];
                \Log::warning('🟠 Réponse d\'échec', $response);
                return response()->json($response, 400);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('🔴 Erreur de validation WhatsApp', [
                'errors' => $e->errors(),
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Données invalides: ' . json_encode($e->errors())
            ], 422);
        } catch (\Exception $e) {
            \Log::error('🔴 Erreur envoi WhatsApp depuis admin', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi: ' . $e->getMessage()
            ], 500);
        }
    }
}