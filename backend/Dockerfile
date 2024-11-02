FROM php:8.1-apache

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN apt-get update \
    && apt-get install -qq -y --no-install-recommends \
    cron \
    vim \
    locales coreutils apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev;

RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer
RUN curl -sS https://getcomposer.org/installer | php -- --install -dir=/usr/local/bin --filename=composer
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql mysqli gd opcache intl zip calendar dom mbstring zip gd xsl && a2enmod rewrite
RUN pecl install apcu xdebug && docker-php-ext-enable apcu xdebug

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions amqp

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN set -eux; \
    mkdir -p var/cache var/log; \
    if [ -f composer.json ]; then \
        composer install; \
        composer dump-autoload; \
        composer dump-env dev; \
        composer run-script post-install-cmd; \
        chmod +x bin/console; sync; \
    fi


# Set the working directory
WORKDIR /var/www/html

# delete cache and logs
RUN rm -rf var/cache/* var/log/*

# Copy the code
COPY . /var/www/html


# Set the appropriate permissions
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/public /var/www/html/config /var/www/html/templates /var/www/html/src /var/www/html/bin

# Make sure directories are writable
RUN chmod -R 777 /var/www/html/


# Copy Xdebug configuration
COPY php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Expose Xdebug port
EXPOSE 9003

CMD ["sh", "-c", "XDEBUG_MODE=coverage apache2-foreground"]
