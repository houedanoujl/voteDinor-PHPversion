<x-filament-panels::page>
    {{ $this->table }}
    
    <!-- Modal WhatsApp -->
    <div id="whatsapp-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Envoyer un message WhatsApp</h3>
                <button onclick="closeWhatsAppModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="candidate-info" class="mb-4 p-3 bg-gray-100 rounded">
                <!-- Info candidat sera remplie par JavaScript -->
            </div>
            
            <form id="whatsapp-form">
                @csrf
                <input type="hidden" id="candidate_id" name="candidate_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea 
                        id="message" 
                        name="message" 
                        rows="4" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Tapez votre message ici..."
                        required
                    ></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeWhatsAppModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        Annuler
                    </button>
                    <button type="submit" id="send-btn" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                        Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentCandidate = null;
        
        function sendWhatsAppMessage(candidateId, prenom, nom, whatsapp) {
            currentCandidate = { id: candidateId, prenom: prenom, nom: nom, whatsapp: whatsapp };
            
            // Remplir les infos du candidat
            document.getElementById('candidate-info').innerHTML = 
                `<strong>${prenom} ${nom}</strong><br><span class="text-sm text-gray-600">${whatsapp}</span>`;
            
            // Pré-remplir le message
            document.getElementById('message').value = 
                `Bonjour ${prenom},\n\nCeci est un message de l'équipe DINOR.\n\nCordialement,\nL'équipe DINOR`;
            
            // Remplir l'ID candidat
            document.getElementById('candidate_id').value = candidateId;
            
            // Afficher le modal
            document.getElementById('whatsapp-modal').classList.remove('hidden');
        }
        
        function closeWhatsAppModal() {
            document.getElementById('whatsapp-modal').classList.add('hidden');
            document.getElementById('whatsapp-form').reset();
        }
        
        // Gérer la soumission du formulaire
        document.getElementById('whatsapp-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const sendBtn = document.getElementById('send-btn');
            const originalText = sendBtn.textContent;
            
            sendBtn.textContent = 'Envoi...';
            sendBtn.disabled = true;
            
            try {
                const formData = new FormData(this);
                
                const response = await fetch('{{ route('admin.whatsapp.send') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Afficher notification de succès
                    showNotification(result.message, 'success');
                    closeWhatsAppModal();
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                showNotification('Erreur de connexion', 'error');
            } finally {
                sendBtn.textContent = originalText;
                sendBtn.disabled = false;
            }
        });
        
        function showNotification(message, type) {
            // Créer une notification simple
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg max-w-sm ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
        
        // Fermer le modal en cliquant à l'extérieur
        document.getElementById('whatsapp-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeWhatsAppModal();
            }
        });
    </script>
</x-filament-panels::page>