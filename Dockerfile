FROM php:8.3-fpm

WORKDIR /var/www

# Install PHP extension installer
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install all system deps + Node 20 in a single layer to minimize image size
RUN apt-get update && apt-get install -y \
        build-essential \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libicu-dev \
        libonig-dev \
        libmemcached-dev \
        lua-zlib-dev \
        locales \
        zip \
        unzip \
        git \
        curl \
        jpegoptim optipng pngquant gifsicle \
        nginx \
        supervisor \
        nano \
        lsof net-tools \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions mbstring pdo_mysql zip exif pcntl gd memcached sockets bcmath intl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create app user
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# Copy application source
COPY --chown=www:www-data . /var/www

# Install PHP dependencies (no dev, no lock file to always resolve fresh)
RUN git config --global --add safe.directory /var/www \
    && rm -f composer.lock && composer install --optimize-autoloader --no-dev

# Install Node dependencies and build frontend assets
RUN npm ci && npm run build && npm cache clean --force

# Copy server configs
RUN cp docker/supervisor.conf /etc/supervisord.conf \
    && cp docker/php.ini /usr/local/etc/php/conf.d/app.ini \
    && cp docker/nginx.conf /etc/nginx/sites-enabled/default

# Set up storage and logs
RUN chmod -R 777 /var/www/storage \
    && touch /var/www/storage/logs/laravel.log \
    && chown www:www-data /var/www/storage/logs/laravel.log \
    && chmod 777 /var/www/storage/logs/laravel.log \
    && chmod 777 /var/www/bootstrap/cache

# PHP error log
RUN mkdir /var/log/php && touch /var/log/php/errors.log && chmod 777 /var/log/php/errors.log

# Create public storage symlink
RUN php artisan storage:link

RUN chmod +x /var/www/docker/run.sh

EXPOSE 80
ENTRYPOINT ["/var/www/docker/run.sh"]
