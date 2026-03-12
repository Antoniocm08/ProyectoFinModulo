# Imagen base: PHP 8.2 con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias para conectar con MariaDB
RUN docker-php-ext-install pdo pdo_mysql

# Copiar todos los archivos de la aplicación al servidor web
COPY . /var/www/html/

# Dar permisos correctos a los archivos
RUN chown -R www-data:www-data /var/www/html

# Puerto que expone el contenedor
EXPOSE 80
