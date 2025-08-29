<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
console.log('🔄 Chargement des scripts globaux Filament...');

// Configuration globale
window.WhatsAppService = {
    baseUrl: "/admin/whatsapp",
    csrfToken: document.querySelector("meta[name=csrf-token]")?.getAttribute("content"),

    // Envoie un message WhatsApp à un candidat
    async sendMessage(candidateId, messageType = "notification", customMessage = null) {
        try {
            this.showLoader("Envoi du message WhatsApp...");

            const response = await fetch(`${this.baseUrl}/send`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": this.csrfToken,
                    "Accept": "application/json"
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
                this.showNotification("✅ Message WhatsApp envoyé avec succès !", "success");
            } else {
                this.showNotification(`❌ Erreur: ${result.message}`, "error");
            }

            return result;

        } catch (error) {
            this.hideLoader();
            console.error("Erreur lors de l'envoi WhatsApp:", error);
            this.showNotification("❌ Erreur technique lors de l'envoi du message", "error");
            return { success: false, message: error.message };
        }
    },

    // Affiche un indicateur de chargement
    showLoader(message = "Chargement...") {
        this.hideLoader();
        const loader = document.createElement("div");
        loader.id = "whatsapp-loader";
        loader.className = "fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50";
        loader.innerHTML = `
            <div class="bg-white rounded-lg p-6 flex items-center space-x-3 shadow-xl">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                <span class="text-gray-700 font-medium">${message}</span>
            </div>
        `;
        document.body.appendChild(loader);
    },

    // Cache l'indicateur de chargement
    hideLoader() {
        const loader = document.getElementById("whatsapp-loader");
        if (loader) {
            loader.remove();
        }
    },

    // Affiche une notification toast
    showNotification(message, type = "info") {
        const notification = document.createElement("div");
        notification.className = `fixed top-4 right-4 max-w-sm p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;

        const colors = {
            success: "bg-green-500 text-white",
            error: "bg-red-500 text-white",
            warning: "bg-yellow-500 text-white",
            info: "bg-blue-500 text-white"
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
            notification.classList.remove("translate-x-full");
        }, 100);

        // Auto-suppression après 5 secondes
        setTimeout(() => {
            notification.classList.add("translate-x-full");
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }
};

// Fonction globale pour envoyer un message WhatsApp
window.sendWhatsAppMessage = function(candidateId) {
    console.log("🟢 Bouton WhatsApp cliqué pour candidat:", candidateId);
    if (window.WhatsAppService) {
        window.WhatsAppService.sendMessage(candidateId, "notification");
    } else {
        console.error("WhatsAppService not available");
        alert("Service WhatsApp non disponible");
    }
};

// Fonction pour supprimer un candidat
window.deleteCandidate = async function(candidateId) {
    try {
        console.log("🗑️ Suppression du candidat:", candidateId);

        if (window.WhatsAppService) {
            window.WhatsAppService.showLoader("Suppression du candidat...");
        }

        const response = await fetch(`/admin/candidates/${candidateId}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]")?.getAttribute("content"),
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        });

        if (window.WhatsAppService) {
            window.WhatsAppService.hideLoader();
        }

        if (response.ok) {
            if (window.WhatsAppService) {
                window.WhatsAppService.showNotification("✅ Candidat supprimé avec succès !", "success");
            }

            setTimeout(() => {
                window.location.reload();
            }, 1500);

            return true;
        } else {
            throw new Error("Erreur lors de la suppression");
        }

    } catch (error) {
        if (window.WhatsAppService) {
            window.WhatsAppService.hideLoader();
            window.WhatsAppService.showNotification("❌ Erreur lors de la suppression du candidat", "error");
        } else {
            alert("Erreur lors de la suppression du candidat");
        }
        console.error("Erreur suppression candidat:", error);
        return false;
    }
};

console.log("🟢 WhatsApp Service avec Green API initialisé");
</script>
