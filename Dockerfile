FROM php:8.4-apache

# Install MySQLi
RUN docker-php-ext-install mysqli

# Enable URL rewriting
RUN a2enmod rewrite

# Remove any conflicting MPM configuration
RUN a2dismod mpm_event mpm_worker mpm_prefork || true
RUN a2enmod mpm_prefork

# Copy the website
COPY . /var/www/html/

# Railway provides the PORT environment variable.
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf
RUN sed -i 's/<VirtualHost \\*:80>/<VirtualHost *:8080>/' /etc/apache2/sites-available/000-default.conf

EXPOSE 8080

CMD ["apache2-foreground"]