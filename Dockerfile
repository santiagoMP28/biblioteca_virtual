# Imagen base con Apache y PHP 8.2
FROM php:8.2-apache

# Instala la extensión de mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copiar TODO el contenido del proyecto al contenedor
COPY . /var/www

# Cambiar la carpeta pública (DocumentRoot) a vistas/html
RUN rm -rf /var/www/html && ln -s /var/www/vistas/html /var/www/html

# Permisos adecuados
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Habilita mod_rewrite (si necesitas .htaccess en el futuro)
RUN a2enmod rewrite

# Cambia DocumentRoot en Apache
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/vistas/html|' /etc/apache2/sites-available/000-default.conf

# Expone el puerto 80
EXPOSE 80
