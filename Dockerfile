# Imagen base con Apache y PHP 8.2
FROM php:8.2-apache

# Instala la extensión mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copia todo el contenido al contenedor
COPY . /var/www

# Establece permisos adecuados
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Establecer vistas/html como carpeta pública
RUN rm -rf /var/www/html && ln -s /var/www/public/html /var/www/html

# Habilita mod_rewrite por si usas .htaccess
RUN a2enmod rewrite

# Cambia el DocumentRoot en la configuración de Apache
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/public/html|' /etc/apache2/sites-available/000-default.conf

# Expone el puerto por defecto de Apache
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]
