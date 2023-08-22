FROM php:8.1-apache

RUN apt-get update

RUN apt update && apt install -y nodejs npm libpng-dev zlib1g-dev libxml2-dev libzip-dev libonig-dev zip curl unzip && docker-php-ext-configure gd \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && pecl install redis \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable redis \
    && docker-php-source delete

RUN docker-php-ext-configure pcntl --enable-pcntl && docker-php-ext-install pcntl

RUN apt-get install -y libwebp-dev libjpeg62-turbo-dev libpng-dev libxpm-dev libfreetype6-dev

RUN docker-php-ext-configure gd  --with-jpeg  --with-freetype
RUN docker-php-ext-install gd

RUN groupadd -r app -g 5500 && useradd -u 5500 -r -g app -m -d /app -s /sbin/nologin -c "App user" app && chmod 755 /var/www/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install pdo pdo_mysql sockets

WORKDIR /var/www/

USER root

COPY --chown=www-data:www-data . /var/www/

RUN cp -rf /var/www/docker/default.conf /etc/apache2/sites-enabled/000-default.conf

RUN chown -R www-data:www-data /var/www

RUN a2enmod rewrite
RUN service apache2 restart

RUN php artisan storage:link

CMD apache2-foreground





