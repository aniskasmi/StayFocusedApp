FROM php:8.1-fpm

ENV PHP_SECURITY_CHECHER_VERSION=1.0.0

RUN apt-get update && apt-get install -y \
      wget \
      git \
      fish

RUN apt-get update && apt-get install -y libzip-dev libicu-dev && docker-php-ext-install pdo zip intl opcache

#Correct time zone
RUN ln -snf /usr/share/zoneinfo/Europe/Paris /etc/localtime && echo Europe/Paris > /etc/timezone

# Support de apcu
RUN pecl install apcu && docker-php-ext-enable apcu

# Support de redis
RUN pecl install redis && docker-php-ext-enable redis

# Support de MySQL (pour la migration)
RUN docker-php-ext-install mysqli pdo_mysql

# Installation de GD
RUN apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Imagick
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        tesseract-ocr \
        libmagickwand-dev \
        libmagickcore-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libpq-dev \
        ghostscript

RUN pecl install imagick && \
    docker-php-ext-enable imagick

# Copier le fichier security.xml dans l'image et le configurer pour ImageMagick
COPY security.xml /etc/ImageMagick-6/security.xml
RUN sed -i 's/rights="none"/rights="read | write"/g' /etc/ImageMagick-6/policy.xml

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Symfony tool
#RUN wget https://get.symfony.com/cli/installer -O - | bash && \
#  mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# Security checker tool
RUN curl -L https://github.com/fabpot/local-php-security-checker/releases/download/v${PHP_SECURITY_CHECHER_VERSION}/local-php-security-checker_${PHP_SECURITY_CHECHER_VERSION}_linux_$(dpkg --print-architecture) --output /usr/local/bin/local-php-security-checker && \
  chmod +x /usr/local/bin/local-php-security-checker

# Pour la récupération des durées
RUN apt-get update && apt-get install -y ffmpeg

# Xdebug (disabled by default, but installed if required)
# RUN pecl install xdebug-2.9.7 && docker-php-ext-enable xdebug
ADD php.ini /usr/local/etc/php/conf.d/

WORKDIR /var/www

EXPOSE 9000
