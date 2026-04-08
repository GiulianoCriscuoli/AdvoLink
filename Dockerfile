FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    libonig-dev \
    git \
    unzip \
    default-mysql-server \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    pdo_mysql \
    mysqli \
    intl \
    xml \
    zip \
    gd \
    opcache \
    mbstring

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


RUN a2enmod rewrite && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf


RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

COPY . /var/www/html

WORKDIR /var/www/html

# Script para iniciar serviços
COPY start-services.sh /usr/local/bin/start-services.sh
RUN chmod +x /usr/local/bin/start-services.sh

EXPOSE 80 3306

CMD ["/usr/local/bin/start-services.sh"]


