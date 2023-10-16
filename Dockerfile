# syntax=docker/dockerfile:experimental
FROM php:8.2-apache-bookworm as base

# Update packages
RUN DEBIAN_FRONTEND=noninteractive apt-get -y update --fix-missing && \
    apt-get upgrade -y

# Install tools & libraries
RUN apt-get -y install --fix-missing apt-utils dpkg build-essential \
    vim bash bash-completion wget dialog git curl zip cron supervisor \
    libcurl4 libcurl4-openssl-dev libzip-dev libmagickwand-dev libmagickwand-6.q16-6 \
    libmcrypt-dev libsqlite3-dev libsqlite3-0 mariadb-client zlib1g-dev \
    libicu-dev libonig-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    libxslt1-dev libxslt1.1 iproute2 libzstd1 libzstd-dev

RUN pecl install imagick && docker-php-ext-enable imagick && \
    pecl install redis && docker-php-ext-enable redis && \      
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install -j$(nproc) intl && \
    docker-php-ext-install gettext && \
    docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && \
    docker-php-ext-install -j$(nproc) gd && \
    docker-php-ext-install zip && \
    docker-php-ext-install exif && \
    docker-php-ext-install sockets && \
    docker-php-ext-install bcmath && \
    docker-php-ext-install xsl && \
    docker-php-ext-install opcache && \
    docker-php-source delete
    
RUN apt-get -y remove build-essential && apt-get autoremove && apt-get clean

ENV TZ=Etc/UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Setup scheduler (cron)
ADD docker/cron/laravel /etc/cron.d/laravel
RUN chmod 0644 /etc/cron.d/laravel

# Setup queue workers (supervisor)
COPY docker/supervisor/supervisord.conf /etc/supervisor

# Enable apache modules
RUN a2enmod rewrite headers brotli deflate && \
    sed -i 's/ServerTokens OS/ServerTokens Prod/g' /etc/apache2/conf-available/security.conf && \
    sed -i 's/ServerSignature On/ServerSignature Off/g' /etc/apache2/conf-available/security.conf

COPY docker/scripts/start /start
COPY docker/scripts/developermode /developermode
COPY docker/scripts/runmigrations /runmigrations

RUN chmod +x /start && \
    chmod +x /developermode && \
    chmod +x /runmigrations

CMD /start
ENTRYPOINT /start

COPY ./docker/config/php/* /usr/local/etc/php/conf.d/
RUN rm /etc/apache2/sites-enabled/000-default.conf /etc/apache2/sites-available/000-default.conf /etc/apache2/mods-enabled/mpm_prefork.conf

COPY ./docker/config/apache/default.conf /etc/apache2/sites-enabled/default.conf
COPY ./docker/config/apache/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

### PROD
FROM base as prod

COPY . /var/www/html
COPY ./docker/files/robots.txt /var/www/html/public/robots.txt
WORKDIR /var/www/html

RUN php composer.phar install -n; \
    php composer.phar clear-cache

RUN php artisan storage:link && \
    mkdir -p /var/www/html/storage/logs && \
    mkdir -p /var/www/html/storage/framework/{sessions,views,cache,testing} && \
    mkdir -p /var/www/html/storage/framework/cache/data && \
    chmod -R 777 /var/www/html/storage/framework/ && \
    chmod -R ug+rwx storage bootstrap/cache && \
    php artisan config:clear && \
    php artisan view:clear && \
    chmod -R +x /var/www/html/scripts && \
    chown -R www-data:www-data /var/www/html
