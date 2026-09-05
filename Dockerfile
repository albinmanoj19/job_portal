FROM php:8.4-apache

# Enable PHP MySQL support
RUN docker-php-ext-install mysqli

# Make sure only Apache prefork MPM is enabled
RUN a2dismod mpm_event mpm_worker mpm_prefork || true \
    && a2enmod mpm_prefork

# Enable .htaccess / rewrite support
RUN a2enmod rewrite

# Copy the website
COPY . /var/www/html/

# Railway uses port 8080
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/:80>/:8080>/' /etc/apache2/sites-available/000-default.conf

EXPOSE 8080

CMD ["apache2-foreground"]