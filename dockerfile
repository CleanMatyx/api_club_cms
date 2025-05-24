# Set master image
FROM php:8.2-fpm-alpine

# Copy composer.lock and composer.json
#COPY composer.lock composer.json /srv/

# Set working directory
WORKDIR /srv

# Install Additional dependencies
RUN apk update && apk add --no-cache \
build-base shadow icu-dev gettext gettext-dev vim curl \
    php82 \
    php82-fpm \
    php82-common \
    php82-pdo \
    php82-pdo_mysql \
    php82-mysqli \
    php82-mbstring \
    php82-xml \
    php82-openssl \
    php82-json \
    php82-phar \
    php82-zip \
    php82-gd \
    php82-dom \
    php82-session \
    php82-zlib \
    nodejs npm git zlib libpng-dev libxml2-dev libzip-dev

    
# SOAP extension
RUN docker-php-ext-install soap && docker-php-ext-enable soap

    
# Add and Enable PHP-PDO Extenstions
RUN docker-php-ext-configure intl
RUN docker-php-ext-configure gettext
RUN docker-php-ext-install pdo pdo_mysql gd intl gettext zip mysqli
RUN docker-php-ext-enable pdo_mysql


# Install PHP Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Remove Cache
RUN rm -rf /var/cache/apk/*

# Add UID '1000' to www-data
RUN usermod -u 1000 www-data

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /srv

# Copiar composer.json y composer.lock primero para aprovechar el cache de Docker
COPY club_cms/composer.json club_cms/composer.lock /srv/

# Copiar el resto de la aplicación
COPY club_cms/ /srv/

# Permisos para Laravel
RUN chown -R www-data:www-data /srv/storage /srv/bootstrap/cache

# Instalar dependencias de Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
