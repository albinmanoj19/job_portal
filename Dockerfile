FROM php:8.4-apache

# Install MySQLi
RUN docker-php-ext-install mysqli

# Disable all possible MPMs, then enable exactly one
RUN a2dismod mpm_event mpm_worker mpm_prefork || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

# Make Apache listen on Railway's port
RUN sed -i 's/^Listen 80$/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:8080>/' /etc/apache2/sites-available/000-default.conf

COPY . /var/www/html/

EXPOSE 8080

CMD ["apache2-foreground"]