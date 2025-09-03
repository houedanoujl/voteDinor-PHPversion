<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Système d'inscription, connexion & notifications WhatsApp (DINOR)

Ce projet inclut un système complet d'inscription/connexion et de notifications WhatsApp pour informer l'admin et les candidats.

- **Flux candidat**:
  - Inscription via formulaire (photo requise si les uploads sont activés).
  - Création de l'utilisateur et du candidat; prévention des doublons.
  - Message WhatsApp de confirmation envoyé au candidat.
  - Notification WhatsApp envoyée à l'admin avec les liens utiles (utilisateur, candidat).

- **Notifications admin**:
  - Configurable via `ADMIN_WHATSAPP` (numéro au format international, ex: `+2250748348221`).
  - Le provider WhatsApp est sélectionné via `WHATSAPP_PROVIDER` (`green_api` ou `business_api`).
  - Un mécanisme de fallback tente l'autre provider si le provider courant échoue (et s'il est configuré).

- **Normalisation des numéros**:
  - Entrée utilisateur: attendue au format `+225XXXXXXXXXX`.
  - Provider Green API: envoi aux 8 derniers chiffres, préfixés par `225` (sans `+`) comme requis par l'API. L'application s'occupe de ce format automatiquement.
  - Provider WhatsApp Business API: envoi au format E.164 (ex: `2250748348221`).

### Configuration (.env)

Exemple minimal avec Green API:

```env
WHATSAPP_PROVIDER=green_api
ADMIN_WHATSAPP=+2250748348221
GREEN_API_ID=your_instance_id
GREEN_API_TOKEN=your_token
```

Exemple avec WhatsApp Business API (Meta):

```env
WHATSAPP_PROVIDER=business_api
ADMIN_WHATSAPP=+2250748348221
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_ACCESS_TOKEN=your_access_token
WHATSAPP_API_URL=https://graph.facebook.com/v18.0
```

Après modification du `.env`, vider les caches dans Docker:

```bash
docker exec -it dinor_app php artisan config:clear && \
docker exec -it dinor_app php artisan cache:clear
```

### Test rapide d'envoi WhatsApp admin

Une commande Artisan est fournie pour diagnostiquer l'envoi vers l'admin:

```bash
docker exec -it dinor_app php artisan test:whatsapp-admin
```

Cette commande affiche:
- le numéro admin chargé, le provider courant
- l'état de configuration des providers (Green/Business)
- le résultat d'envoi (succès/erreur, code de statut et corps de réponse)

### Journalisation

Lors d'une inscription candidat, des logs détaillés sont écrits:
- début et fin de notification admin
- numéro admin chargé
- résultat de l'appel WhatsApp

Consulter les logs:

```bash
docker exec -it dinor_app sh -lc "tail -n 200 storage/logs/laravel.log | cat"
```

### Important (numéro expéditeur)

Le numéro « expéditeur » des messages WhatsApp dépend du provider et de l'instance configurée (ex: instance Green API connectée à `+22543348221`). Assurez-vous que l'instance/provider est relié au numéro souhaité côté fournisseur; l'application ne modifie pas le numéro expéditeur.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
