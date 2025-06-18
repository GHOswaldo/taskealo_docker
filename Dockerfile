# Usa la imagen base de PHP con Apache
FROM php:8.3-apache

# Instala las dependencias necesarias para mysqli
RUN apt-get update && apt-get install -y libmariadb-dev && \
    docker-php-ext-install mysqli && \
    docker-php-ext-enable mysqli

# Habilita el mod_rewrite de Apache
RUN a2enmod rewrite
