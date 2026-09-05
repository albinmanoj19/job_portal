FROM php:8.4-apache

RUN docker-php-ext-install mysqli

RUN a2enmod rewrite

COPY . /var/www/html/

EXPOSE 8080

CMD ["apache2-foreground"]