FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql fileinfo \
    && a2enmod rewrite headers

COPY public/ /var/www/html/
COPY config/ /var/www/config/

RUN chown -R www-data:www-data /var/www/html \
    && mkdir -p /var/www/html/uploads/associados \
    && chown -R www-data:www-data /var/www/html/uploads

ENV APACHE_DOCUMENT_ROOT=/var/www/html

RUN sed -ri -e 's!/var/www/html!/var/www/html!g' /etc/apache2/sites-available/000-default.conf /etc/apache2/apache2.conf

EXPOSE 80
CMD ["apache2-foreground"]
