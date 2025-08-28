<div class="p-4 border border-gray-300 rounded">
    <h3 class="font-bold mb-2">Test Livewire Component</h3>
    <p class="text-sm mb-2">Status: {{ $showTest ? 'VISIBLE' : 'HIDDEN' }}</p>
    <button 
        wire:click="toggleTest" 
        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
    >
        Toggle Test ({{ $showTest ? 'Hide' : 'Show' }})
    </button>
    
    @if($showTest)
        <div class="mt-4 p-4 bg-green-100 border border-green-300 rounded">
            <p class="text-green-800">✅ Livewire fonctionne correctement!</p>
        </div>
    @endif
</div>