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
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'message' => 'required|string|max:1000',
        ]);

        try {
            $candidate = Candidate::findOrFail($request->candidate_id);
            $whatsappService = new WhatsAppService();
            
            $result = $whatsappService->sendMessage($candidate->whatsapp, $request->message);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => "Message envoyé avec succès à {$candidate->prenom} {$candidate->nom}"
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible d\'envoyer le message WhatsApp. Vérifiez les logs.'
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur envoi WhatsApp depuis admin: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi: ' . $e->getMessage()
            ], 500);
        }
    }
}