FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    vim \
    openssh-server \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    zip unzip \
    sudo && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd mbstring pdo pdo_mysql zip && \
    echo "www-data ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers && \
    echo "root:rootpassword" | chpasswd && \
    mkdir /var/run/sshd && \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 22

COPY ../../ /var/www/html

WORKDIR /var/www/html
RUN composer install

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

CMD service ssh start && apache2-foreground
