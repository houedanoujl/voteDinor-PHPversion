<?php

namespace App\Filament\Admin\Resources;

use App\Models\Candidate;
use App\Services\WhatsAppService;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static ?string $navigationLabel = 'Candidats';

    protected static ?string $pluralModelLabel = 'Candidats';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('prenom')
                    ->label('Prénom')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->copyable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('votes_count')
                    ->label('Votes')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Photo')
                    ->getStateUsing(function ($record) {
                        return $record->getPhotoUrl();
                    })
                    ->height(60)
                    ->width(60)
                    ->circular(),
                                Tables\Columns\TextColumn::make('id')
                    ->label('Actions')
                    ->formatStateUsing(function ($state, $record) {
                        // Bouton WhatsApp avec Green API et logs de débogage
                        $whatsappButton = "
                        <button onclick='console.log(\"🟢 Clic sur bouton WhatsApp candidat {$record->id}\"); sendWhatsAppMessage({$record->id})'
                               style='background: linear-gradient(45deg, #10b981, #059669); border: none; color: white; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: inline-flex; align-items: center; gap: 4px;'
                               onmouseover='this.style.transform=\"translateY(-1px)\"; this.style.boxShadow=\"0 4px 8px rgba(0,0,0,0.2)\"'
                               onmouseout='this.style.transform=\"translateY(0)\"; this.style.boxShadow=\"0 2px 4px rgba(0,0,0,0.1)\"'
                               title='Envoyer un message WhatsApp'>
                            <svg style='width: 16px; height: 16px;' fill='currentColor' viewBox='0 0 24 24'>
                                <path d='M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.786z'/>
                            </svg>
                            WhatsApp
                        </button>";

                        // Bouton de détail
                        $viewUrl = route('filament.admin.resources.candidates.view', $record);
                        $detailButton = "
                        <a href='{$viewUrl}'
                           style='background: linear-gradient(45deg, #3b82f6, #2563eb); border: none; color: white; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: inline-flex; align-items: center; gap: 4px;'
                           onmouseover='this.style.transform=\"translateY(-1px)\"; this.style.boxShadow=\"0 4px 8px rgba(0,0,0,0.2)\"'
                           onmouseout='this.style.transform=\"translateY(0)\"; this.style.boxShadow=\"0 2px 4px rgba(0,0,0,0.1)\"'
                           title='Voir les détails du candidat'>
                            <svg style='width: 16px; height: 16px;' fill='currentColor' viewBox='0 0 20 20'>
                                <path d='M10 12a2 2 0 100-4 2 2 0 000 4z'/>
                                <path fill-rule='evenodd' d='M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z' clip-rule='evenodd'/>
                            </svg>
                            Détail
                        </a>";

                        // Boutons d'approbation pour les candidats en attente
                        if ($record->status === 'pending') {
                            $approveUrl = route('admin.candidates.approve', $record);
                            $rejectUrl = route('admin.candidates.reject', $record);

                            $approveButton = "
                            <a href='{$approveUrl}'
                               style='background: linear-gradient(45deg, #10b981, #059669); border: none; color: white; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: inline-flex; align-items: center; gap: 4px;'
                               onmouseover='this.style.transform=\"translateY(-1px)\"; this.style.boxShadow=\"0 4px 8px rgba(0,0,0,0.2)\"'
                               onmouseout='this.style.transform=\"translateY(0)\"; this.style.boxShadow=\"0 2px 4px rgba(0,0,0,0.1)\"'
                               title='Approuver le candidat'>
                                <svg style='width: 16px; height: 16px;' fill='currentColor' viewBox='0 0 20 20'>
                                    <path fill-rule='evenodd' d='M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' clip-rule='evenodd'/>
                                </svg>
                                ✓ Approuver
                            </a>";

                            $rejectButton = "
                            <a href='{$rejectUrl}'
                               onclick='return confirm(\"Êtes-vous sûr de vouloir rejeter ce candidat ?\")'
                               style='background: linear-gradient(45deg, #ef4444, #dc2626); border: none; color: white; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: inline-flex; align-items: center; gap: 4px;'
                               onmouseover='this.style.transform=\"translateY(-1px)\"; this.style.boxShadow=\"0 4px 8px rgba(0,0,0,0.2)\"'
                               onmouseout='this.style.transform=\"translateY(0)\"; this.style.boxShadow=\"0 2px 4px rgba(0,0,0,0.1)\"'
                               title='Rejeter le candidat'>
                                <svg style='width: 16px; height: 16px;' fill='currentColor' viewBox='0 0 20 20'>
                                    <path fill-rule='evenodd' d='M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z' clip-rule='evenodd'/>
                                </svg>
                                ✗ Rejeter
                            </a>";

                            return "
                            <div style='display: flex; flex-direction: column; gap: 8px; min-width: max-content;'>
                                <div style='display: flex; gap: 4px; flex-wrap: wrap;'>
                                    {$approveButton}
                                    {$rejectButton}
                                </div>
                                <div style='display: flex; gap: 4px; flex-wrap: wrap;'>
                                    {$detailButton}
                                    {$whatsappButton}
                                </div>
                            </div>";
                        }

                        // Bouton de suppression (pour tous les candidats) avec logs de débogage
                        $deleteButton = "
                        <button onclick='console.log(\"🟢 Clic sur bouton Supprimer candidat {$record->id}\"); if(confirm(\"Êtes-vous sûr de vouloir supprimer définitivement ce candidat et tous ses votes ?\")) { deleteCandidate({$record->id}); }'
                               style='background: linear-gradient(45deg, #ef4444, #dc2626); border: none; color: white; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: inline-flex; align-items: center; gap: 4px;'
                               onmouseover='this.style.transform=\"translateY(-1px)\"; this.style.boxShadow=\"0 4px 8px rgba(0,0,0,0.2)\"'
                               onmouseout='this.style.transform=\"translateY(0)\"; this.style.boxShadow=\"0 2px 4px rgba(0,0,0,0.1)\"'
                               title='Supprimer définitivement le candidat'>
                            <svg style='width: 16px; height: 16px;' fill='currentColor' viewBox='0 0 20 20'>
                                <path fill-rule='evenodd' d='M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z' clip-rule='evenodd'/>
                            </svg>
                            🗑️ Supprimer
                        </button>";

                        // Pour les candidats approuvés/rejetés - boutons pour changer le statut
                        if ($record->status === 'approved') {
                            $statusBadge = '<span style="color: #059669; font-weight: 600; font-size: 12px;">✓ Approuvé</span>';

                            // Bouton pour repasser en attente ou rejeter
                            $changeStatusButton = "
                            <div style='display: flex; gap: 4px;'>
                                <a href='" . route('admin.candidates.reject', $record) . "'
                                   onclick='return confirm(\"Voulez-vous rejeter ce candidat approuvé ?\")'
                                   style='background: linear-gradient(45deg, #f59e0b, #d97706); border: none; color: white; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; text-decoration: none; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: inline-flex; align-items: center; gap: 4px;'
                                   onmouseover='this.style.transform=\"translateY(-1px)\"; this.style.boxShadow=\"0 4px 8px rgba(0,0,0,0.2)\"'
                                   onmouseout='this.style.transform=\"translateY(0)\"; this.style.boxShadow=\"0 2px 4px rgba(0,0,0,0.1)\"'
                                   title='Changer vers Rejeté'>
                                    <svg style='width: 14px; height: 14px;' fill='currentColor' viewBox='0 0 20 20'>
                                        <path fill-rule='evenodd' d='M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z' clip-rule='evenodd'/>
                                    </svg>
                                    Rejeter
                                </a>
                            </div>";

                        } elseif ($record->status === 'rejected') {
                            $statusBadge = '<span style="color: #dc2626; font-weight: 600; font-size: 12px;">✗ Rejeté</span>';

                            // Bouton pour approuver
                            $changeStatusButton = "
                            <div style='display: flex; gap: 4px;'>
                                <a href='" . route('admin.candidates.approve', $record) . "'
                                   onclick='return confirm(\"Voulez-vous approuver ce candidat rejeté ?\")'
                                   style='background: linear-gradient(45deg, #10b981, #059669); border: none; color: white; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; text-decoration: none; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: inline-flex; align-items: center; gap: 4px;'
                                   onmouseover='this.style.transform=\"translateY(-1px)\"; this.style.boxShadow=\"0 4px 8px rgba(0,0,0,0.2)\"'
                                   onmouseout='this.style.transform=\"translateY(0)\"; this.style.boxShadow=\"0 2px 4px rgba(0,0,0,0.1)\"'
                                   title='Changer vers Approuvé'>
                                    <svg style='width: 14px; height: 14px;' fill='currentColor' viewBox='0 0 20 20'>
                                        <path fill-rule='evenodd' d='M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' clip-rule='evenodd'/>
                                    </svg>
                                    Approuver
                                </a>
                            </div>";
                        } else {
                            $statusBadge = '<span style="color: #6b7280; font-size: 12px;">En attente</span>';
                            $changeStatusButton = '';
                        }

                        return "
                        <div style='display: flex; flex-direction: column; gap: 8px; min-width: max-content;'>
                            <div style='display: flex; align-items: center; gap: 8px;'>
                                {$statusBadge}
                                {$changeStatusButton}
                            </div>
                            <div style='display: flex; gap: 4px; flex-wrap: wrap;'>
                                {$detailButton}
                                {$whatsappButton}
                            </div>
                            <div style='display: flex; gap: 4px;'>
                                {$deleteButton}
                            </div>
                        </div>";
                    })
                    ->html(),

                // Colonne invisible pour injecter le JavaScript
                Tables\Columns\TextColumn::make('script_loader')
                    ->label('')
                    ->formatStateUsing(function () {
                        static $scriptLoaded = false;
                        if (!$scriptLoaded) {
                            $scriptLoaded = true;
                            return new HtmlString('
                                                                <script>
                                console.log("🟢 Script CandidateResource - Début du chargement");
                                
                                if (!window.scriptsLoaded) {
                                    window.scriptsLoaded = true;
                                    console.log("🟢 Scripts non encore chargés, initialisation...");
                                    
                                    // Configuration globale
                                    window.WhatsAppService = {
                                        baseUrl: "/admin/whatsapp",
                                        csrfToken: document.querySelector("meta[name=csrf-token]")?.getAttribute("content") || "' . csrf_token() . '",

                                        async sendMessage(candidateId, messageType = "notification", customMessage = null) {
                                            try {
                                                console.log("🟢 WhatsApp Service - Début sendMessage", { candidateId, messageType, customMessage });
                                                
                                                this.showLoader("Envoi du message WhatsApp...");
                                                
                                                const requestData = {
                                                    candidate_id: candidateId,
                                                    message_type: messageType,
                                                    message: customMessage
                                                };
                                                console.log("🟢 Données à envoyer:", requestData);
                                                console.log("🟢 URL cible:", `${this.baseUrl}/send`);
                                                console.log("🟢 CSRF Token:", this.csrfToken);
                                                
                                                const response = await fetch(`${this.baseUrl}/send`, {
                                                    method: "POST",
                                                    headers: {
                                                        "Content-Type": "application/json",
                                                        "X-CSRF-TOKEN": this.csrfToken,
                                                        "Accept": "application/json"
                                                    },
                                                    body: JSON.stringify(requestData)
                                                });
                                                
                                                console.log("🟢 Réponse HTTP:", response);
                                                console.log("🟢 Status:", response.status);
                                                console.log("🟢 Headers:", Object.fromEntries(response.headers.entries()));
                                                
                                                const result = await response.json();
                                                console.log("🟢 Résultat JSON:", result);
                                                
                                                this.hideLoader();
                                                if (result.success) {
                                                    this.showNotification("✅ Message WhatsApp envoyé avec succès !", "success");
                                                } else {
                                                    this.showNotification(`❌ Erreur: ${result.message}`, "error");
                                                }
                                                return result;
                                            } catch (error) {
                                                console.error("🔴 Erreur lors de l envoi WhatsApp:", error);
                                                console.error("🔴 Stack trace:", error.stack);
                                                this.hideLoader();
                                                this.showNotification("❌ Erreur technique lors de l envoi du message", "error");
                                                return { success: false, message: error.message };
                                            }
                                        },

                                        showLoader(message = "Chargement...") {
                                            this.hideLoader();
                                            const loader = document.createElement("div");
                                            loader.id = "whatsapp-loader";
                                            loader.className = "fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50";
                                            loader.innerHTML = `<div class="bg-white rounded-lg p-6 flex items-center space-x-3 shadow-xl"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div><span class="text-gray-700 font-medium">${message}</span></div>`;
                                            document.body.appendChild(loader);
                                        },

                                        hideLoader() {
                                            const loader = document.getElementById("whatsapp-loader");
                                            if (loader) loader.remove();
                                        },

                                        showNotification(message, type = "info") {
                                            const notification = document.createElement("div");
                                            notification.className = `fixed top-4 right-4 max-w-sm p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
                                            const colors = { success: "bg-green-500 text-white", error: "bg-red-500 text-white", warning: "bg-yellow-500 text-white", info: "bg-blue-500 text-white" };
                                            notification.className += ` ${colors[type] || colors.info}`;
                                            notification.innerHTML = `<div class="flex items-center justify-between"><span class="font-medium">${message}</span><button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">×</button></div>`;
                                            document.body.appendChild(notification);
                                            setTimeout(() => notification.classList.remove("translate-x-full"), 100);
                                            setTimeout(() => { notification.classList.add("translate-x-full"); setTimeout(() => { if (notification.parentNode) notification.remove(); }, 300); }, 5000);
                                        }
                                    };

                                                                    // Fonction globale pour envoyer un message WhatsApp
                                window.sendWhatsAppMessage = function(candidateId) {
                                    console.log("🟢 sendWhatsAppMessage - Fonction appelée pour candidat:", candidateId);
                                    console.log("🟢 WhatsAppService disponible:", !!window.WhatsAppService);
                                    console.log("🟢 État complet de window.WhatsAppService:", window.WhatsAppService);
                                    
                                    if (window.WhatsAppService) {
                                        console.log("🟢 Appel de sendMessage...");
                                        window.WhatsAppService.sendMessage(candidateId, "notification");
                                    } else {
                                        console.error("🔴 WhatsAppService non disponible");
                                        alert("Service WhatsApp non disponible - Vérifiez la console");
                                    }
                                };

                                                                    // Fonction pour supprimer un candidat
                                window.deleteCandidate = async function(candidateId) {
                                    try {
                                        console.log("🟢 deleteCandidate - Fonction appelée pour candidat:", candidateId);
                                        console.log("🟢 URL de suppression:", `/admin/candidates/${candidateId}`);
                                        console.log("🟢 CSRF Token:", document.querySelector("meta[name=csrf-token]")?.getAttribute("content") || "' . csrf_token() . '");
                                        
                                        if (window.WhatsAppService) window.WhatsAppService.showLoader("Suppression du candidat...");
                                        
                                        const response = await fetch(`/admin/candidates/${candidateId}`, {
                                            method: "DELETE",
                                            headers: {
                                                "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]")?.getAttribute("content") || "' . csrf_token() . '",
                                                "Accept": "application/json",
                                                "Content-Type": "application/json"
                                            }
                                        });
                                        
                                        console.log("🟢 Réponse suppression:", response);
                                        console.log("🟢 Status:", response.status);
                                        
                                        if (window.WhatsAppService) window.WhatsAppService.hideLoader();
                                        
                                        if (response.ok) {
                                            const result = await response.json();
                                            console.log("🟢 Résultat suppression:", result);
                                            if (window.WhatsAppService) window.WhatsAppService.showNotification("✅ Candidat supprimé avec succès !", "success");
                                            setTimeout(() => window.location.reload(), 1500);
                                            return true;
                                        } else {
                                            const errorResult = await response.text();
                                            console.error("🔴 Erreur HTTP:", response.status, errorResult);
                                            throw new Error(`Erreur HTTP ${response.status}: ${errorResult}`);
                                        }
                                    } catch (error) {
                                        console.error("🔴 Erreur suppression candidat:", error);
                                        if (window.WhatsAppService) {
                                            window.WhatsAppService.hideLoader();
                                            window.WhatsAppService.showNotification("❌ Erreur lors de la suppression du candidat: " + error.message, "error");
                                        } else {
                                            alert("Erreur lors de la suppression du candidat: " + error.message);
                                        }
                                        return false;
                                    }
                                };

                                    console.log("🟢 WhatsApp Service avec Green API initialisé (Script Loader)");
                                } else {
                                    console.log("🟠 Scripts déjà chargés, pas de re-initialisation");
                                }
                                
                                console.log("🟢 Script CandidateResource - Fin du chargement");
                                console.log("🟢 État final - window.WhatsAppService:", window.WhatsAppService);
                                console.log("🟢 État final - window.sendWhatsAppMessage:", window.sendWhatsAppMessage);
                                console.log("🟢 État final - window.deleteCandidate:", window.deleteCandidate);
                                </script>
                            ');
                        }
                        return '';
                    })
                    ->html()
                    ->visible(false),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null) // Désactive le clic sur la ligne

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('votes');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\CandidateResource\Pages\ListCandidates::route('/'),
            'view' => \App\Filament\Admin\Resources\CandidateResource\Pages\ViewCandidate::route('/{record}'),
        ];
    }
}
