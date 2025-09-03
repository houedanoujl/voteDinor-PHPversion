@extends('layouts.app')

@section('title', 'Réinitialiser par WhatsApp')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg border border-gray-200 p-8">
        <h1 class="text-2xl font-bold mb-6">Réinitialiser le mot de passe via WhatsApp</h1>

        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-50 text-red-700 border border-red-200">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="mb-4 p-3 rounded bg-green-50 text-green-700 border border-green-200">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.whatsapp.send') }}" class="space-y-4">
            @csrf

            <label class="block text-sm font-medium text-gray-700">Email ou numéro WhatsApp</label>
            <input type="text" name="identifier" value="{{ old('identifier') }}"
                   class="w-full rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500" placeholder="ex: jean@exemple.com ou 07 12 34 56 78" required>

            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2.5 rounded-lg">Envoyer le nouveau mot de passe</button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-800">Retour à la connexion</a>
        </div>
    </div>

</div>
@endsection


