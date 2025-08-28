FROM php:8.3-fpm

# Arguments de build
ARG user=dinor
ARG uid=1000

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libicu-dev \
    zip \
    unzip \
    supervisor \
    cron \
    && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP nécessaires
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Installation de Redis PHP extension
RUN pecl install redis && docker-php-ext-enable redis

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Création de l'utilisateur système
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Configuration du répertoire de travail
WORKDIR /var/www

# Copie des fichiers de configuration d'abord (pour le cache Docker)
COPY --chown=$user:$user composer.json composer.lock ./
COPY --chown=$user:$user package.json package-lock.json* ./

# Installation des dépendances PHP
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copie de tous les fichiers de l'application
COPY --chown=$user:$user . /var/www

# Configuration des permissions
RUN chown -R $user:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Création du lien symbolique pour le storage
RUN php artisan storage:link || true

# Configuration Supervisor pour les queues
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Script d'initialisation
COPY docker/scripts/init.sh /usr/local/bin/init.sh
RUN chmod +x /usr/local/bin/init.sh

# Utilisation de l'utilisateur créé
USER $user

# Exposition du port
EXPOSE 9000

# Commande d'initialisation puis PHP-FPM
CMD ["/usr/local/bin/init.sh"]