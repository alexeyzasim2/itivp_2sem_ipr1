FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    && docker-php-ext-install mysqli pdo pdo_mysql

WORKDIR /var/www/html

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN echo 'DirectoryIndex form.html' >> /etc/apache2/apache2.conf

EXPOSE 80

CMD ["apache2-foreground"]
