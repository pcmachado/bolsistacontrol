FROM php:8.2.13-fpm

# Arguments
ARG APP_DIR=/var/www
ARG ENABLE_XDEBUG=false
ARG XDEBUG_CLIENT_HOST=127.0.0.1
ENV XDEBUG_CLIENT_HOST=${XDEBUG_CLIENT_HOST}

# Defina o fuso horário do sistema para Brasília (-3) e configurações de PHP-FPM
ENV ENABLE_XDEBUG=${ENABLE_XDEBUG} \
    PHP_PM_MAX_CHILDREN=20 \
    PHP_PM_START_SERVERS=2 \
    PHP_PM_MIN_SPARE_SERVERS=1 \
    PHP_PM_MAX_SPARE_SERVERS=3 \
    DEBIAN_FRONTEND=noninteractive

# Set working directory
WORKDIR $APP_DIR

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    unixodbc \
    gnupg2 \
    unixodbc-dev \
    libpq-dev \
    libzip-dev \
    libldap2-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions, including LDAP and XML
RUN docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets pdo_pgsql zip ldap xml \
    && ln -sf /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime && echo "America/Sao_Paulo" > /etc/timezone \
    && { \
    echo '[www]'; \
    echo 'pm.max_children = ${PHP_PM_MAX_CHILDREN}'; \
    echo 'pm.start_servers = ${PHP_PM_START_SERVERS}'; \
    echo 'pm.min_spare_servers = ${PHP_PM_MIN_SPARE_SERVERS}'; \
    echo 'pm.max_spare_servers = ${PHP_PM_MAX_SPARE_SERVERS}'; \
    } > /usr/local/etc/php-fpm.d/zz-pm.conf

# Install redis
RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

# Configure Xdebug
RUN if [ "$ENABLE_XDEBUG" = "true" ]; then \
        pecl install xdebug && \
        docker-php-ext-enable xdebug && \
        touch /var/www/xdebug.log && chown www-data:www-data /var/www/xdebug.log && chmod 775 /var/www/xdebug.log; \
    fi

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code
COPY --chown=www-data:www-data ./ ./

ARG UID=1000
ARG GID=1000
RUN addgroup --gid ${GID} usergroup \
    && adduser --uid ${UID} --gid ${GID} --shell /bin/bash --disabled-password --gecos "" user \
    && chown -R user:usergroup /var/www
USER user

# RUN composer install --no-interaction --no-progress --prefer-dist --optimize-autoloader --no-dev

# Copy custom PHP configurations
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini
