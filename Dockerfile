FROM php:8.3-apache
RUN docker-php-ext-install mysqli
RUN a2enmod rewrite
COPY ./src/public /var/www/html/
COPY ./src/private /var/www/private/
WORKDIR /var/www/html/
EXPOSE 80
