# Imagen base con Apache y PHP
FROM php:8.2-apache

# Instalar extensiones para PostgreSQL Y MySQL (por si acaso)
RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql pdo_mysql mysqli && \
    docker-php-ext-enable pdo_pgsql

# Copiar todo el proyecto al contenedor
COPY . /var/www

# Asegúrate que tienes estos permisos:
    RUN mkdir -p /tmp/archivos && \
    chown -R www-data:www-data /tmp/archivos && \
    chmod -R 755 /tmp/archivos

# Configuración de Apache
RUN rm -rf /var/www/html && \
    ln -s /var/www/public /var/www/html && \
    chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www && \
    a2enmod rewrite && \
    sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/public|' /etc/apache2/sites-available/000-default.conf

EXPOSE 80