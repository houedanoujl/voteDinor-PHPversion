<x-mail::message>
# Félicitations ! Votre candidature est approuvée

Bonjour {{ $candidate->prenom }},

Nous avons le plaisir de vous informer que votre candidature au **Concours Photo Rétro DINOR** a été approuvée !

Votre photo participe désormais officiellement au concours et les visiteurs peuvent voter pour vous.

## Détails de votre participation

**Nom :** {{ $candidate->full_name }}  
**Date de validation :** {{ now()->format('d/m/Y à H:i') }}  
**Statut :** ✅ Candidature approuvée

## Prochaines étapes

- Votre photo est maintenant visible sur le site du concours
- Les utilisateurs peuvent voter pour votre photo (1 vote par personne par jour)
- Vous pouvez partager votre participation sur les réseaux sociaux
- Suivez le classement en temps réel sur notre site

<x-mail::button :url="$contestUrl">
Voir le concours
</x-mail::button>

## Règlement du concours

- Le vote est ouvert à tous
- 1 vote par candidat par jour maximum
- Les votes sont comptabilisés en temps réel
- Le concours se termine le [DATE DE FIN]

Nous vous souhaitons bonne chance pour le concours !

Cordialement,  
L'équipe DINOR

<x-mail::subcopy>
Partagez votre participation : "Je participe au Concours Photo Rétro DINOR ! Votez pour moi sur {{ $contestUrl }}"
</x-mail::subcopy>
</x-mail::message>