FROM php:8.4-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git zip unzip libicu-dev libpq-dev libonig-dev libzip-dev \
    libxml2-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql intl zip opcache \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Installer Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Télécharger et installer la CLI Symfony
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Définir le dossier de travail
WORKDIR /var/www/html

# Ensuite on copie le reste (code source)
COPY . .

# Copier uniquement les fichiers nécessaires pour composer
COPY composer.json composer.lock ./
RUN composer install