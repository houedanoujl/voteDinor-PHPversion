<div class="space-y-4 p-4 bg-gray-50 rounded-lg border">
    <h4 class="font-semibold text-gray-900 mb-3">Configuration WhatsApp</h4>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- WhatsApp Business API -->
        <div class="bg-white p-3 rounded border">
            <div class="flex items-center justify-between mb-2">
                <h5 class="font-medium text-gray-800">WhatsApp Business API</h5>
                <span class="text-lg">{{ $businessApiStatus }}</span>
            </div>
            
            <div class="space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Phone Number ID:</span>
                    <span class="font-mono text-xs">
                        {{ $businessApiConfig['phone_number_id'] ? 'Configuré' : 'Non configuré' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Access Token:</span>
                    <span class="font-mono text-xs">
                        {{ $businessApiConfig['access_token'] ? 'Configuré' : 'Non configuré' }}
                    </span>
                </div>
            </div>
            
            @if(!$businessApiConfig['phone_number_id'] || !$businessApiConfig['access_token'])
                <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-800">
                    <strong>Variables d'environnement requises:</strong><br>
                    WHATSAPP_PHONE_NUMBER_ID<br>
                    WHATSAPP_ACCESS_TOKEN
                </div>
            @endif
        </div>
        
        <!-- Green API -->
        <div class="bg-white p-3 rounded border">
            <div class="flex items-center justify-between mb-2">
                <h5 class="font-medium text-gray-800">Green API</h5>
                <span class="text-lg">{{ $greenApiStatus }}</span>
            </div>
            
            <div class="space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Instance ID:</span>
                    <span class="font-mono text-xs">
                        {{ $greenApiConfig['instance_id'] ? 'Configuré' : 'Non configuré' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Token:</span>
                    <span class="font-mono text-xs">
                        {{ $greenApiConfig['token'] ? 'Configuré' : 'Non configuré' }}
                    </span>
                </div>
            </div>
            
            @if(!$greenApiConfig['instance_id'] || !$greenApiConfig['token'])
                <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-800">
                    <strong>Variables d'environnement requises:</strong><br>
                    GREEN_API_ID<br>
                    GREEN_API_TOKEN
                </div>
            @endif
        </div>
    </div>
    
    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded text-sm text-blue-800">
        <strong>💡 Instructions:</strong><br>
        • Configurez au moins un provider pour tester les envois WhatsApp<br>
        • WhatsApp Business API nécessite un compte Business vérifié<br>
        • Green API est une alternative plus simple à configurer
    </div>
</div>
