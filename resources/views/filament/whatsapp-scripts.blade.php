{{-- Scripts WhatsApp avec Green API pour Filament --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
/**
 * Service WhatsApp avec Green API pour l'admin Filament
 */

// Configuration globale
window.WhatsAppService = {
    baseUrl: '/admin/whatsapp',
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),

    /**
     * Envoie un message WhatsApp à un candidat
     */
    async sendMessage(candidateId, messageType = 'notification', customMessage = null) {
        try {
            // Afficher un indicateur de chargement
            this.showLoader(`Envoi du message WhatsApp...`);

            const response = await fetch(`${this.baseUrl}/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    candidate_id: candidateId,
                    message_type: messageType,
                    message: customMessage
                })
            });

            const result = await response.json();

            this.hideLoader();

            if (result.success) {
                this.showNotification('✅ Message WhatsApp envoyé avec succès !', 'success');
            } else {
                this.showNotification(`❌ Erreur: ${result.message}`, 'error');
            }

            return result;

        } catch (error) {
            this.hideLoader();
            console.error('Erreur lors de l\'envoi WhatsApp:', error);
            this.showNotification('❌ Erreur technique lors de l\'envoi du message', 'error');
            return { success: false, message: error.message };
        }
    },

    /**
     * Affiche un indicateur de chargement
     */
    showLoader(message = 'Chargement...') {
        // Supprimer l'ancien loader s'il existe
        this.hideLoader();

        const loader = document.createElement('div');
        loader.id = 'whatsapp-loader';
        loader.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        loader.innerHTML = `
            <div class="bg-white rounded-lg p-6 flex items-center space-x-3 shadow-xl">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                <span class="text-gray-700 font-medium">${message}</span>
            </div>
        `;

        document.body.appendChild(loader);
    },

    /**
     * Cache l'indicateur de chargement
     */
    hideLoader() {
        const loader = document.getElementById('whatsapp-loader');
        if (loader) {
            loader.remove();
        }
    },

    /**
     * Affiche une notification toast
     */
    showNotification(message, type = 'info') {
        // Créer l'élément de notification
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 max-w-sm p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;

        // Définir les couleurs selon le type
        const colors = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            warning: 'bg-yellow-500 text-white',
            info: 'bg-blue-500 text-white'
        };

        notification.className += ` ${colors[type] || colors.info}`;
        notification.innerHTML = `
            <div class="flex items-center justify-between">
                <span class="font-medium">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Animer l'apparition
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto-suppression après 5 secondes
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }
};

/**
 * Fonction globale pour envoyer un message WhatsApp (appelée depuis les boutons)
 */
window.sendWhatsAppMessage = function(candidateId) {
    console.log('🟢 Bouton WhatsApp cliqué pour candidat:', candidateId);
    if (window.WhatsAppService) {
        window.WhatsAppService.sendMessage(candidateId, 'notification');
    } else {
        console.error('WhatsAppService not available');
        alert('Service WhatsApp non disponible');
    }
};

/**
 * Fonction pour vérifier le statut de l'API
 */
window.checkWhatsAppApiStatus = function() {
    if (window.WhatsAppService) {
        window.WhatsAppService.checkApiStatus();
    } else {
        console.error('WhatsAppService not available');
        alert('Service WhatsApp non disponible');
    }
};

/**
 * Fonction pour supprimer un candidat
 */
window.deleteCandidate = async function(candidateId) {
    try {
        console.log('🗑️ Suppression du candidat:', candidateId);

        // Afficher un loader
        if (window.WhatsAppService) {
            window.WhatsAppService.showLoader('Suppression du candidat...');
        }

        const response = await fetch(`/admin/candidates/${candidateId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (window.WhatsAppService) {
            window.WhatsAppService.hideLoader();
        }

        if (response.ok) {
            // Recharger la page pour voir les changements
            if (window.WhatsAppService) {
                window.WhatsAppService.showNotification('✅ Candidat supprimé avec succès !', 'success');
            }

            setTimeout(() => {
                window.location.reload();
            }, 1500);

            return true;
        } else {
            throw new Error('Erreur lors de la suppression');
        }

    } catch (error) {
        if (window.WhatsAppService) {
            window.WhatsAppService.hideLoader();
            window.WhatsAppService.showNotification('❌ Erreur lors de la suppression du candidat', 'error');
        } else {
            alert('Erreur lors de la suppression du candidat');
        }
        console.error('Erreur suppression candidat:', error);
        return false;
    }
};

console.log('🟢 WhatsApp Service avec Green API initialisé');
</script>

<script>
// Configuration additionnelle pour Filament
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter un bouton de vérification du statut dans la barre de navigation (optionnel)
    const navbar = document.querySelector('.fi-topbar');
    if (navbar) {
        const statusButton = document.createElement('button');
        statusButton.innerHTML = `
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.786z"/>
            </svg>
            Statut WhatsApp
        `;
        statusButton.className = 'hidden sm:inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500';
        statusButton.onclick = () => checkWhatsAppApiStatus();

        // L'ajouter à la barre de navigation n'est pas toujours possible selon la structure de Filament
        // On peut l'ajouter via CSS ou d'autres méthodes si nécessaire
    }

    console.log('🟢 Scripts WhatsApp Green API chargés pour Filament');
});

// Styles additionnels pour les notifications
const additionalStyles = `
<style>
/* Styles pour les notifications WhatsApp */
#whatsapp-loader {
    font-family: inherit;
}

.whatsapp-notification {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Amélioration des boutons WhatsApp dans les tableaux */
.fi-ta-actions button[onclick*="sendWhatsAppMessage"] {
    transition: all 0.2s ease;
}

.fi-ta-actions button[onclick*="sendWhatsAppMessage"]:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(34, 197, 94, 0.3);
}

/* Indicateur de chargement pour les boutons WhatsApp */
.whatsapp-loading {
    position: relative;
    pointer-events: none;
}

.whatsapp-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 16px;
    height: 16px;
    border: 2px solid #ffffff;
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}
</style>
`;

document.head.insertAdjacentHTML('beforeend', additionalStyles);
</script>
