FROM php:8.4-apache

# Install MySQLi
RUN docker-php-ext-install mysqli

# Make sure only ONE Apache MPM is enabled
RUN a2dismod mpm_event mpm_worker mpm_prefork || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

# Copy the website
COPY . /var/www/html/

# Railway uses the PORT environment variable
RUN sed -i 's/Listen 80/Listen ${PORT:-8080}/' /etc/apache2/ports.conf \
    && sed -i 's/:80>/:${PORT:-8080}>/g' /etc/apache2/sites-available/000-default.conf

EXPOSE 8080

CMD ["apache2-foreground"]