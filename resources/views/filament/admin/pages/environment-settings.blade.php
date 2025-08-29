<x-filament-panels::page>
    @php
        $envVars = $this->getEnvironmentVariables();
    @endphp

    <div class="space-y-6">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Variables d'environnement
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Cette page affiche les variables d'environnement actuelles. Les valeurs sensibles sont masquées par des astérisques. Pour modifier ces valeurs, éditez directement le fichier <code>.env</code> à la racine du projet.</p>
                    </div>
                </div>
            </div>
        </div>

        @foreach($envVars as $category => $variables)
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $category }}</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Configuration pour {{ strtolower($category) }}</p>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        @foreach($variables as $key => $value)
                            <div class="@if(!$loop->last) border-b border-gray-200 @endif bg-{{ $loop->even ? 'gray-50' : 'white' }} px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">{{ $key }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <code class="bg-gray-100 rounded px-2 py-1">{{ $value ?: 'Non défini' }}</code>
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>
        @endforeach

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Comment modifier ces variables
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Pour modifier ces variables d'environnement :</p>
                        <ol class="mt-2 ml-4 list-decimal space-y-1">
                            <li>Accédez au fichier <code>.env</code> dans le répertoire racine du projet</li>
                            <li>Modifiez les valeurs souhaitées</li>
                            <li>Sauvegardez le fichier</li>
                            <li>Redémarrez l'application pour que les changements prennent effet</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>