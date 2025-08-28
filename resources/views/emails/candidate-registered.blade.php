<x-mail::message>
# Nouvelle inscription candidat

Un nouveau candidat s'est inscrit au Concours Photo DINOR :

**Candidat :** {{ $candidate->full_name }}  
**Email :** {{ $candidate->email ?? 'Non fourni' }}  
**WhatsApp :** {{ $candidate->whatsapp }}  
**Date d'inscription :** {{ $candidate->created_at->format('d/m/Y à H:i') }}

@if($candidate->description)
**Description :** {{ $candidate->description }}
@endif

@if($candidate->photo_url)
**Photo :** [Voir la photo]({{ $candidate->photo_url }})
@endif

<x-mail::button :url="$approveUrl">
Examiner la candidature
</x-mail::button>

Connectez-vous à l'interface d'administration pour approuver ou rejeter cette candidature.

Cordialement,  
L'équipe DINOR

<x-mail::subcopy>
Cette notification est envoyée automatiquement lors de chaque nouvelle inscription.
</x-mail::subcopy>
</x-mail::message>