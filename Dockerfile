FROM php:8.1-apache
RUN docker-php-ext-install pdo pdo_mysql
COPY src/ /var/www/html/
EXPOSE 80

RUN sed -i 's|DocumentRoot /var/www/html/public|DocumentRoot /var/www/html|g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite