# Imagen base con Apache y PHP
FROM php:8.2-apache

# Instalar extensión mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copiar todo el proyecto al contenedor
COPY . /var/www

# Enlace simbólico de public a html (Apache sirve desde /var/www/html)
RUN rm -rf /var/www/html && ln -s /var/www/public /var/www/html

# Permisos apropiados
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Habilita mod_rewrite si necesitas .htaccess
RUN a2enmod rewrite

# Cambiar DocumentRoot explícitamente a public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/public|' /etc/apache2/sites-available/000-default.conf

EXPOSE 80
