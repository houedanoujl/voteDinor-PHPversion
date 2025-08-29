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
     * Envoie une notification de changement de statut
     */
    async sendStatusNotification(candidateId, status) {
        try {
            this.showLoader(`Envoi de la notification de statut...`);

            const response = await fetch(`${this.baseUrl}/send-status-notification`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    candidate_id: candidateId,
                    status: status
                })
            });

            const result = await response.json();

            this.hideLoader();

            if (result.success) {
                this.showNotification('✅ Notification de statut envoyée !', 'success');
            } else {
                this.showNotification(`❌ Erreur: ${result.message}`, 'error');
            }

            return result;

        } catch (error) {
            this.hideLoader();
            console.error('Erreur lors de l\'envoi de notification:', error);
            this.showNotification('❌ Erreur technique lors de l\'envoi de la notification', 'error');
            return { success: false, message: error.message };
        }
    },

    /**
     * Vérifie le statut de la connexion Green API
     */
    async checkApiStatus() {
        try {
            const response = await fetch(`${this.baseUrl}/status`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            const result = await response.json();

            if (result.success) {
                const status = result.data?.stateInstance || 'unknown';
                this.showNotification(`🔗 Statut Green API: ${status}`, 'info');
            } else {
                this.showNotification('❌ Impossible de vérifier le statut Green API', 'error');
            }

            return result;

        } catch (error) {
            console.error('Erreur lors de la vérification du statut:', error);
            this.showNotification('❌ Erreur technique lors de la vérification', 'error');
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
    if (window.WhatsAppService) {
        window.WhatsAppService.sendMessage(candidateId, 'notification');
    } else {
        console.error('WhatsAppService not available');
        alert('Service WhatsApp non disponible');
    }
};

/**
 * Fonction pour envoyer une notification de statut
 */
window.sendWhatsAppStatusNotification = function(candidateId, status) {
    if (window.WhatsAppService) {
        window.WhatsAppService.sendStatusNotification(candidateId, status);
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

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    console.log('WhatsApp Service with Green API initialized');

    // Vérifier que le token CSRF est disponible
    if (!window.WhatsAppService.csrfToken) {
        console.warn('CSRF Token not found. Make sure you have <meta name="csrf-token" content="{{ csrf_token() }}"> in your layout.');
    }
});
